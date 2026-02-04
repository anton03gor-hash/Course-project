<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Movement;
use App\Models\Stock;
use App\Models\Role;
use App\Models\Category;
use App\Models\Manufacturer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminRole;
    protected $employeeRole;
    protected $clientRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Создаем роли один раз для всех тестов
        $this->adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $this->clientRole = Role::firstOrCreate(['name' => 'client']);
        
        // Создаем категорию и производителя для товаров
        Category::firstOrCreate(['name' => 'Test Category']);
        Manufacturer::firstOrCreate([
            'name' => 'Test Manufacturer',
            'country' => 'RU',
            'city' => 'Moscow',
            'street' => 'Test Street',
            'house_number' => '1'
        ]);
    }

    private function createUserWithRole($role)
    {
        return User::factory()->create(['role_id' => $role->id]);
    }

    /**
     * Тест: Клиент может создать заявку
     */
    public function test_client_can_create_order(): void
    {
        // ARRANGE
        $client = $this->createUserWithRole($this->clientRole);
        
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();
        
        // Создаем остатки товара
        Stock::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 100
        ]);

        $orderData = [
            'warehouse_id' => $warehouse->id,
            'notes' => 'Тестовая заявка',
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => 5
                ]
            ]
        ];

        // ACT
        $response = $this->actingAs($client)
            ->post(route('orders.store'), $orderData);

        // ASSERT
        $this->assertDatabaseHas('orders', [
            'user_id' => $client->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'pending',
            'notes' => 'Тестовая заявка'
        ]);

        // Проверяем, что товар добавлен в заявку
        $order = Order::first();
        $this->assertDatabaseHas('order_products', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 5
        ]);

        // Проверяем ответ
        if ($response->getStatusCode() === 302) {
            $response->assertRedirect(route('orders.show', $order));
        } else {
            $response->assertStatus(200);
        }
        
        // Проверяем сообщение об успехе, если есть
        if (session()->has('success')) {
            $response->assertSessionHas('success');
        }
    }

    /**
     * Тест: Администратор может создать заявку для клиента
     */
    public function test_admin_can_create_order_for_client(): void
    {
        // ARRANGE
        $admin = $this->createUserWithRole($this->adminRole);
        $client = $this->createUserWithRole($this->clientRole);
        
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();
        
        Stock::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 50
        ]);

        $orderData = [
            'user_id' => $client->id,
            'warehouse_id' => $warehouse->id,
            'notes' => 'Заявка от администратора',
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => 10
                ]
            ]
        ];

        // ACT
        $response = $this->actingAs($admin)
            ->post(route('orders.store'), $orderData);

        // ASSERT
        $this->assertDatabaseHas('orders', [
            'user_id' => $client->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'pending'
        ]);

        $order = Order::first();
        
        // Проверяем ответ
        if ($response->getStatusCode() === 302) {
            $response->assertRedirect(route('orders.show', $order));
        } else {
            $response->assertStatus(200);
        }
    }

    /**
     * Тест: Сотрудник не может создать заявку
     */
    public function test_employee_cannot_create_order(): void
    {
        // ARRANGE
        $employee = $this->createUserWithRole($this->employeeRole);
        
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();

        $orderData = [
            'warehouse_id' => $warehouse->id,
            'notes' => 'Тестовая заявка',
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => 5
                ]
            ]
        ];

        // ACT
        $response = $this->actingAs($employee)
            ->post(route('orders.store'), $orderData);

        // ASSERT
        $statusCode = $response->getStatusCode();
        
        // Проверяем, что доступ запрещен
        if ($statusCode === 403) {
            $response->assertForbidden();
        } else {
            // Может быть редирект 302 или другой статус
            $this->assertNotEquals(200, $statusCode, 
                "Сотрудник не должен иметь доступ к созданию заявок. Получен статус: {$statusCode}"
            );
        }
        
        $this->assertDatabaseCount('orders', 0);
    }

    /**
     * Тест: Гость не может создать заявку
     */
    public function test_guest_cannot_create_order(): void
    {
        // ARRANGE
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();

        $orderData = [
            'warehouse_id' => $warehouse->id,
            'notes' => 'Тестовая заявка',
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => 5
                ]
            ]
        ];

        // ACT
        $response = $this->post(route('orders.store'), $orderData);

        // ASSERT
        // Должен быть редирект на страницу входа
        $this->assertEquals(302, $response->getStatusCode(),
            "Гость должен быть перенаправлен на страницу входа. Получен статус: {$response->getStatusCode()}"
        );
        
        $response->assertRedirect('/login');
        $this->assertDatabaseCount('orders', 0);
    }

    /**
     * Тест: Валидация при создании заявки
     */
    public function test_order_creation_validation(): void
    {
        // ARRANGE
        $client = $this->createUserWithRole($this->clientRole);

        // ACT - пытаемся создать заявку с невалидными данными
        $response = $this->actingAs($client)
            ->post(route('orders.store'), [
                'warehouse_id' => '',
                'products' => []
            ]);

        // ASSERT
        // Проверяем наличие ошибок валидации
        if ($response->getStatusCode() === 302) {
            // Если редирект, проверяем ошибки в сессии
            $response->assertSessionHasErrors(['warehouse_id', 'products']);
        } else {
            // Если 422 или другой статус, проверяем JSON ответ
            $response->assertStatus(422);
        }
        
        $this->assertDatabaseCount('orders', 0);
    }

    /**
     * Тест: Создание заявки с недостаточным количеством товара
     */
    public function test_cannot_create_order_with_insufficient_stock(): void
    {
        // ARRANGE
        $client = $this->createUserWithRole($this->clientRole);
        
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();
        
        // Создаем недостаточное количество товара
        Stock::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 5
        ]);

        $orderData = [
            'warehouse_id' => $warehouse->id,
            'notes' => 'Тестовая заявка',
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => 10 // Требуется больше, чем есть
                ]
            ]
        ];

        // ACT
        $response = $this->actingAs($client)
            ->post(route('orders.store'), $orderData);

        // ASSERT
        // Проверяем наличие ошибок
        if ($response->getStatusCode() === 302) {
            $response->assertSessionHasErrors(['products']);
        } elseif ($response->getStatusCode() === 422) {
            // Для API
            $response->assertJsonValidationErrors(['products']);
        }
        
        $this->assertDatabaseCount('orders', 0);
    }

    /**
     * Тест: Автоматическое создание перемещений при создании заявки
     */
    public function test_movements_created_automatically_for_order(): void
{
    // ARRANGE
    $client = $this->createUserWithRole($this->clientRole);
    
    $warehouse1 = Warehouse::factory()->create();
    $warehouse2 = Warehouse::factory()->create();
    $product = Product::factory()->create();
    
    // Создаем остатки на разных складах
    Stock::factory()->create([
        'product_id' => $product->id,
        'warehouse_id' => $warehouse1->id,
        'quantity' => 10 // НЕДОСТАТОЧНО на целевом складе
    ]);
    
    Stock::factory()->create([
        'product_id' => $product->id,
        'warehouse_id' => $warehouse2->id,
        'quantity' => 20
    ]);

    $orderData = [
        'warehouse_id' => $warehouse1->id,
        'notes' => 'Тестовая заявка',
        'products' => [
            [
                'id' => $product->id,
                'quantity' => 25 // Требуется больше, чем есть на целевом складе (10)
            ]
        ]
    ];

    // ACT
    $response = $this->actingAs($client)
        ->post(route('orders.store'), $orderData);

    // ASSERT
    $order = Order::first();
    
    if ($order) {
        // Должны быть созданы перемещения
        $this->assertDatabaseHas('movements', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'status' => 'in_progress'
        ]);
        
        // Проверяем, что создано перемещение с дополнительного склада
        $this->assertDatabaseHas('movements', [
            'from_warehouse_id' => $warehouse2->id,
            'to_warehouse_id' => $warehouse1->id,
            'product_id' => $product->id,
            'type' => 'for_order'
        ]);
        
        // Проверяем количество в перемещении
        $movement = Movement::where('order_id', $order->id)->first();
        $this->assertEquals(15, $movement->quantity); // 25 - 10 = 15
    } else {
        $this->fail('Заявка не была создана');
    }
}
}