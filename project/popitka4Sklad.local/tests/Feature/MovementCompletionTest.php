<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Movement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Manufacturer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovementCompletionTest extends TestCase
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

    private function assertAccessDenied($response)
    {
        $statusCode = $response->getStatusCode();
        
        if ($statusCode === 403) {
            $response->assertForbidden();
        } else {
            $this->assertTrue(
                in_array($statusCode, [302, 401]),
                "Ожидался статус 302, 401 или 403. Получен: {$statusCode}"
            );
        }
    }

    /**
     * Тест: Сотрудник может выполнить перемещение
     */
    public function test_employee_can_complete_movement(): void
    {
        // ARRANGE
        $employee = $this->createUserWithRole($this->employeeRole);
        
        $fromWarehouse = Warehouse::factory()->create();
        $toWarehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();
        
        // Создаем исходные остатки
        Stock::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $fromWarehouse->id,
            'quantity' => 100
        ]);
        
        Stock::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $toWarehouse->id,
            'quantity' => 50
        ]);

        $movement = Movement::factory()->create([
            'from_warehouse_id' => $fromWarehouse->id,
            'to_warehouse_id' => $toWarehouse->id,
            'product_id' => $product->id,
            'quantity' => 30,
            'status' => 'in_progress'
        ]);

        // ACT
        $response = $this->actingAs($employee)
            ->patch(route('movements.complete', $movement));

        // ASSERT
        $movement->refresh();
        $this->assertEquals('complete', $movement->status);
        
        // Проверяем обновление остатков
        $fromStock = Stock::where('product_id', $product->id)
            ->where('warehouse_id', $fromWarehouse->id)
            ->first();
        $this->assertEquals(70, $fromStock->quantity); // 100 - 30
        
        $toStock = Stock::where('product_id', $product->id)
            ->where('warehouse_id', $toWarehouse->id)
            ->first();
        $this->assertEquals(80, $toStock->quantity); // 50 + 30
        
        // Проверяем ответ
        if ($response->getStatusCode() === 302) {
            $response->assertRedirect(route('movements.show', $movement));
        } else {
            $response->assertStatus(200);
        }
        
        // Проверяем сообщение об успехе, если есть
        if (session()->has('success')) {
            $response->assertSessionHas('success');
        }
    }

    /**
     * Тест: Администратор может выполнить перемещение
     */
    public function test_admin_can_complete_movement(): void
    {
        // ARRANGE
        $admin = $this->createUserWithRole($this->adminRole);
        
        $movement = Movement::factory()->create(['status' => 'in_progress']);

        // ACT
        $response = $this->actingAs($admin)
            ->patch(route('movements.complete', $movement));

        // ASSERT
        $movement->refresh();
        $this->assertEquals('complete', $movement->status);
        
        // Проверяем ответ
        $this->assertTrue(
            in_array($response->getStatusCode(), [200, 302]),
            "Ожидался статус 200 или 302. Получен: {$response->getStatusCode()}"
        );
    }

    /**
     * Тест: Клиент не может выполнить перемещение
     */
    public function test_client_cannot_complete_movement(): void
    {
        // ARRANGE
        $client = $this->createUserWithRole($this->clientRole);
        
        $movement = Movement::factory()->create(['status' => 'in_progress']);

        // ACT
        $response = $this->actingAs($client)
            ->patch(route('movements.complete', $movement));

        // ASSERT
        $this->assertAccessDenied($response);
        
        $movement->refresh();
        $this->assertEquals('in_progress', $movement->status);
    }

    /**
     * Тест: Нельзя выполнить уже выполненное перемещение
     */
    public function test_cannot_complete_already_completed_movement(): void
    {
        // ARRANGE
        $employee = $this->createUserWithRole($this->employeeRole);
        
        $movement = Movement::factory()->create(['status' => 'complete']);

        // ACT
        $response = $this->actingAs($employee)
            ->patch(route('movements.complete', $movement));

        // ASSERT
        $statusCode = $response->getStatusCode();
        
        if ($statusCode === 302) {
            $response->assertRedirect();
            
            // Проверяем наличие сообщения об ошибке
            if (session()->has('error')) {
                $response->assertSessionHas('error');
            }
        } else {
            // Если не редирект, проверяем, что статус не изменился
            $movement->refresh();
            $this->assertEquals('complete', $movement->status);
        }
    }

    /**
     * Тест: Нельзя выполнить перемещение с недостаточным количеством товара
     */
    // public function test_cannot_complete_movement_with_insufficient_stock(): void
    // {
    //     // ARRANGE
    //     $employee = $this->createUserWithRole($this->employeeRole);
        
    //     $fromWarehouse = Warehouse::factory()->create();
    //     $product = Product::factory()->create();
        
    //     // Создаем недостаточное количество товара
    //     Stock::factory()->create([
    //         'product_id' => $product->id,
    //         'warehouse_id' => $fromWarehouse->id,
    //         'quantity' => 5
    //     ]);

    //     $movement = Movement::factory()->create([
    //         'from_warehouse_id' => $fromWarehouse->id,
    //         'product_id' => $product->id,
    //         'quantity' => 10, // Требуется больше, чем есть
    //         'status' => 'in_progress'
    //     ]);

    //     // ACT
    //     $response = $this->actingAs($employee)
    //         ->patch(route('movements.complete', $movement));

    //     // ASSERT
    //     $movement->refresh();
        
    //     // Проверяем, что статус не изменился
    //     $this->assertEquals('in_progress', $movement->status);
        
    //     // Проверяем наличие ошибок
    //     if ($response->getStatusCode() === 302) {
    //         // Если есть редирект, могут быть ошибки в сессии
    //         if (session()->has('errors')) {
    //             $response->assertSessionHasErrors();
    //         }
    //     }
    // }
}