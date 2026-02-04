<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manufacturer;
use Illuminate\Validation\Rule;

class ManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Manufacturer::withCount('products');
        
        // Поиск по названию
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Фильтр по стране
        if ($request->has('country') && $request->country) {
            $query->where('country', $request->country);
        }
        
        // Фильтр по городу
        if ($request->has('city') && $request->city) {
            $query->where('city', $request->city);
        }
        
        $manufacturers = $query->latest()->paginate(10);
        
        // Получаем уникальные страны и города для фильтров
        $countries = Manufacturer::distinct()->pluck('country')->filter();
        $cities = Manufacturer::distinct()->pluck('city')->filter();

        return view('manufacturers.index', compact('manufacturers', 'countries', 'cities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:manufacturers,name',
            'country' => 'required|string|max:45',
            'city' => 'required|string|max:60',
            'street' => 'required|string|max:60',
            'house_number' => 'required|string|max:10',
        ]);

        $manufacturer = Manufacturer::create($validated);

        return redirect()
            ->route('manufacturers.show', $manufacturer)
            ->with('success', 'Производитель успешно создан.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Manufacturer $manufacturer)
    {
        $manufacturer->load(['products.category', 'products.stocks.warehouse']);
        
        $products = $manufacturer->products()
            ->with(['category', 'stocks.warehouse'])
            ->orderBy('name')
            ->paginate(20);

        return view('manufacturers.show', compact('manufacturer', 'products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Manufacturer $manufacturer)
    {
        return view('manufacturers.edit', compact('manufacturer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Manufacturer $manufacturer)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('manufacturers')->ignore($manufacturer->id),
            ],
            'country' => 'required|string|max:45',
            'city' => 'required|string|max:60',
            'street' => 'required|string|max:60',
            'house_number' => 'required|string|max:10',
        ]);

        $manufacturer->update($validated);

        return redirect()
            ->route('manufacturers.show', $manufacturer)
            ->with('success', 'Производитель успешно обновлен.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manufacturer $manufacturer)
    {
        // Проверяем, есть ли связанные товары
        if ($manufacturer->products()->exists()) {
            return redirect()
                ->route('manufacturers.index')
                ->with('error', 'Невозможно удалить производителя: имеются связанные товары.');
        }

        $manufacturer->delete();

        return redirect()
            ->route('manufacturers.index')
            ->with('success', 'Производитель успешно удален.');
    }
}
