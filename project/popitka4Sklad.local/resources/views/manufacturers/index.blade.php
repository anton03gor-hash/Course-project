{{-- resources/views/manufacturers/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Управление производителями')

@section('content')
<div class="container py-4">
    <!-- Заголовок и кнопки -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold mb-1">
                        <i class="fas fa-industry me-2"></i>Производители
                    </h1>
                    <p class="text-muted mb-0">Управление производителями товаров</p>
                </div>
                <div>
                    <a href="{{ route('products.index') }}" class="btn btn-gray me-2">
                        <i></i>Товары
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-gray me-2">
                        <i></i>Категории
                    </a>
                    <a href="{{ route('manufacturers.create') }}" class="btn btn-gray">
                        <i class="fas fa-plus me-2"></i>Добавить производителя
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Уведомления -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Фильтры и поиск -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-custom p-3">
                <form action="{{ route('manufacturers.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Поиск по названию..." value="{{ request('search') }}">
                        </div>
                        
                        <div class="col-md-3">
                            <select name="country" class="form-control">
                                <option value="">Все страны</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>
                                        {{ $country }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <select name="city" class="form-control">
                                <option value="">Все города</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                        {{ $city }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-outline-gray w-100">
                                <i class="fas fa-search me-2"></i>Найти
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Таблица производителей -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-body p-0">
                    @if($manufacturers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Производитель</th>
                                        <th>Адрес</th>
                                        <th class="text-center">Товаров</th>
                                        <th class="text-center">Статус</th>
                                        <th class="text-end">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($manufacturers as $manufacturer)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded p-2 me-3">
                                                        <i class="fas fa-industry text-gray-500"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ $manufacturer->name }}</h6>
                                                        <small class="text-muted">ID: {{ $manufacturer->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-0 small">{{ $manufacturer->full_address }}</p>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark fs-6">{{ $manufacturer->products_count }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($manufacturer->products_count > 0)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Активен
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>Нет товаров
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="{{ route('manufacturers.show', $manufacturer) }}" 
                                                       class="btn btn-sm btn-outline-gray" title="Просмотр">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('manufacturers.edit', $manufacturer) }}" 
                                                       class="btn btn-sm btn-outline-gray" title="Редактировать">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('manufacturers.destroy', $manufacturer) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                title="Удалить"
                                                                onclick="return confirm('Удалить производителя «{{ $manufacturer->name }}»?')">
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
                            {{ $manufacturers->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-industry fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Производители не найдены</h5>
                            <p class="text-muted">Начните с добавления первого производителя</p>
                            <a href="{{ route('manufacturers.create') }}" class="btn btn-gray">
                                <i class="fas fa-plus me-2"></i>Добавить производителя
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    
</div>
@endsection