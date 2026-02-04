<?php
// app/Http/Controllers/CategoryController.php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products')
            ;
                // Поиск по названию
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $categories = $query->latest()->paginate(10);
        // $categories = Category::withCount('products')
        //     ->orderBy('name')
        //     ->paginate(15);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:45|unique:categories,name',
            'description' => 'nullable|string|max:255',
        ]);

        $category = Category::create($validated);

        return redirect()
            ->route('categories.show', $category)
            ->with('success', 'Категория успешно создана.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load(['products.manufacturer', 'products.stocks.warehouse']);
        
        $products = $category->products()
            ->with(['manufacturer', 'stocks.warehouse'])
            ->orderBy('name')
            ->paginate(20);

        return view('categories.show', compact('category', 'products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:45',
                Rule::unique('categories')->ignore($category->id),
            ],
            'description' => 'nullable|string|max:255',
        ]);

        $category->update($validated);

        return redirect()
            ->route('categories.show', $category)
            ->with('success', 'Категория успешно обновлена.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Проверяем, есть ли связанные товары
        if ($category->products()->exists()) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'Невозможно удалить категорию: имеются связанные товары.');
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Категория успешно удалена.');
    }
}