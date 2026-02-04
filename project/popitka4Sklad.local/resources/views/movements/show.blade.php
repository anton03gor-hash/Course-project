{{-- resources/views/movements/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Перемещение #' . $movement->id)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Заголовок и действия -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('movements.index') }}">Перемещения</a></li>
                            <li class="breadcrumb-item active">#{{ $movement->id }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 fw-bold mb-1">Перемещение #{{ $movement->id }}</h1>
                    <p class="text-muted mb-0">Детальная информация о перемещении</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('movements.index') }}" class="btn btn-outline-gray">
                        <i class="fas fa-arrow-left me-2"></i>Назад
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('movements.edit', $movement) }}" class="btn btn-outline-gray">
                        <i class="fas fa-edit me-2"></i>Редактировать
                    </a>
                    @endif
                    @if($movement->status == 'in_progress' && (auth()->user()->isAdmin() || auth()->user()->isEmployee()))
                    <form action="{{ route('movements.complete', $movement) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Выполнить
                        </button>
                    </form>
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
                                    <label class="form-label text-muted">Номер перемещения</label>
                                    <p class="fw-bold">#{{ $movement->id }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Статус</label>
                                    <p>
                                        @if($movement->status == 'complete')
                                            <span class="badge bg-success fs-6">
                                                <i class="fas fa-check me-1"></i>Выполнено
                                            </span>
                                        @else
                                            <span class="badge bg-warning fs-6">
                                                <i class="fas fa-sync-alt me-1"></i>В процессе
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Тип перемещения</label>
                                    <p>
                                        @if($movement->type == 'for_order')
                                            <span class="badge bg-info">
                                                <i class="fas fa-clipboard-list me-1"></i>Для заявки
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-exchange-alt me-1"></i>Между складами
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Количество</label>
                                    <p class="fw-bold fs-5 text-primary">{{ $movement->quantity }}</p>
                                </div>
                            </div>

                            <!-- Информация о заявке -->
                            @if($movement->order)
                            <div class="mb-3 p-3 bg-light rounded">
                                <label class="form-label text-muted fw-bold">Связанная заявка</label>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-1 fw-bold">Заявка #{{ $movement->order->id }}</p>
                                        <p class="mb-1 text-muted">Клиент: {{ $movement->order->user->full_name }}</p>
                                        <p class="mb-0 text-muted">Склад: {{ $movement->order->warehouse->name }}</p>
                                    </div>
                                    <a href="{{ route('orders.show', $movement->order) }}" class="btn btn-sm btn-outline-gray">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Дата создания</label>
                                    <p>{{ $movement->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Последнее обновление</label>
                                    <p>{{ $movement->updated_at->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Статистика и действия -->
                <div class="col-lg-4 mb-4">
                    <div class="card-custom h-100 px-3 py-2">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Информация
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                
                            </div>
                            
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Товар</span>
                                    <span class="badge bg-primary">{{ $movement->product->name }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Категория</span>
                                    <span class="badge bg-info">{{ $movement->product->category->name }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Производитель</span>
                                    <span>{{ $movement->product->manufacturer->name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Детали перемещения -->
            <div class="row">
                <div class="col-12">
                    <div class="card-custom px-3 py-2">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h5 class="fw-bold mb-0">
                                <i></i>Маршрут перемещения
                            </h5>
                        </div>
                        <div class="card-body py-2">
                            <div class="row text-center">
                                <!-- Склад-источник -->
                                <div class="col-md-4">
                                    <div class="card-custom bg-light px-3 py-2">
                                        <div class="card-body">
                                            <i class="fas fa-warehouse fa-2x text-danger mb-3"></i>
                                            <h5 class="fw-bold">Откуда</h5>
                                            <p class="mb-1 fw-bold">{{ $movement->fromWarehouse->name }}</p>
                                            <p class="text-muted small mb-1">
                                                {{ $movement->fromWarehouse->city }}, {{ $movement->fromWarehouse->street }}
                                            </p>
                                            <p class="text-muted small">
                                                {{ $movement->fromWarehouse->house_number }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Стрелка -->
                                <div class="col-md-4 d-flex align-items-center justify-content-center">
                                    <div class="text-center">
                                        <i class="fas fa-arrow-right fa-2x text-gray-400 mb-2"></i>
                                        <p class="text-muted mb-0">Перемещение</p>
                                        <p class="fw-bold text-primary">{{ $movement->quantity }} шт.</p>
                                    </div>
                                </div>

                                <!-- Склад-назначения -->
                                <div class="col-md-4">
                                    <div class="card-custom bg-light">
                                        <div class="card-body px-3 py-2">
                                            <i class="fas fa-warehouse fa-2x text-success mb-3"></i>
                                            <h5 class="fw-bold">Куда</h5>
                                            <p class="mb-1 fw-bold">{{ $movement->toWarehouse->name }}</p>
                                            <p class="text-muted small mb-1">
                                                {{ $movement->toWarehouse->city }}, {{ $movement->toWarehouse->street }}
                                            </p>
                                            <p class="text-muted small">
                                                {{ $movement->toWarehouse->house_number }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Информация о товаре -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card-custom px-3 py-2">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h5 class="fw-bold mb-0">
                                <i></i>Информация о товаре
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded p-3 me-3">
                                            <i class="fas fa-box fa-2x text-gray-500"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-1">{{ $movement->product->name }}</h5>
                                            <p class="text-muted mb-1">{{ $movement->product->category->name }}</p>
                                            <p class="text-muted mb-0">{{ $movement->product->manufacturer->name }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @if($movement->product->description)
                                    <div class="mt-3 mt-md-0">
                                        <label class="form-label text-muted">Описание</label>
                                        <p class="fw-light">{{ $movement->product->description }}</p>
                                    </div>
                                    @endif
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