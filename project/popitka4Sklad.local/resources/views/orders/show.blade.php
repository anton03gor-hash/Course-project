{{-- resources/views/orders/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Заявка #' . $order->id)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 ">
            <!-- Заголовок и действия -->
            <div class="d-flex justify-content-between align-items-center mb-4 ">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Заявки</a></li>
                            <li class="breadcrumb-item active">#{{ $order->id }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 fw-bold mb-1">Заявка #{{ $order->id }}</h1>
                    <p class="text-muted mb-0">Детальная информация о заявке</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-gray">
                        <i class="fas fa-arrow-left me-2"></i>Назад
                    </a>
                    @if(!($order->status!='pending' && auth()->user()->isClient() || auth()->user()->isEmployee()))
                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-gray">
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
                                    <label class="form-label text-muted">Номер заявки</label>
                                    <p class="fw-bold">#{{ $order->id }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Статус</label>
                                    <p>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'confirmed' => 'info',
                                                'rejected' => 'danger',
                                                'completed' => 'success',
                                                'received' => 'info'
                                            ];
                                            $statusText = [
                                                'pending' => 'Ожидает',
                                                'confirmed' => 'Подтверждена',
                                                'rejected' => 'Отклонена',
                                                'completed' => 'Выполнена',
                                                'received' => 'Получен'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$order->status] }} fs-6">
                                            {{ $statusText[$order->status] }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Клиент</label>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-gray-500"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $order->user->full_name }}</div>
                                            <small class="text-muted">{{ $order->user->email }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Склад получения</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-warehouse text-gray-400 me-2"></i>
                                        <div>
                                            <div class="fw-bold">{{ $order->warehouse->name }}</div>
                                            <small class="text-muted">
                                                {{ $order->warehouse->city }}, {{ $order->warehouse->street }}, {{$order->warehouse->house_number}}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($order->notes)
                            <div class="mb-3">
                                <label class="form-label text-muted">Примечание</label>
                                <p class="fw-light bg-light p-3 rounded">{{ $order->notes }}</p>
                            </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Дата создания</label>
                                    <p>{{ $order->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Последнее обновление</label>
                                    <p>{{ $order->updated_at->format('d.m.Y H:i') }}</p>
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
                                    <i class="fas fa-clipboard-list fa-2x text-gray-500"></i>
                                </div>
                            </div>
                            
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Товаров в заявке</span>
                                    <span class="badge bg-primary">{{ $order->products->count() }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Общее количество</span>
                                    <span class="badge bg-info">
                                        {{ $order->products->sum('pivot.quantity') }}
                                    </span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Перемещения</span>
                                    <span class="badge bg-warning">{{ $order->movements->count() }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Выполнено перемещений</span>
                                    <span class="badge bg-success">
                                        {{ $order->movements->where('status', 'complete')->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Товары в заявке -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card-custom px-3 py-2">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h5 class="fw-bold mb-0">
                                <i class="me-2"></i>Товары в заявке
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($order->products->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Товар</th>
                                                <th>Категория</th>
                                                <th>Производитель</th>
                                                <th class="text-center">Количество</th>
                                                <th class="text-center">Доступно на складах</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->products as $product)
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
                                                    <span class="fw-bold text-primary">{{ $product->pivot->quantity }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="fw-bold {{ $product->total_quantity >= $product->pivot->quantity ? 'text-success' : 'text-danger' }}">
                                                        {{ $product->total_quantity }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-boxes fa-2x text-muted mb-3"></i>
                                    <h5 class="text-muted">Товары отсутствуют</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Перемещения -->
            @if($order->movements->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card-custom px-3 py-2">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h5 class="fw-bold mb-0">
                                <i class=" me-2"></i>Связанные перемещения
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Товар</th>
                                            <th>Из склада</th>
                                            <th>В склад</th>
                                            <th class="text-center">Количество</th>
                                            <th>Статус</th>
                                            <th>Дата создания</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->movements as $movement)
                                        <tr>
                                            <td>{{ $movement->product->name }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-warehouse text-gray-400 me-2"></i>
                                                    {{ $movement->fromWarehouse->name }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-warehouse text-gray-400 me-2"></i>
                                                    {{ $movement->toWarehouse->name }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold">{{ $movement->quantity }}</span>
                                            </td>
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