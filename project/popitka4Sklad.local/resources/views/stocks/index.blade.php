{{-- resources/views/stocks/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Управление остатками')

@section('content')
<div class="container py-4">
    <!-- Заголовок и кнопки -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold mb-1">
                        <i></i>Остатки на складах
                    </h1>
                    <p class="text-muted mb-0">Управление товарными остатками на складах</p>
                </div>
                <div>
                    <a href="{{ route('stocks.create') }}" class="btn btn-gray">
                        <i class="fas fa-plus me-2"></i>Добавить остаток
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Фильтры и поиск -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card-custom p-3">
            <form action="{{ route('stocks.index') }}" method="GET" id="stockFilterForm">
                <div class="row g-3 align-items-end">
                    <!-- Поиск -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Поиск</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Название товара" value="{{ request('search') }}"
                               onchange="document.getElementById('stockFilterForm').submit()">
                    </div>
                    
                    <!-- Склад -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Склад</label>
                        <select name="warehouse" class="form-select" onchange="document.getElementById('stockFilterForm').submit()">
                            <option value="">Все склады</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ request('warehouse') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Товар -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Товар</label>
                        <select name="product" class="form-select" onchange="document.getElementById('stockFilterForm').submit()">
                            <option value="">Все товары</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Категория -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Категория</label>
                        <select name="category" class="form-select" onchange="document.getElementById('stockFilterForm').submit()">
                            <option value="">Все категории</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Статус -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Статус</label>
                        <select name="status" class="form-select" onchange="document.getElementById('stockFilterForm').submit()">
                            <option value="">Все статусы</option>
                            <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>В наличии</option>
                            <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Мало осталось</option>
                            <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Нет в наличии</option>
                            <option value="with_stock" {{ request('status') == 'with_stock' ? 'selected' : '' }}>Есть остаток</option>
                        </select>
                    </div>
                                        
                    <!-- Кнопки -->
                    <div class="col-md-12 mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if(request()->hasAny(['search', 'warehouse', 'product', 'category', 'status', 'quantity_from', 'quantity_to']))
                                    <small class="text-muted">
                                        Найдено: {{ $stocks->total() }} позиций
                                    </small>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                
                                @if(request()->hasAny(['search', 'warehouse', 'product', 'category', 'status', 'quantity_from', 'quantity_to']))
                                    <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Сбросить
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Таблица остатков -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-body p-0">
                    @if($stocks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Товар</th>
                                        <th>Склад</th>
                                        <th class="text-center">Количество</th>
                                        <th>Позиция</th>
                                        <th>Последнее обновление</th>
                                        <th class="text-end">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stocks as $stock)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-2 me-3">
                                                    <i class="fas fa-box text-gray-500"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $stock->product->name }}</h6>
                                                    <small class="text-muted">
                                                        {{ $stock->product->category->name }} • {{ $stock->product->manufacturer->name }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-warehouse text-gray-400 me-2"></i>
                                                {{ $stock->warehouse->name }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $stock->warehouse->city }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold 
                                                @if($stock->quantity == 0) text-danger
                                                @elseif($stock->quantity < 10) text-warning
                                                @else text-success @endif">
                                                {{ $stock->quantity }}
                                            </span>
                                            @if($stock->quantity < 10 && $stock->quantity > 0)
                                                <div class="small text-warning">Мало осталось</div>
                                            @endif
                                        </td>
                                        <td>{{ $stock->position ?? '—' }}</td>
                                        <td>{{ $stock->last_update->format('d.m.Y H:i') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('stocks.show', $stock) }}" 
                                                   class="btn btn-sm btn-outline-gray" title="Просмотр">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('stocks.edit', $stock) }}" 
                                                   class="btn btn-sm btn-outline-gray" title="Редактировать">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('stocks.destroy', $stock) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Удалить" 
                                                            onclick="return confirm('Удалить остаток?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Пагинация -->
                        <div class="card-footer bg-transparent">
                            {{ $stocks->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Остатки не найдены</h5>
                            <p class="text-muted">Начните с добавления первого остатка</p>
                            <a href="{{ route('stocks.create') }}" class="btn btn-gray">
                                <i class="fas fa-plus me-2"></i>Добавить остаток
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Статистика -->
    <div class="row mt-4">
        <div class="col-12 ">
            <div class="card-custom px-3 py-2">
                <div class="card-header bg-transparent border-bottom-0  py-2">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Статистика остатков
                    </h5>
                </div>
                <div class="card-body ">
                    <div class="row text-center ">
                        @php
                            $totalStocks = $stocks->count();
                            $inStock = $stocks->where('quantity', '>', 0)->count();
                            $lowStock = $stocks->where('quantity', '<', 10)->where('quantity', '>', 0)->count();
                            $outOfStock = $stocks->where('quantity', 0)->count();
                        @endphp
                        <div class="col-md-3 ">
                            <div class="card-custom bg-light px-3 py-2">
                                <div class="card-body">
                                    <i class="fas fa-boxes fa-2x text-primary mb-2"></i>
                                    <h3 class="fw-bold text-primary">{{ $totalStocks }}</h3>
                                    <p class="text-muted mb-0">Всего позиций</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-custom bg-light px-3 py-2">
                                <div class="card-body">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h3 class="fw-bold text-success">{{ $inStock }}</h3>
                                    <p class="text-muted mb-0">В наличии</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-custom bg-light px-3 py-2">
                                <div class="card-body">
                                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                                    <h3 class="fw-bold text-warning">{{ $lowStock }}</h3>
                                    <p class="text-muted mb-0">Мало осталось</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-custom bg-light px-3 py-2">
                                <div class="card-body">
                                    <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                    <h3 class="fw-bold text-danger">{{ $outOfStock }}</h3>
                                    <p class="text-muted mb-0">Нет в наличии</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection