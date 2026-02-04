<?php
// app/Http/Controllers/ProductController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Product::with(['category', 'manufacturer', 'stocks.warehouse']);
        
        // Поиск по названию
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Фильтр по категории
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        // Фильтр по производителю
        if ($request->has('manufacturer') && $request->manufacturer) {
            $query->where('manufacturer_id', $request->manufacturer);
        }
        
        $products = $query->latest()->paginate(10);
        
        return view('products.index', compact('products', 'user'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $manufacturers = Manufacturer::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('products.create', compact('categories', 'manufacturers', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'manufacturer_id' => 'required|exists:manufacturers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:0',
            'position' => 'nullable|string|max:45',
        ]);

        // Создаем товар
        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'manufacturer_id' => $validated['manufacturer_id'],
        ]);

        // Создаем запись об остатке
        if ($validated['quantity'] > 0) {
            Stock::create([
                'product_id' => $product->id,
                'warehouse_id' => $validated['warehouse_id'],
                'quantity' => $validated['quantity'],
                'position' => $validated['position'],
                'last_update' => now(),
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Товар успешно создан.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'manufacturer', 'stocks.warehouse', 'orders.user']);
        
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $manufacturers = Manufacturer::orderBy('name')->get();
        
        return view('products.edit', compact('product', 'categories', 'manufacturers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'manufacturer_id' => 'required|exists:manufacturers,id',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Товар успешно обновлен.');
    }

    public function destroy(Product $product)
    {
        // Проверяем, есть ли связанные записи
        if ($product->orders()->exists() || $product->movements()->exists()) {
            return redirect()->route('products.index')
                ->with('error', 'Невозможно удалить товар: имеются связанные заявки или перемещения.');
        }

        $product->stocks()->delete();
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Товар успешно удален.');
    }
}