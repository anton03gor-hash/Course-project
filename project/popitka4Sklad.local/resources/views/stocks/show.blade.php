{{-- resources/views/stocks/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Остаток #' . $stock->id)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Заголовок и действия -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb ">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('stocks.index') }}">Остатки</a></li>
                            <li class="breadcrumb-item active">#{{ $stock->id }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 fw-bold mb-1">Остаток #{{ $stock->id }}</h1>
                    <p class="text-muted mb-0">Детальная информация об остатке товара</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('stocks.index') }}" class="btn btn-outline-gray">
                        <i class="fas fa-arrow-left me-2"></i>Назад
                    </a>
                    <a href="{{ route('stocks.edit', $stock) }}" class="btn btn-outline-gray">
                        <i class="fas fa-edit me-2"></i>Редактировать
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Основная информация -->
                <div class="col-lg-8 mb-4">
                    <div class="card-custom h-100">
                        <div class="card-header bg-transparent border-bottom-0 px-3 py-2">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-info-circle me-2"></i>Основная информация
                            </h5>
                        </div>
                        <div class="card-body px-3 py-2">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Товар</label>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded p-2 me-3">
                                            <i class="fas fa-box text-gray-500"></i>
                                        </div>
                                        <div>
                                            <p class="fw-bold mb-1">{{ $stock->product->name }}</p>
                                            <p class="text-muted mb-0 small">
                                                {{ $stock->product->category->name }} • {{ $stock->product->manufacturer->name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Склад</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-warehouse text-gray-400 me-2"></i>
                                        <div>
                                            <p class="fw-bold mb-1">{{ $stock->warehouse->name }}</p>
                                            <p class="text-muted mb-0 small">
                                                {{ $stock->warehouse->city }}, {{ $stock->warehouse->street }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Количество</label>
                                    <p class="fw-bold fs-3 
                                        @if($stock->quantity == 0) text-danger
                                        @elseif($stock->quantity < 10) text-warning
                                        @else text-success @endif">
                                        {{ $stock->quantity }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Позиция на складе</label>
                                    <p class="fw-bold">{{ $stock->position ?? '—' }}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Статус</label>
                                    <p>
                                        @if($stock->quantity == 0)
                                            <span class="badge bg-danger fs-6">
                                                <i class="fas fa-times me-1"></i>Нет в наличии
                                            </span>
                                        @elseif($stock->quantity < 10)
                                            <span class="badge bg-warning fs-6">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Мало осталось
                                            </span>
                                        @else
                                            <span class="badge bg-success fs-6">
                                                <i class="fas fa-check me-1"></i>В наличии
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Последнее обновление</label>
                                    <p>{{ $stock->last_update->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>

                            @if($stock->product->description)
                            <div class="mb-3">
                                <label class="form-label text-muted">Описание товара</label>
                                <p class="fw-light">{{ $stock->product->description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Статистика и действия -->
                <div class="col-lg-4 mb-4 ">
                    <div class="card-custom h-100 px-3 py-2">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Информация
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
                                    <span>ID остатка</span>
                                    <span class="badge bg-primary">#{{ $stock->id }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Дата создания</span>
                                    <span>{{ $stock->created_at->format('d.m.Y') }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Категория</span>
                                    <span class="badge bg-info">{{ $stock->product->category->name }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Производитель</span>
                                    <span>{{ $stock->product->manufacturer->name }}</span>
                                </div>
                            </div>

                            
                        </div>
                    </div>
                </div>
            </div>

            <!-- История перемещений -->
            @if($stock->product->movements->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card-custom">
                        <div class="card-header bg-transparent border-bottom-0 px-3 py-2">
                            <h5 class="fw-bold mb-0">
                                <i></i>История перемещений товара
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th class="px-3">ID перемещения</th>
                                            <th>Из склада</th>
                                            <th>В склад</th>
                                            <th class="text-center">Количество</th>
                                            <th>Статус</th>
                                            <th>Дата</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stock->product->movements->sortByDesc('created_at')->take(5) as $movement)
                                        <tr>
                                            <td class="fw-bold px-3">#{{ $movement->id }}</td>
                                            <td>{{ $movement->fromWarehouse->name }}</td>
                                            <td>{{ $movement->toWarehouse->name }}</td>
                                            <td class="text-center">{{ $movement->quantity }}</td>
                                            <td>
                                                <span class="badge {{ $movement->status == 'complete' ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $movement->status == 'complete' ? 'Выполнено' : 'В процессе' }}
                                                </span>
                                            </td>
                                            <td>{{ $movement->created_at->format('d.m.Y H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection