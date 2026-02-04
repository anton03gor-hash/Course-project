{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Управление товарами')

@section('content')
<div class="container py-4">
    <!-- Заголовок и кнопки -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold mb-1">
                        <i></i>Товары
                    </h1>
                    <p class="text-muted mb-0">Управление товарами и остатками на складах</p>
                </div>
                @if(auth()->user()->isAdmin())
                <div>
                    <a href="{{ route('categories.index') }}" class="btn btn-gray">
                        <i></i>Категории
                    </a>
                    <a href="{{ route('manufacturers.index') }}" class="btn btn-gray">
                        <i></i>Производители
                    </a>
                    <a href="{{ route('products.create') }}" class="btn btn-gray">
                        <i class="fas fa-plus me-2"></i>Добавить товар
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Фильтры и поиск -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-custom p-3">
                <form action="{{ route('products.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Поиск по названию..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-select">
                                <option value="">Все категории</option>
                                @foreach($categories = \App\Models\Category::orderBy('name')->get() as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="manufacturer" class="form-select">
                                <option value="">Все производители</option>
                                @foreach($manufacturers = \App\Models\Manufacturer::orderBy('name')->get() as $manufacturer)
                                    <option value="{{ $manufacturer->id }}" {{ request('manufacturer') == $manufacturer->id ? 'selected' : '' }}>
                                        {{ $manufacturer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-gray w-100">
                                <i class="fas fa-search me-2"></i>Найти
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Таблица товаров -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-body p-0">
                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Название</th>
                                        <th>Категория</th>
                                        <th>Производитель</th>
                                        <th class="text-center">Общее количество</th>
                                        <th class="text-center">Склады</th>
                                        <th>Дата создания</th>
                                        <th class="text-end">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-2 me-3">
                                                    <i class="fas fa-box text-gray-500"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $product->name }}</h6>
                                                    @if($product->description)
                                                        <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $product->category->name }}</span>
                                        </td>
                                        <td>{{ $product->manufacturer->name }}</td>
                                        <td class="text-center">
                                            <span class="fw-bold {{ $product->total_quantity > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $product->total_quantity }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($product->stocks->count() > 0)
                                                <span class="badge bg-info">{{ $product->stocks->count() }} скл.</span>
                                            @else
                                                <span class="badge bg-warning">Нет на складах</span>
                                            @endif
                                        </td>
                                        <td>{{ $product->created_at->format('d.m.Y') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('products.show', $product) }}" 
                                                   class="btn btn-sm btn-outline-gray" title="Просмотр">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(auth()->user()->isAdmin())
                                                <a href="{{ route('products.edit', $product) }}" 
                                                   class="btn btn-sm btn-outline-gray" title="Редактировать">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('products.destroy', $product) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Удалить" 
                                                            onclick="return confirm('Удалить товар?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Пагинация -->
                        <div class="card-footer bg-transparent">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Товары не найдены</h5>
                            <p class="text-muted">Начните с добавления первого товара</p>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('products.create') }}" class="btn btn-gray">
                                    <i class="fas fa-plus me-2"></i>Добавить товар
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection