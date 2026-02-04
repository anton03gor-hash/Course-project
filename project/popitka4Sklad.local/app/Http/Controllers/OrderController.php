<?php
// app/Http/Controllers/OrderController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User; // Добавляем эту строку
use App\Models\Warehouse;
use App\Models\Movement;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Order::with(['user', 'warehouse', 'products']);
        
        // Для клиентов показываем только их заявки
        if ($user->isClient()) {
            $query->where('user_id', $user->id);
        }
        
        // Фильтр по статусу
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Фильтр по складу
        if ($request->has('warehouse') && $request->warehouse) {
            $query->where('warehouse_id', $request->warehouse);
        }
        
        // Фильтр по клиенту (только для админов и сотрудников)
        if (($user->isAdmin() || $user->isEmployee()) && $request->has('user') && $request->user) {
            $query->where('user_id', $request->user);
        }
        
        // Фильтр по дате
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Поиск по ID заявки
        if ($request->has('search') && $request->search) {
            $query->where('id', $request->search);
        }
        
        $orders = $query->latest()->paginate(15);
        $warehouses = Warehouse::orderBy('name')->get();
        $users = User::whereHas('role', function($q) {
            $q->where('name', 'client');
        })->orderBy('surname')->orderBy('name')->get();
        
        return view('orders.index', compact('orders', 'user', 'warehouses', 'users'));
    }
    // public function index()
    // {
    //     $user = auth()->user();
        
    //     $query = Order::with(['user', 'warehouse', 'products']);
        
    //     // Для клиентов показываем только их заявки
    //     if ($user->isClient()) {
    //         $query->where('user_id', $user->id);
    //     }
        
    //     // Для сотрудников показываем все заявки кроме своих
    //     if ($user->isEmployee()) {
    //         $query->where('user_id', '!=', $user->id);
    //     }
        
    //     $orders = $query->latest()->paginate(10);
        
    //     return view('orders.index', compact('orders', 'user'));
    // }

    public function create()
    {
        $user = auth()->user();
        $products = Product::with(['stocks.warehouse'])
            ->whereHas('stocks', function($query) {
                $query->where('quantity', '>', 0);
            })
            ->orderBy('name')
            ->get();
            
        $warehouses = Warehouse::orderBy('name')->get();
        
        // Для администратора получаем список всех клиентов
        $clients = [];
        if ($user->isAdmin()) {
            $clients = User::whereHas('role', function($query) {
                $query->where('name', 'client');
            })->orderBy('surname')->get();
        }
        
        return view('orders.create', compact('products', 'warehouses', 'user', 'clients'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'notes' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.01',
        ]);

        // Для администратора добавляем выбор клиента
        if ($user->isAdmin()) {
            $validated['user_id'] = $request->validate([
                'user_id' => 'required|exists:users,id'
            ])['user_id'];
        }

        // Проверяем доступность товаров
        $availabilityErrors = $this->checkProductAvailability($validated['products'], $validated['warehouse_id']);
        if (!empty($availabilityErrors)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['products' => $availabilityErrors]);
        }

        return DB::transaction(function () use ($validated, $user) {
            // Создаем заявку
            $orderData = [
                'warehouse_id' => $validated['warehouse_id'],
                'notes' => $validated['notes'],
                'status' => 'pending',
            ];

            // Устанавливаем пользователя: для админа - выбранного, для клиента - текущего
            if (isset($validated['user_id'])) {
                $orderData['user_id'] = $validated['user_id'];
            } else {
                $orderData['user_id'] = $user->id;
            }

            $order = Order::create($orderData);

            // Добавляем товары в заявку
            foreach ($validated['products'] as $productData) {
                $order->products()->attach($productData['id'], [
                    'quantity' => $productData['quantity']
                ]);
            }

            // Автоматически создаем перемещения для выполнения заявки
            $this->createMovementsForOrder($order);

            return redirect()->route('orders.show', $order)
                ->with('success', 'Заявка успешно создана. Перемещения сформированы автоматически.');
        });
    }

    public function show(Order $order)
    {
        // Проверяем права доступа
        $user = auth()->user();
        if ($user->isClient() && $order->user_id !== $user->id) {
            abort(403, 'Доступ запрещен');
        }
        
        $order->load(['user', 'warehouse', 'products.category', 'products.manufacturer', 'movements']);
        
        return view('orders.show', compact('order'));
    }

public function edit(Order $order)
{
    $user = auth()->user();
    
    // Проверяем права доступа
    if ($user->isClient() && $order->user_id !== $user->id) {
        abort(403, 'Доступ запрещен');
    }
    
    $order->load(['products', 'user']);
    $warehouses = Warehouse::orderBy('name')->get();
    $products = Product::with(['stocks.warehouse'])
        ->whereHas('stocks', function($query) {
            $query->where('quantity', '>', 0);
        })
        ->orderBy('name')
        ->get();
    
    // Получаем список всех клиентов для выбора (только для админа)
    $clients = [];
    if ($user->isAdmin()) {
        $clients = User::whereHas('role', function($query) {
            $query->where('name', 'client');
        })->orderBy('surname')->get();
    }
    
    return view('orders.edit', compact('order', 'warehouses', 'products', 'clients', 'user'));
}

public function update(Request $request, Order $order)
{
    $user = auth()->user();
    
    // Проверяем права доступа
    if ($user->isClient() && $order->user_id !== $user->id) {
        abort(403, 'Доступ запрещен');
    }
    
    // Базовые правила валидации
    $validationRules = [
        'warehouse_id' => 'required|exists:warehouses,id',
        'notes' => 'nullable|string|max:500',
        'products' => 'required|array|min:1',
        'products.*.id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|numeric|min:0.01',
    ];
    
    // Только админ может изменять статус и пользователя
    if ($user->isAdmin()) {
        $validationRules['status'] = 'required|in:pending,confirmed,rejected,completed,received';
        $validationRules['user_id'] = 'required|exists:users,id';
    } else {
        // Для клиента статус всегда остается 'pending' при редактировании
        $request->merge(['status' => 'pending']);
    }
    
    $validated = $request->validate($validationRules);
    
    // Для клиента используем его же ID
    if (!$user->isAdmin()) {
        $validated['user_id'] = $user->id;
    }
    
    // Проверяем доступность товаров (кроме случаев, когда заявка уже выполнена или получается)
    $oldStatus = $order->status;
    $newStatus = $validated['status'] ?? $order->status;
    
    // Проверяем доступность только если:
    // 1. Заявка еще не выполнена/получена
    // 2. Или мы меняем статус с completed/received на другой
    if (!in_array($oldStatus, ['completed', 'received']) || in_array($newStatus, ['completed', 'received'])) {
        $availabilityErrors = $this->checkProductAvailability($validated['products'], $validated['warehouse_id'], $order);
        if (!empty($availabilityErrors)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['products' => $availabilityErrors]);
        }
    }

    return DB::transaction(function () use ($validated, $order, $user, $oldStatus, $newStatus) {
        // Обновляем заявку
        $updateData = [
            'warehouse_id' => $validated['warehouse_id'],
            'notes' => $validated['notes'],
            'status' => $newStatus,
        ];
        
        // Только админ может изменять пользователя заказа
        if ($user->isAdmin()) {
            $updateData['user_id'] = $validated['user_id'];
        }
        
        $order->update($updateData);

        // Обновляем товары в заявке (только если статус не completed/received или мы меняем на другой статус)
        if (!in_array($oldStatus, ['completed', 'received']) || !in_array($newStatus, ['completed', 'received'])) {
            $order->products()->detach();
            foreach ($validated['products'] as $productData) {
                $order->products()->attach($productData['id'], [
                    'quantity' => $productData['quantity']
                ]);
            }
        }

        // Логика обработки статусов (только для админа)
        if ($user->isAdmin()) {
            // Если статус изменился на completed или received, вычитаем товары со склада
            if (!in_array($oldStatus, ['completed', 'received']) && in_array($newStatus, ['completed', 'received'])) {
                $this->subtractStockForCompletedOrder($order);
            }

            // Если статус изменился с completed/received на другой, возвращаем товары на склад
            if (in_array($oldStatus, ['completed', 'received']) && !in_array($newStatus, ['completed', 'received'])) {
                $this->returnStockForOrder($order);
            }

            // Если статус изменился на confirmed, обновляем перемещения (но не удаляем выполненные)
            if ($newStatus === 'confirmed' && $oldStatus !== 'confirmed') {
                $this->updateMovementsForOrder($order, true); // true - сохранять выполненные перемещения
            }
            
            // Если меняются товары и статус confirmed, обновляем перемещения
            if ($newStatus === 'confirmed' && $oldStatus === 'confirmed') {
                $this->updateMovementsForOrder($order, true);
            }
        } else {
            // Для клиента всегда создаем/обновляем перемещения
            $this->updateMovementsForOrder($order, false);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Заявка успешно обновлена.');
    });
}

    public function destroy(Order $order)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Доступ запрещен');
        }

        DB::transaction(function () use ($order) {
            // Удаляем связанные перемещения
            $order->movements()->delete();
            
            // Удаляем связи с товарами
            $order->products()->detach();
            
            // Удаляем заявку
            $order->delete();
        });

        return redirect()->route('orders.index')
            ->with('success', 'Заявка успешно удалена.');
    }

    /**
     * Создает перемещения для выполнения заявки
     */
    // private function createMovementsForOrder(Order $order)
    // {
    //     foreach ($order->products as $product) {
    //         $requiredQuantity = $product->pivot->quantity;
    //         $targetWarehouseId = $order->warehouse_id;
            
    //         // Ищем склады с достаточным количеством товара
    //         $availableStocks = Stock::where('product_id', $product->id)
    //             ->where('warehouse_id', '!=', $targetWarehouseId)
    //             ->where('quantity', '>', 0)
    //             ->orderBy('quantity', 'desc')
    //             ->get();
            
    //         $allocatedQuantity = 0;
            
    //         foreach ($availableStocks as $stock) {
    //             if ($allocatedQuantity >= $requiredQuantity) break;
                
    //             $quantityToMove = min($stock->quantity, $requiredQuantity - $allocatedQuantity);
                
    //             Movement::create([
    //                 'from_warehouse_id' => $stock->warehouse_id,
    //                 'to_warehouse_id' => $targetWarehouseId,
    //                 'product_id' => $product->id,
    //                 'quantity' => $quantityToMove,
    //                 'status' => 'in_progress',
    //                 'type' => 'for_order',
    //                 'order_id' => $order->id,
    //             ]);
                
    //             $allocatedQuantity += $quantityToMove;
    //         }
            
    //         // Если товара недостаточно, создаем перемещение с доступным количеством
    //         if ($allocatedQuantity < $requiredQuantity) {
    //             // Можно добавить логику для обработки недостатка товара
    //         }
    //     }
    // }

    /**
     * Обновляет перемещения при изменении заявки
     */
    public function checkMovementsCompletion(Order $order)
    {
        $uncompletedMovements = $order->movements()
            ->where('status', '!=', 'completed')
            ->exists();
        
        return !$uncompletedMovements;
    }
/**
 * Обновляет перемещения при изменении заявки
 * @param bool $keepCompleted - сохранять выполненные перемещения
 */
private function updateMovementsForOrder(Order $order, $keepCompleted = false)
{
    // Получаем выполненные перемещения
    $completedMovements = $keepCompleted ? $order->movements()->where('status', 'completed')->get() : collect();
    
    // Удаляем только незавершенные перемещения
    $order->movements()->where('status', '!=', 'completed')->delete();
    
    // Создаем новые перемещения для недостающего количества
    foreach ($order->products as $product) {
        $requiredQuantity = $product->pivot->quantity;
        $targetWarehouseId = $order->warehouse_id;
        
        // Учитываем уже выполненные перемещения для этого товара
        $completedQuantity = $completedMovements
            ->where('product_id', $product->id)
            ->sum('quantity');
        
        $remainingQuantity = $requiredQuantity - $completedQuantity;
        
        if ($remainingQuantity <= 0) {
            continue; // Весь товар уже перемещен
        }
        
        // Проверяем наличие на целевом складе (кроме уже перемещенного)
        $targetStock = Stock::where('product_id', $product->id)
            ->where('warehouse_id', $targetWarehouseId)
            ->first();

        $availableOnTarget = $targetStock ? $targetStock->quantity : 0;
        $allocatedQuantity = min($availableOnTarget, $remainingQuantity);
        $remainingQuantity -= $allocatedQuantity;

        // Если на целевом складе недостаточно, создаем перемещения с других складов
        if ($remainingQuantity > 0) {
            // Ищем склады с достаточным количеством товара
            $availableStocks = Stock::where('product_id', $product->id)
                ->where('warehouse_id', '!=', $targetWarehouseId)
                ->where('quantity', '>', 0)
                ->orderBy('quantity', 'desc')
                ->get();
            
            foreach ($availableStocks as $stock) {
                if ($remainingQuantity <= 0) break;
                
                $quantityToMove = min($stock->quantity, $remainingQuantity);
                
                Movement::create([
                    'from_warehouse_id' => $stock->warehouse_id,
                    'to_warehouse_id' => $targetWarehouseId,
                    'product_id' => $product->id,
                    'quantity' => $quantityToMove,
                    'status' => 'in_progress',
                    'type' => 'for_order',
                    'order_id' => $order->id,
                ]);
                
                $remainingQuantity -= $quantityToMove;
            }
            
            // Если после распределения все еще осталось необходимое количество
            if ($remainingQuantity > 0) {
                \Log::warning("Недостаточно товара для заявки #{$order->id}. Товар: {$product->name}, недостающее количество: {$remainingQuantity}");
            }
        }
    }
    
    // Восстанавливаем выполненные перемещения
    foreach ($completedMovements as $movement) {
        // Проверяем, не удалили ли мы это перемещение (может быть, если изменился товар)
        $exists = $order->movements()->where('id', $movement->id)->exists();
        if (!$exists && $product = $order->products->firstWhere('id', $movement->product_id)) {
            // Создаем новое выполненное перемещение
            Movement::create([
                'from_warehouse_id' => $movement->from_warehouse_id,
                'to_warehouse_id' => $movement->to_warehouse_id,
                'product_id' => $movement->product_id,
                'quantity' => $movement->quantity,
                'status' => 'completed',
                'type' => 'for_order',
                'order_id' => $order->id,
                'created_at' => $movement->created_at,
                'updated_at' => $movement->updated_at,
            ]);
        }
    }
}
        private function subtractStockForCompletedOrder(Order $order)
{
    foreach ($order->products as $product) {
        $requiredQuantity = $product->pivot->quantity;
        $targetWarehouseId = $order->warehouse_id;
        
        // Учитываем уже перемещенное количество через выполненные перемещения
        $alreadyMoved = $order->movements()
            ->where('product_id', $product->id)
            ->where('status', 'completed')
            ->sum('quantity');
        
        $quantityToSubtract = $requiredQuantity - $alreadyMoved;
        
        if ($quantityToSubtract <= 0) {
            continue; // Весь товар уже перемещен и вычтен
        }
        
        // Находим остаток на целевом складе
        $targetStock = Stock::where('product_id', $product->id)
            ->where('warehouse_id', $targetWarehouseId)
            ->first();
        
        if ($targetStock) {
            // Проверяем, достаточно ли товара на целевом складе
            if ($targetStock->quantity >= $quantityToSubtract) {
                // Вычитаем только недостающее количество
                $targetStock->decrement('quantity', $quantityToSubtract);
                $targetStock->update(['last_update' => now()]);
            } else {
                // Если товара недостаточно, выбрасываем исключение
                throw new \Exception("Недостаточно товара '{$product->name}' на складе '{$targetStock->warehouse->name}'. Доступно: {$targetStock->quantity}, требуется: {$quantityToSubtract} (уже перемещено: {$alreadyMoved})");
            }
        } else {
            throw new \Exception("Товар '{$product->name}' отсутствует на складе '{$order->warehouse->name}'");
        }
    }
}

    /**
     * Возвращает товары на склад при отмене выполнения заявки
     */
    private function returnStockForOrder(Order $order)
{
    foreach ($order->products as $product) {
        $returnQuantity = $product->pivot->quantity;
        $targetWarehouseId = $order->warehouse_id;
        
        // Учитываем уже перемещенное количество через выполненные перемещения
        $alreadyMoved = $order->movements()
            ->where('product_id', $product->id)
            ->where('status', 'completed')
            ->sum('quantity');
        
        $quantityToReturn = $returnQuantity - $alreadyMoved;
        
        if ($quantityToReturn <= 0) {
            continue; // Весь товар был перемещен, возвращать нечего
        }
        
        // Находим или создаем остаток на целевом складе
        $targetStock = Stock::firstOrCreate(
            [
                'product_id' => $product->id,
                'warehouse_id' => $targetWarehouseId
            ],
            [
                'quantity' => 0,
                'position' => null,
                'last_update' => now()
            ]
        );
        
        // Возвращаем только то количество, которое было вычтено
        $targetStock->increment('quantity', $quantityToReturn);
        $targetStock->update(['last_update' => now()]);
    }
}

    /**
 * Проверяет доступность товаров для заявки с учетом уже выполненных перемещений
 */
private function checkProductAvailability($products, $warehouseId, $order = null)
{
    $errors = [];

    foreach ($products as $index => $productData) {
        $product = Product::find($productData['id']);
        $requiredQuantity = $productData['quantity'];

        // Проверяем общее количество товара на всех складах
        $totalAvailable = $product->stocks->sum('quantity');
        
        if ($totalAvailable < $requiredQuantity) {
            $errors[] = "Товар '{$product->name}': недостаточно на всех складах. Доступно: {$totalAvailable}, требуется: {$requiredQuantity}";
            continue;
        }

        // Проверяем количество на целевом складе
        $targetStock = Stock::where('product_id', $product->id)
            ->where('warehouse_id', $warehouseId)
            ->first();

        $availableOnTarget = $targetStock ? $targetStock->quantity : 0;
        
        // Если есть заявка, учитываем уже выполненные перемещения для этого товара
        if ($order) {
            $completedMovementsQuantity = $order->movements()
                ->where('product_id', $product->id)
                ->where('status', 'completed')
                ->sum('quantity');
            
            $availableOnTarget += $completedMovementsQuantity;
        }

        // Если товара недостаточно на целевом складе, проверяем возможность перемещения
        if ($availableOnTarget < $requiredQuantity) {
            $neededFromOther = $requiredQuantity - $availableOnTarget;
            
            // Проверяем доступность на других складах (учитывая уже запланированные перемещения)
            $availableOnOther = Stock::where('product_id', $product->id)
                ->where('warehouse_id', '!=', $warehouseId)
                ->where('quantity', '>', 0)
                ->sum('quantity');
            
            // Если есть заявка, учитываем незавершенные перемещения
            if ($order) {
                $reservedForThisOrder = $order->movements()
                    ->where('product_id', $product->id)
                    ->where('status', '!=', 'completed')
                    ->where('from_warehouse_id', '!=', $warehouseId)
                    ->sum('quantity');
                
                $availableOnOther -= $reservedForThisOrder;
            }
            
            if ($availableOnOther < $neededFromOther) {
                $errors[] = "Товар '{$product->name}': невозможно собрать необходимое количество. На целевом складе: {$availableOnTarget}, на других складах: {$availableOnOther}, требуется: {$requiredQuantity}";
            }
        }
    }

    return $errors;
}

    /**
     * Создает перемещения для выполнения заявки с проверкой доступности
     */
    private function createMovementsForOrder(Order $order)
    {
        foreach ($order->products as $product) {
            $requiredQuantity = $product->pivot->quantity;
            $targetWarehouseId = $order->warehouse_id;
            
            // Проверяем наличие на целевом складе
            $targetStock = Stock::where('product_id', $product->id)
                ->where('warehouse_id', $targetWarehouseId)
                ->first();

            $allocatedQuantity = $targetStock ? min($targetStock->quantity, $requiredQuantity) : 0;
            $remainingQuantity = $requiredQuantity - $allocatedQuantity;

            // Если на целевом складе недостаточно, создаем перемещения с других складов
            if ($remainingQuantity > 0) {
                // Ищем склады с достаточным количеством товара
                $availableStocks = Stock::where('product_id', $product->id)
                    ->where('warehouse_id', '!=', $targetWarehouseId)
                    ->where('quantity', '>', 0)
                    ->orderBy('quantity', 'desc')
                    ->get();
                
                foreach ($availableStocks as $stock) {
                    if ($remainingQuantity <= 0) break;
                    
                    $quantityToMove = min($stock->quantity, $remainingQuantity);
                    
                    Movement::create([
                        'from_warehouse_id' => $stock->warehouse_id,
                        'to_warehouse_id' => $targetWarehouseId,
                        'product_id' => $product->id,
                        'quantity' => $quantityToMove,
                        'status' => 'in_progress',
                        'type' => 'for_order',
                        'order_id' => $order->id,
                    ]);
                    
                    $remainingQuantity -= $quantityToMove;
                }
                
                // Если после распределения все еще осталось необходимое количество
                if ($remainingQuantity > 0) {
                    // Можно добавить логирование или уведомление о недостатке товара
                    \Log::warning("Недостаточно товара для заявки #{$order->id}. Товар: {$product->name}, недостающее количество: {$remainingQuantity}");
                }
            }
        }
    }
}