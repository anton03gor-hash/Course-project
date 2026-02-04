{{-- resources/views/categories/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Управление категориями')

@section('content')
<div class="container py-4">
    <!-- Заголовок и кнопки -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold mb-1">
                        <i></i>Категории товаров
                    </h1>
                    <p class="text-muted mb-0">Управление категориями товарной номенклатуры</p>
                </div>
                <div>
                    <a href="{{ route('products.index') }}" class="btn btn-gray me-2">
                        <i></i>Товары
                    </a>
                    <a href="{{ route('categories.create') }}" class="btn btn-gray">
                        <i class="fas fa-plus me-2"></i>Добавить категорию
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
                <form action="{{ route('categories.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Поиск по названию..." value="{{ request('search') }}">
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

    <!-- Таблица категорий -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-body p-0">
                    @if($categories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Название</th>
                                        <th>Описание</th>
                                        <th class="text-center">Товаров</th>
                                        <th class="text-center">Статус</th>
                                        <th class="text-end">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded p-2 me-3">
                                                        <i class="fas fa-tag text-gray-500"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ $category->name }}</h6>
                                                        <small class="text-muted">ID: {{ $category->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($category->description)
                                                    <p class="mb-0 text-muted small">{{ Str::limit($category->description, 60) }}</p>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark fs-6">{{ $category->products_count }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($category->products_count > 0)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Активна
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>Нет товаров
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="{{ route('categories.show', $category) }}" 
                                                       class="btn btn-sm btn-outline-gray" title="Просмотр">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('categories.edit', $category) }}" 
                                                       class="btn btn-sm btn-outline-gray" title="Редактировать">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('categories.destroy', $category) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                title="Удалить"
                                                                onclick="return confirm('Удалить категорию «{{ $category->name }}»?')">
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
                            {{ $categories->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Категории не найдены</h5>
                            <p class="text-muted">Начните с добавления первой категории</p>
                            <a href="{{ route('categories.create') }}" class="btn btn-gray">
                                <i class="fas fa-plus me-2"></i>Добавить категорию
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Статистика -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card-custom px-2 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Статистика категорий
                    </h5>
                </div>
                <div class="card-body py-2">
                    <div class="row text-center">
                        @php
                            $totalCategories = $categories->count();
                            $totalProducts = $categories->sum('products_count');
                            $activeCategories = $categories->where('products_count', '>', 0)->count();
                            $emptyCategories = $totalCategories - $activeCategories;
                        @endphp
                        <div class="col-md-3">
                            <div class="card-custom bg-light">
                                <div class="card-body py-2">
                                    <i class="fas fa-tags fa-2x text-primary mb-2"></i>
                                    <h3 class="fw-bold text-primary">{{ $totalCategories }}</h3>
                                    <p class="text-muted mb-0">Всего категорий</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-custom bg-light">
                                <div class="card-body py-2">
                                    <i class="fas fa-boxes fa-2x text-success mb-2"></i>
                                    <h3 class="fw-bold text-success">{{ $totalProducts }}</h3>
                                    <p class="text-muted mb-0">Всего товаров</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-custom bg-light">
                                <div class="card-body py-2">
                                    <i class="fas fa-check-circle fa-2x text-info mb-2"></i>
                                    <h3 class="fw-bold text-info">{{ $activeCategories }}</h3>
                                    <p class="text-muted mb-0">Активных категорий</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-custom bg-light">
                                <div class="card-body py-2">
                                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                    <h3 class="fw-bold text-warning">{{ $emptyCategories }}</h3>
                                    <p class="text-muted mb-0">Пустых категорий</p>
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