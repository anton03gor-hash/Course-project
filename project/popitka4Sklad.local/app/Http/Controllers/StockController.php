<?php
// app/Http/Controllers/StockController.php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::with(['product.category', 'product.manufacturer', 'warehouse']);
        
        // Фильтр по складу
        if ($request->has('warehouse') && $request->warehouse) {
            $query->where('warehouse_id', $request->warehouse);
        }
        
        // Фильтр по товару
        if ($request->has('product') && $request->product) {
            $query->where('product_id', $request->product);
        }
        
        // Фильтр по категории
        if ($request->has('category') && $request->category) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }
        
        // Фильтр по статусу остатков
        if ($request->has('status') && $request->status) {
            switch ($request->status) {
                case 'in_stock':
                    $query->where('quantity', '>', 10);
                    break;
                case 'low_stock':
                    $query->where('quantity', '>', 0)->where('quantity', '<=', 10);
                    break;
                case 'out_of_stock':
                    $query->where('quantity', 0);
                    break;
                case 'with_stock':
                    $query->where('quantity', '>', 0);
                    break;
            }
        }
        
        // Фильтр по количеству
        if ($request->has('quantity_from') && $request->quantity_from !== '') {
            $query->where('quantity', '>=', $request->quantity_from);
        }
        
        if ($request->has('quantity_to') && $request->quantity_to !== '') {
            $query->where('quantity', '<=', $request->quantity_to);
        }
        
        // Поиск по названию товара
        if ($request->has('search') && $request->search) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }
        
        // Сортировка
        $sort = $request->get('sort', 'warehouse_id');
        $direction = $request->get('direction', 'asc');
        
        if (in_array($sort, ['warehouse_id', 'quantity', 'last_update'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('warehouse_id')->orderBy('product_id');
        }
        
        $stocks = $query->paginate(20);
        $warehouses = Warehouse::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        
        return view('stocks.index', compact('stocks', 'warehouses', 'products', 'categories'));
    }
    // public function index()
    // {
    //     $stocks = Stock::with(['product.category', 'product.manufacturer', 'warehouse'])
    //         ->orderBy('warehouse_id')
    //         ->orderBy('product_id')
    //         ->paginate(20);
            
    //     $warehouses = Warehouse::orderBy('name')->get();
    //     $products = Product::orderBy('name')->get();
        
    //     return view('stocks.index', compact('stocks', 'warehouses', 'products'));
    // }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        
        return view('stocks.create', compact('products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:0',
            'position' => 'nullable|string|max:45',
        ]);

        // Проверяем, существует ли уже запись для этого товара на этом складе
        $existingStock = Stock::where('product_id', $validated['product_id'])
            ->where('warehouse_id', $validated['warehouse_id'])
            ->first();

        if ($existingStock) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'product_id' => 'Этот товар уже есть на выбранном складе. Используйте редактирование для изменения количества.'
                ]);
        }

        $stock = Stock::create([
            'product_id' => $validated['product_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'quantity' => $validated['quantity'],
            'position' => $validated['position'],
            'last_update' => now(),
        ]);

        return redirect()->route('stocks.show', $stock)
            ->with('success', 'Остаток успешно создан.');
    }

    public function show(Stock $stock)
    {
        $stock->load(['product.category', 'product.manufacturer', 'warehouse', 'product.movements']);
        
        return view('stocks.show', compact('stock'));
    }

    public function edit(Stock $stock)
    {
        $stock->load(['product', 'warehouse']);
        
        return view('stocks.edit', compact('stock'));
    }

    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0',
            'position' => 'nullable|string|max:45',
        ]);

        $stock->update([
            'quantity' => $validated['quantity'],
            'position' => $validated['position'],
            'last_update' => now(),
        ]);

        return redirect()->route('stocks.show', $stock)
            ->with('success', 'Остаток успешно обновлен.');
    }

    public function destroy(Stock $stock)
    {
        // Проверяем, есть ли связанные перемещения
        if ($stock->product->movements()->where(function($query) use ($stock) {
            $query->where('from_warehouse_id', $stock->warehouse_id)
                  ->orWhere('to_warehouse_id', $stock->warehouse_id);
        })->exists()) {
            return redirect()->route('stocks.index')
                ->with('error', 'Невозможно удалить остаток: имеются связанные перемещения.');
        }

        $stock->delete();

        return redirect()->route('stocks.index')
            ->with('success', 'Остаток успешно удален.');
    }

    /**
     * Быстрое обновление количества
     */
    public function updateQuantity(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);

        $stock->update([
            'quantity' => $validated['quantity'],
            'last_update' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Количество успешно обновлено.');
    }

    /**
     * Получение остатков по товару (для AJAX)
     */
    public function getByProduct(Product $product)
    {
        $stocks = Stock::with('warehouse')
            ->where('product_id', $product->id)
            ->where('quantity', '>', 0)
            ->get();

        return response()->json($stocks);
    }

    /**
     * Получение остатков по складу (для AJAX)
     */
    public function getByWarehouse(Warehouse $warehouse)
    {
        $stocks = Stock::with('product')
            ->where('warehouse_id', $warehouse->id)
            ->where('quantity', '>', 0)
            ->get();

        return response()->json($stocks);
    }
}