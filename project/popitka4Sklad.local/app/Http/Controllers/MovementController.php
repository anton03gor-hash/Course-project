<?php
// app/Http/Controllers/MovementController.php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Order;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Movement::with(['fromWarehouse', 'toWarehouse', 'product', 'order']);
        
        // Для сотрудников показываем только активные перемещения
        if ($user->isEmployee()) {
            $query->where('status', 'in_progress');
        }
        
        // Фильтр по статусу
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Фильтр по типу
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Фильтр по складу
        if ($request->has('warehouse') && $request->warehouse) {
            $warehouseId = $request->warehouse;
            $query->where(function($q) use ($warehouseId) {
                $q->where('from_warehouse_id', $warehouseId)
                  ->orWhere('to_warehouse_id', $warehouseId);
            });
        }
        
        // Фильтр по товару
        if ($request->has('product') && $request->product) {
            $query->where('product_id', $request->product);
        }
        
        // Фильтр по дате
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $movements = $query->latest()->paginate(15);
        $warehouses = Warehouse::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        return view('movements.index', compact('movements', 'user', 'warehouses', 'products'));
    }

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
    $orders = Order::where('status', 'confirmed')->orderBy('created_at', 'desc')->get();
    
    // Получаем данные о наличии товаров для JavaScript
    $warehouseStocks = Stock::with('warehouse')
        ->where('quantity', '>', 0)
        ->get()
        ->map(function($stock) {
            return [
                'product_id' => $stock->product_id,
                'warehouse_id' => $stock->warehouse_id,
                'quantity' => $stock->quantity,
                'warehouse_name' => $stock->warehouse->name
            ];
        });
    
    return view('movements.create', compact('products', 'warehouses', 'orders', 'user', 'warehouseStocks'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'type' => 'required|in:between_warehouses,for_order',
            'order_id' => 'nullable|required_if:type,for_order|exists:orders,id',
        ]);

        // Проверяем доступность товара на складе-источнике
        $availableStock = Stock::where('product_id', $validated['product_id'])
            ->where('warehouse_id', $validated['from_warehouse_id'])
            ->first();

        if (!$availableStock || $availableStock->quantity < $validated['quantity']) {
            $availableQuantity = $availableStock ? $availableStock->quantity : 0;
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'quantity' => "Недостаточно товара на складе-источнике. Доступно: {$availableQuantity}"
                ]);
        }

        return DB::transaction(function () use ($validated) {
            $movement = Movement::create([
                'from_warehouse_id' => $validated['from_warehouse_id'],
                'to_warehouse_id' => $validated['to_warehouse_id'],
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'status' => 'in_progress',
                'type' => $validated['type'],
                'order_id' => $validated['order_id'] ?? null,
            ]);

            return redirect()->route('movements.show', $movement)
                ->with('success', 'Перемещение успешно создано.');
        });
    }

    public function show(Movement $movement)
    {
        $movement->load(['fromWarehouse', 'toWarehouse', 'product', 'order.user']);
        
        return view('movements.show', compact('movement'));
    }

public function edit(Movement $movement)
{
    // Только админ может редактировать перемещения
    if (!auth()->user()->isAdmin()) {
        abort(403, 'Доступ запрещен');
    }
    
    $movement->load(['product', 'fromWarehouse', 'toWarehouse']);
    $products = Product::with(['stocks'])
        ->orderBy('name')
        ->get()
        ->map(function($product) {
            $product->total_quantity = $product->stocks->sum('quantity');
            return $product;
        });
        
    $warehouses = Warehouse::orderBy('name')->get();
    $orders = Order::where('status', 'confirmed')->orderBy('created_at', 'desc')->get();
    
    // Получаем данные о наличии товаров для JavaScript
    $warehouseStocks = Stock::with('warehouse')
        ->get()
        ->map(function($stock) {
            return [
                'product_id' => $stock->product_id,
                'warehouse_id' => $stock->warehouse_id,
                'quantity' => $stock->quantity,
                'warehouse_name' => $stock->warehouse->name
            ];
        });
    
    return view('movements.edit', compact('movement', 'products', 'warehouses', 'orders', 'warehouseStocks'));
}

    public function update(Request $request, Movement $movement)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Доступ запрещен');
        }

        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'status' => 'required|in:in_progress,complete',
            'type' => 'required|in:between_warehouses,for_order',
            'order_id' => 'nullable|required_if:type,for_order|exists:orders,id',
        ]);

        return DB::transaction(function () use ($validated, $movement) {
            $oldStatus = $movement->status;
            $newStatus = $validated['status'];

            // Обновляем перемещение
            $movement->update($validated);

            // Если статус изменился на complete, обновляем остатки
            if ($oldStatus !== 'complete' && $newStatus === 'complete') {
                $this->completeMovement($movement);
            }

            // Если статус изменился с complete на in_progress, отменяем перемещение
            if ($oldStatus === 'complete' && $newStatus === 'in_progress') {
                $this->revertMovement($movement);
            }

            return redirect()->route('movements.show', $movement)
                ->with('success', 'Перемещение успешно обновлено.');
        });
    }

    public function destroy(Movement $movement)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Доступ запрещен');
        }

        // Нельзя удалить выполненное перемещение
        if ($movement->status === 'complete') {
            return redirect()->route('movements.index')
                ->with('error', 'Нельзя удалить выполненное перемещение.');
        }

        $movement->delete();

        return redirect()->route('movements.index')
            ->with('success', 'Перемещение успешно удалено.');
    }

    /**
     * Выполняет перемещение - обновляет остатки на складах
     */
    private function completeMovement(Movement $movement)
    {
        // Уменьшаем количество на складе-источнике
        $fromStock = Stock::where('product_id', $movement->product_id)
            ->where('warehouse_id', $movement->from_warehouse_id)
            ->first();

        if ($fromStock) {
            $fromStock->decrement('quantity', $movement->quantity);
            $fromStock->update(['last_update' => now()]);
        }

        // Увеличиваем количество на складе-назначения
        $toStock = Stock::firstOrCreate(
            [
                'product_id' => $movement->product_id,
                'warehouse_id' => $movement->to_warehouse_id
            ],
            [
                'quantity' => 0,
                'position' => null,
                'last_update' => now()
            ]
        );

        $toStock->increment('quantity', $movement->quantity);
        $toStock->update(['last_update' => now()]);
    }

    /**
     * Отменяет выполненное перемещение - возвращает остатки
     */
    private function revertMovement(Movement $movement)
    {
        // Возвращаем количество на склад-источник
        $fromStock = Stock::where('product_id', $movement->product_id)
            ->where('warehouse_id', $movement->from_warehouse_id)
            ->first();

        if ($fromStock) {
            $fromStock->increment('quantity', $movement->quantity);
            $fromStock->update(['last_update' => now()]);
        }

        // Уменьшаем количество на складе-назначения
        $toStock = Stock::where('product_id', $movement->product_id)
            ->where('warehouse_id', $movement->to_warehouse_id)
            ->first();

        if ($toStock) {
            $toStock->decrement('quantity', $movement->quantity);
            $toStock->update(['last_update' => now()]);
            
            // Если количество стало 0, удаляем запись
            if ($toStock->quantity <= 0) {
                $toStock->delete();
            }
        }
    }

    /**
     * Быстрое выполнение перемещения (для сотрудников)
     */
    public function complete(Movement $movement)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isEmployee()) {
            abort(403, 'Доступ запрещен');
        }

        if ($movement->status === 'complete') {
            return redirect()->back()->with('error', 'Перемещение уже выполнено.');
        }

        return DB::transaction(function () use ($movement) {
            $movement->update(['status' => 'complete']);
            $this->completeMovement($movement);

            return redirect()->route('movements.show', $movement)
                ->with('success', 'Перемещение отмечено как выполненное.');
        });
    }
}