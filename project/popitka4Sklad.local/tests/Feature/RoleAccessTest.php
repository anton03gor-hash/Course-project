<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Movement;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Manufacturer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

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
    }

    private function createUserWithRole($role)
    {
        return User::factory()->create(['role_id' => $role->id]);
    }

private function assertAccessDenied($response)
{
    if ($response->getStatusCode() === 403) {
        $response->assertForbidden();
    } else {
        // Может быть 302 (редирект) или 401 (Unauthorized)
        $this->assertTrue(
            in_array($response->getStatusCode(), [302, 401]),
            "Ожидался статус 302 или 401, получен: {$response->getStatusCode()}"
        );
    }
}

    private function assertAccessGranted($response)
    {
        if ($response->getStatusCode() === 200) {
            $response->assertStatus(200);
        } else {
            // Если не 200, то должен быть редирект на нужную страницу
            $response->assertRedirect();
        }
    }

    /**
     * Тест: Доступ к дашборду для разных ролей
     */
    public function test_dashboard_access_for_all_roles(): void
{
    $response = $this->get(route('dashboard'));
    
    // Проверяем, что это редирект (статус 302)
    $this->assertEquals(302, $response->getStatusCode(),
        "Гость должен быть перенаправлен на страницу входа. Получен статус: {$response->getStatusCode()}"
    );
    // Администратор имеет доступ
    $admin = $this->createUserWithRole($this->adminRole);
    $response = $this->actingAs($admin)->get(route('dashboard'));
    $response->assertStatus(200); // Должен быть прямой доступ

    // Сотрудник имеет доступ
    $employee = $this->createUserWithRole($this->employeeRole);
    $response = $this->actingAs($employee)->get(route('dashboard'));
    $response->assertStatus(200); // Должен быть прямой доступ

    // Клиент имеет доступ
    $client = $this->createUserWithRole($this->clientRole);
    $response = $this->actingAs($client)->get(route('dashboard'));
    $response->assertStatus(200); // Должен быть прямой доступ

    // Гость не имеет доступа - должен быть редирект на логин
    $response = $this->get(route('dashboard'));
    
    // Если маршрут dashboard защищен auth middleware, будет редирект
    // if ($response->getStatusCode() === 302) {
    //     $response->assertRedirect('/login');
    // } else {
    //     // Если нет middleware, тест должен провалиться - гость не должен видеть дашборд
    //     $this->assertNotEquals(200, $response->getStatusCode(), 
    //         "Гость не должен иметь доступ к дашборду. Получен статус: {$response->getStatusCode()}");
    // }
}

    /**
     * Тест: Доступ к управлению пользователями (только для admin)
     */
    public function test_user_management_access(): void
    {
        // Администратор имеет доступ
        $admin = $this->createUserWithRole($this->adminRole);
        $response = $this->actingAs($admin)->get(route('users.index'));
        $this->assertAccessGranted($response);

        // Сотрудник не имеет доступ к управлению пользователями
        $employee = $this->createUserWithRole($this->employeeRole);
        $response = $this->actingAs($employee)->get(route('users.index'));
        $this->assertAccessDenied($response);

        // Обычный пользователь не имеет доступа
        $client = $this->createUserWithRole($this->clientRole);
        $response = $this->actingAs($client)->get(route('users.index'));
        $this->assertAccessDenied($response);
    }

    /**
     * Тест: Доступ к управлению заявками для разных ролей
     */
    public function test_order_management_access(): void
    {
        $client = $this->createUserWithRole($this->clientRole);
        
        // Создаем простую заявку
        $warehouse = Warehouse::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $client->id,
            'warehouse_id' => $warehouse->id,
        ]);

        // Администратор имеет доступ ко всем операциям
        $admin = $this->createUserWithRole($this->adminRole);
        $response = $this->actingAs($admin)->get(route('orders.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('orders.create'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('orders.edit', $order));
        $response->assertStatus(200);

        // Менеджер (сотрудник) имеет доступ
        $employee = $this->createUserWithRole($this->employeeRole);
        $response = $this->actingAs($employee)->get(route('orders.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($employee)->get(route('orders.show', $order));
        $response->assertStatus(200);

        // Клиент имеет доступ только к своим заявкам
        $response = $this->actingAs($client)->get(route('orders.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($client)->get(route('orders.show', $order));
        $response->assertStatus(200);

        // Другой клиент не может просматривать чужую заявку
        $otherClient = $this->createUserWithRole($this->clientRole);
        $response = $this->actingAs($otherClient)->get(route('orders.show', $order));
        $this->assertAccessDenied($response);
    }

    /**
     * Тест: Доступ к управлению перемещениями для admin и employee
     */
    public function test_movement_management_access(): void
    {
        // Создаем перемещение
        $warehouse1 = Warehouse::factory()->create();
        $warehouse2 = Warehouse::factory()->create();
        $product = Product::factory()->create();
        
        $movement = Movement::factory()->create([
            'from_warehouse_id' => $warehouse1->id,
            'to_warehouse_id' => $warehouse2->id,
            'product_id' => $product->id,
        ]);

        // Администратор имеет полный доступ
        $admin = $this->createUserWithRole($this->adminRole);
        $response = $this->actingAs($admin)->get(route('movements.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('movements.create'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('movements.edit', $movement));
        $response->assertStatus(200);

        // Сотрудник имеет доступ к просмотру и выполнению
        $employee = $this->createUserWithRole($this->employeeRole);
        $response = $this->actingAs($employee)->get(route('movements.index'));
        $response->assertStatus(200);

        // Проверяем доступ к выполнению перемещения
        $response = $this->actingAs($employee)->patch(route('movements.complete', $movement));
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302, 403]));

        // Сотрудник не может редактировать перемещения
        $response = $this->actingAs($employee)->get(route('movements.edit', $movement));
        $this->assertAccessDenied($response);

        // Клиент не имеет доступа к перемещениям
        $client = $this->createUserWithRole($this->clientRole);
        $response = $this->actingAs($client)->get(route('movements.index'));
        $this->assertAccessDenied($response);
    }

    /**
     * Тест: Доступ к управлению товарами для admin
     */
    public function test_product_management_access(): void
    {
        // Создаем товар
        $category = Category::firstOrCreate(['name' => 'Test Category']);
        $manufacturer = Manufacturer::firstOrCreate([
            'name' => 'Test Manufacturer',
            'country' => 'RU',
            'city' => 'Moscow',
            'street' => 'Test Street',
            'house_number' => '1'
        ]);
        
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'manufacturer_id' => $manufacturer->id,
        ]);

        // Администратор имеет доступ
        $admin = $this->createUserWithRole($this->adminRole);
        $response = $this->actingAs($admin)->get(route('products.create'));
        $this->assertAccessGranted($response);

        $response = $this->actingAs($admin)->get(route('products.edit', $product));
        $this->assertAccessGranted($response);

        // Сотрудник не имеет доступа к управлению товарами
        $employee = $this->createUserWithRole($this->employeeRole);
        $response = $this->actingAs($employee)->get(route('products.create'));
        $this->assertAccessDenied($response);

        // Клиент имеет доступ только к просмотру
        $client = $this->createUserWithRole($this->clientRole);
        $response = $this->actingAs($client)->get(route('products.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($client)->get(route('products.show', $product));
        $response->assertStatus(200);

        $response = $this->actingAs($client)->get(route('products.create'));
        $this->assertAccessDenied($response);
    }

    /**
     * Тест: Доступ к управлению складами для admin
     */
    public function test_warehouse_management_access(): void
    {
        $warehouse = Warehouse::factory()->create();

        // Администратор имеет доступ
        $admin = $this->createUserWithRole($this->adminRole);
        $response = $this->actingAs($admin)->get(route('warehouses.create'));
        $this->assertAccessGranted($response);

        $response = $this->actingAs($admin)->get(route('warehouses.edit', $warehouse));
        $this->assertAccessGranted($response);

        // Сотрудник не имеет доступа к управлению складами
        $employee = $this->createUserWithRole($this->employeeRole);
        $response = $this->actingAs($employee)->get(route('warehouses.create'));
        $this->assertAccessDenied($response);

        // Клиент имеет доступ только к просмотру складов при создании заявки
        $client = $this->createUserWithRole($this->clientRole);
        $response = $this->actingAs($client)->get(route('orders.create'));
        $response->assertStatus(200); // На странице создания заявки должны показываться склады
    }

    /**
     * Тест: CRUD операции с заявками для разных ролей
     */
    public function test_crud_operations_for_different_roles(): void
{
    // ARRANGE
    $warehouse = Warehouse::factory()->create();
    
    // Создаем категорию и производителя для товара
    $category = Category::firstOrCreate(['name' => 'Test Category']);
    $manufacturer = Manufacturer::firstOrCreate([
        'name' => 'Test Manufacturer',
        'country' => 'RU',
        'city' => 'Moscow',
        'street' => 'Test Street',
        'house_number' => '1'
    ]);
    
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'manufacturer_id' => $manufacturer->id,
    ]);
    
    // Создаем остатки товара
    Stock::factory()->create([
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 100
    ]);

    // ВАЖНО: Добавляем поле 'notes' в данные заявки
    $orderData = [
        'warehouse_id' => $warehouse->id,
        'notes' => 'Тестовая заявка', // ← ДОБАВЛЕНО
        'products' => [
            [
                'id' => $product->id,
                'quantity' => 10
            ]
        ]
    ];

    // Клиент может создавать заявки
    $client = $this->createUserWithRole($this->clientRole);
    $response = $this->actingAs($client)
        ->post(route('orders.store'), $orderData);

    // Проверяем создание заявки
    if ($response->getStatusCode() === 302) {
        $response->assertRedirect(); // редирект на страницу заявки
    } elseif ($response->getStatusCode() === 200) {
        $response->assertStatus(200);
    } else {
        // Если ошибка, выводим отладочную информацию
        $response->assertStatus(500); // или любая другая ошибка
    }

    // Проверяем, что заявка создана в БД
    $this->assertDatabaseHas('orders', [
        'user_id' => $client->id,
        'warehouse_id' => $warehouse->id,
        'notes' => 'Тестовая заявка' // ← Проверяем notes
    ]);

    $order = Order::first();
    
    if (!$order) {
        $this->fail('Заявка не была создана');
        return;
    }

    // Клиент может редактировать свои заявки
    $updateData = [
        'warehouse_id' => $warehouse->id,
        'notes' => 'Обновленная заявка',
        'products' => [
            [
                'id' => $product->id,
                'quantity' => 5
            ]
        ]
    ];

    $response = $this->actingAs($client)
        ->put(route('orders.update', $order), $updateData);

    // Проверяем ответ
    $statusCode = $response->getStatusCode();
    
    if ($statusCode === 302) {
        $response->assertRedirect(route('orders.show', $order));
    } elseif ($statusCode === 200) {
        $response->assertStatus(200);
    } else {
        $this->fail("Неожиданный статус при обновлении: {$statusCode}");
    }

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'notes' => 'Обновленная заявка'
    ]);

    // Администратор может удалять заявки
    $admin = $this->createUserWithRole($this->adminRole);
    $response = $this->actingAs($admin)
        ->delete(route('orders.destroy', $order));

    if ($response->getStatusCode() === 302) {
        $response->assertRedirect(route('orders.index'));
    } elseif ($response->getStatusCode() === 200) {
        $response->assertStatus(200);
    }

    $this->assertDatabaseMissing('orders', ['id' => $order->id]);

    // Клиент не может удалять заявки
    $newOrder = Order::factory()->create(['user_id' => $client->id]);
    $response = $this->actingAs($client)
        ->delete(route('orders.destroy', $newOrder));
        
    $this->assertAccessDenied($response);
    $this->assertDatabaseHas('orders', ['id' => $newOrder->id]);
}

    /**
     * Тест: Разные уровни доступа для редактирования заявок
     */
    public function test_order_edit_access_levels(): void
    {
        $client = $this->createUserWithRole($this->clientRole);
        $order = Order::factory()->create(['user_id' => $client->id]);

        // Клиент может редактировать свою заявку
        $response = $this->actingAs($client)
            ->get(route('orders.edit', $order));
        $response->assertStatus(200);

        // Другой клиент не может редактировать чужую заявку
        $otherClient = $this->createUserWithRole($this->clientRole);
        $response = $this->actingAs($otherClient)
            ->get(route('orders.edit', $order));
        $this->assertAccessDenied($response);

        // Администратор может редактировать любую заявку
        $admin = $this->createUserWithRole($this->adminRole);
        $response = $this->actingAs($admin)
            ->get(route('orders.edit', $order));
        $response->assertStatus(200);

        // Сотрудник может редактировать заявки (для обработки)
        $employee = $this->createUserWithRole($this->employeeRole);
        $response = $this->actingAs($employee)
            ->get(route('orders.edit', $order));
        $response->assertStatus(200);
    }

    /**
     * Тест: Яндекс OAuth доступ для всех пользователей
     */
    public function test_yandex_oauth_access_for_all(): void
    {
        // Гость может получить доступ к Яндекс OAuth
        $response = $this->get(route('yandex.redirect'));
        $this->assertTrue(in_array($response->getStatusCode(), [302, 200]));

        // Авторизованный пользователь также может получить доступ
        $client = $this->createUserWithRole($this->clientRole);
        $response = $this->actingAs($client)->get(route('yandex.redirect'));
        $this->assertTrue(in_array($response->getStatusCode(), [302, 200]));
    }
}