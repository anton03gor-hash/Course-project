{{-- resources/views/categories/show.blade.php --}}
@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="container py-4">
    <!-- Заголовок и действия -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Категории</a></li>
                            <li class="breadcrumb-item active">{{ $category->name }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 fw-bold mb-1">
                        <i class="fas fa-tag me-2"></i>{{ $category->name }}
                    </h1>
                    <p class="text-muted mb-0">
                        @if($category->description)
                            {{ $category->description }}
                        @else
                            Детальная информация о категории
                        @endif
                    </p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-gray">
                        <i class="fas fa-arrow-left me-2"></i>Назад
                    </a>
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-gray">
                        <i class="fas fa-edit me-2"></i>Редактировать
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Основная информация -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card-custom h-100 py-2 px-3">
                <div class="card-header bg-transparent border-bottom-0">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-info-circle me-2"></i>Информация о категории
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Название категории</label>
                            <p class="fw-bold fs-5">{{ $category->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">ID категории</label>
                            <p class="fw-bold">#{{ $category->id }}</p>
                        </div>
                    </div>

                    @if($category->description)
                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label text-muted">Описание</label>
                            <p class="fw-light">{{ $category->description }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Дата создания</label>
                            <p>{{ $category->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Последнее обновление</label>
                            <p>{{ $category->updated_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Статистика -->
        <div class="col-lg-4 mb-4">
            <div class="card-custom h-100  py-2 px-3">
                <div class="card-header bg-transparent border-bottom-0">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Статистика
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px;">
                            <i class="fas fa-tag fa-2x text-gray-500"></i>
                        </div>
                    </div>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Товаров в категории</span>
                            <span class="badge bg-primary">{{ $products->total() }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Дата создания</span>
                            <span>{{ $category->created_at->format('d.m.Y') }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Статус</span>
                            @if($products->total() > 0)
                                <span class="badge bg-success">Активна</span>
                            @else
                                <span class="badge bg-warning">Нет товаров</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Товары в категории -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom py-2 px-3">
                <div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fas"></i>Товары в категории
                    </h5>
                    <a href="{{ route('products.create') }}?category_id={{ $category->id }}" class="btn btn-sm btn-gray">
                        <i class="fas fa-plus me-1"></i>Добавить товар
                    </a>
                </div>
                <div class="card-body">
                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Товар</th>
                                        <th>Производитель</th>
                                        <th class="text-center">SKU</th>
                                        <th class="text-center">На складах</th>
                                        <th class="text-center">Общее количество</th>
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
                                                        <small class="text-muted">
                                                            @if($product->description)
                                                                {{ Str::limit($product->description, 50) }}
                                                            @else
                                                                Без описания
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $product->manufacturer->name }}</td>
                                            <td class="text-center">
                                                <code>{{ $product->sku ?? '—' }}</code>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark">{{ $product->stocks->count() }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold">{{ $product->stocks->sum('quantity') }}</span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="{{ route('products.show', $product) }}" 
                                                       class="btn btn-sm btn-outline-gray" title="Просмотр">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('products.edit', $product) }}" 
                                                       class="btn btn-sm btn-outline-gray" title="Редактировать">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
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
                        <div class="text-center py-4">
                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">В категории нет товаров</h5>
                            <p class="text-muted">Добавьте первый товар в эту категорию</p>
                            <a href="{{ route('products.create') }}?category_id={{ $category->id }}" class="btn btn-gray">
                                <i class="fas fa-plus me-2"></i>Добавить товар
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection