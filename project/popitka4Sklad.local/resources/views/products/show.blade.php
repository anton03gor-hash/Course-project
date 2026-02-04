{{-- resources/views/products/show.blade.php --}}
@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Заголовок и действия -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Товары</a></li>
                            <li class="breadcrumb-item active">{{ $product->name }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 fw-bold mb-1">{{ $product->name }}</h1>
                    <p class="text-muted mb-0">Детальная информация о товаре</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-gray">
                        <i class="fas fa-arrow-left me-2"></i>Назад
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-gray">
                        <i class="fas fa-edit me-2"></i>Редактировать
                    </a>
                    @endif
                </div>
            </div>

            <div class="row">
                <!-- Основная информация -->
                <div class="col-lg-8 mb-4">
                    <div class="card-custom h-100 px-3 py-2">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-info-circle me-2"></i>Основная информация
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Название</label>
                                    <p class="fw-bold">{{ $product->name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Категория</label>
                                    <p>
                                        <span class="badge bg-light text-dark">{{ $product->category->name }}</span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Производитель</label>
                                    <p class="fw-bold">{{ $product->manufacturer->name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Общее количество</label>
                                    <p class="fw-bold fs-5 {{ $product->total_quantity > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $product->total_quantity }}
                                    </p>
                                </div>
                            </div>

                            @if($product->description)
                            <div class="mb-3">
                                <label class="form-label text-muted">Описание</label>
                                <p class="fw-light">{{ $product->description }}</p>
                            </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Дата создания</label>
                                    <p>{{ $product->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Последнее обновление</label>
                                    <p>{{ $product->updated_at->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Статистика -->
                <div class="col-lg-4 mb-4">
                    <div class="card-custom h-100 px-3 py-2">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Статистика
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px;">
                                    <i class="fas fa-box fa-2x text-gray-500"></i>
                                </div>
                            </div>
                            
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Склады с товаром</span>
                                    <span class="badge bg-primary">{{ $product->stocks->count() }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Заявки с товаром</span>
                                    <span class="badge bg-info">{{ $product->orders->count() }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Перемещения</span>
                                    <span class="badge bg-warning">{{ $product->movements->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Остатки на складах -->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card-custom">
                        <div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center py-2 px-3">
                            <h5 class="fw-bold mb-0">
                                <i></i>Остатки на складах
                            </h5>
                            <span class="badge bg-secondary">{{ $product->stocks->count() }} складов</span>
                        </div>
                        <div class="card-body">
                            @if($product->stocks->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Склад</th>
                                                <th>Адрес</th>
                                                <th class="text-center">Количество</th>
                                                <th>Позиция</th>
                                                <th>Последнее обновление</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($product->stocks as $stock)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class=" text-gray-400 me-2"></i>
                                                        {{ $stock->warehouse->name }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $stock->warehouse->city }}, {{ $stock->warehouse->street }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="fw-bold {{ $stock->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ $stock->quantity }}
                                                    </span>
                                                </td>
                                                <td>{{ $stock->position ?? '—' }}</td>
                                                <td>{{ $stock->last_update->format('d.m.Y H:i') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-warehouse fa-2x text-muted mb-3"></i>
                                    <h5 class="text-muted">Товар отсутствует на складах</h5>
                                    <p class="text-muted">Добавьте остатки товара на склад</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection