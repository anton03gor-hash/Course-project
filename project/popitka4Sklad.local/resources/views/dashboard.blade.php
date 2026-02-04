{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Панель управления')

@section('content')
<div class="container py-4">
    <!-- Заголовок и приветствие -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-custom p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 fw-bold mb-1">
                            <i class="fas "></i>Панель управления
                        </h1>
                        <p class="text-muted mb-0">Добро пожаловать, {{ $user->full_name }}!</p>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Статистика -->
    <div class="row mb-4">
        @if($user->isAdmin())
            <!-- Статистика для администратора -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card-custom p-3 h-100">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Пользователи</p>
                            <h3 class="fw-bold">{{ $stats['total_users'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card-custom p-3 h-100">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Товары</p>
                            <h3 class="fw-bold">{{ $stats['total_products'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-boxes fa-2x text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card-custom p-3 h-100">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Склады</p>
                            <h3 class="fw-bold">{{ $stats['total_warehouses'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-warehouse fa-2x text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card-custom p-3 h-100">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Ожидают заявки</p>
                            <h3 class="fw-bold">{{ $stats['pending_orders'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clipboard-list fa-2x text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($user->isEmployee())
            <!-- Статистика для сотрудника -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card-custom p-3 h-100">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Ожидают перемещения</p>
                            <h3 class="fw-bold">{{ $stats['pending_movements'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-truck-loading fa-2x text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card-custom p-3 h-100">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Выполнено сегодня</p>
                            <h3 class="fw-bold">{{ $stats['completed_movements_today'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card-custom p-3 h-100">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Ожидают заявки</p>
                            <h3 class="fw-bold">{{ $stats['pending_orders'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clipboard-list fa-2x text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($user->isClient())
            <!-- Статистика для клиента -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card-custom p-3 h-100">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Всего заявок</p>
                            <h3 class="fw-bold">{{ $stats['my_orders'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4 ">
                <div class="card-custom p-3 h-100">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Ожидают</p>
                            <h3 class="fw-bold">{{ $stats['pending_orders'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card-custom p-3 h-100">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Выполнено</p>
                            <h3 class="fw-bold">{{ $stats['completed_orders'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Таблицы и списки -->
    <div class="row">
        @if($user->isAdmin())
            <!-- Для администратора -->
            <div class="col-lg-6 mb-4 ">
                <div class="card-custom h-100 px-3 py-2">
                    <div class="card-header bg-transparent border-bottom-0">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-clock me-2"></i>Последние заявки
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($stats['recent_orders']->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($stats['recent_orders'] as $order)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Заявка #{{ $order->id }}</h6>
                                            <small class="text-muted">{{ $order->user->full_name }} • {{ $order->warehouse->name }}</small>
                                        </div>
                                        <span class="badge 
                                            @if($order->status == 'pending') bg-warning
                                            @elseif($order->status == 'confirmed') bg-info
                                            @elseif($order->status == 'completed') bg-success
                                            @elseif($order->status == 'received') bg-info
                                            @else bg-danger @endif">
                                            @if($order->status == 'pending') Ожидает
                                                @elseif($order->status == 'confirmed') Подтверждена
                                                @elseif($order->status == 'rejected') Отклонена
                                                @elseif($order->status == 'completed') Выполнена
                                                @elseif($order->status == 'received') Получен
                                                @endif
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">Нет заявок</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card-custom h-100 px-3 py-2">
                    <div class="card-header bg-transparent border-bottom-0">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>Товары с низким запасом
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($stats['low_stock_products']->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($stats['low_stock_products'] as $product)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $product->name }}</h6>
                                            <small class="text-muted">{{ $product->category->name }}</small>
                                        </div>
                                        <span class="badge bg-danger">Мало</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">Все товары в достаточном количестве</p>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($user->isEmployee())
            <!-- Для сотрудника -->
            <div class="col-12 mb-4">
                <div class="card-custom px-3 py-2">
                    <div class="card-header bg-transparent border-bottom-0">
                        <h5 class="fw-bold mb-0">
                            <i></i>Активные перемещения
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($stats['active_movements']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Товар</th>
                                            <th>Из склада</th>
                                            <th>В склад</th>
                                            <th>Количество</th>
                                            <th>Статус</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stats['active_movements'] as $movement)
                                        <tr>
                                            <td>{{ $movement->product->name }}</td>
                                            <td>{{ $movement->fromWarehouse->name }}</td>
                                            <td>{{ $movement->toWarehouse->name }}</td>
                                            <td>{{ $movement->quantity }}</td>
                                            <td>
                                                <span class="badge bg-warning">В процессе</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">Нет активных перемещений</p>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($user->isClient())
            <!-- Для клиента -->
            <div class="col-12 mb-4 ">
                <div class="card-custom">
                    <div class="card-header bg-transparent border-bottom-0">
                        <h5 class="fw-bold mb-0 px-3 py-2">
                            <i class="fas fa-history me-2"></i>История заявок
                        </h5>
                    </div>
                    <div class="card-body px-3 py-2">
                        @if($stats['recent_orders']->count() > 0)
                            <div class="list-group list-group-flush ">
                                @foreach($stats['recent_orders'] as $order)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Заявка #{{ $order->id }}</h6>
                                            <small class="text-muted">Склад: {{ $order->warehouse->name }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge 
                                                @if($order->status == 'pending') bg-warning
                                                @elseif($order->status == 'confirmed') bg-info
                                                @elseif($order->status == 'completed') bg-success
                                                @elseif($order->status == 'received') bg-info
                                                @else bg-danger @endif">
                                                
                                                @if($order->status == 'pending') Ожидает
                                                @elseif($order->status == 'confirmed') Подтверждена
                                                @elseif($order->status == 'rejected') Отклонена
                                                @elseif($order->status == 'completed') Выполнена
                                                @elseif($order->status == 'received') Получен
                                                @endif
                                            </span>
                                            <div class="text-muted small mt-1">
                                                {{ $order->created_at->format('d.m.Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">У вас пока нет заявок</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Быстрые действия -->
    <div class="row">
        <div class="col-12 ">
            <div class="card-custom px-3 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <h5 class="fw-bold mb-0">Быстрые действия</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 py-2">
                        @if($user->isAdmin())
                            <div class="col-auto">
                                <a href="{{ route('products.index') }}" class="btn btn-outline-gray">
                                    <i class="fas fa-boxes me-2"></i>Управление товарами
                                </a>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('users.index') }}" class="btn btn-outline-gray">
                                    <i class="fas fa-users me-2"></i>Управление пользователями
                                </a>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('orders.index') }}" class="btn btn-outline-gray">
                                    <i class="fas fa-clipboard-list me-2"></i>Все заявки
                                </a>
                            </div>
                        @elseif($user->isEmployee())
                            <div class="col-auto">
                                <a href="{{ route('movements.index') }}" class="btn btn-outline-gray">
                                    <i class="fas fa-truck me-2"></i>Перемещения
                                </a>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('orders.index') }}" class="btn btn-outline-gray">
                                    <i class="fas fa-clipboard-list me-2"></i>Заявки
                                </a>
                            </div>
                        @elseif($user->isClient())
                            <div class="col-auto">
                                <a href="{{ route('products.index') }}" class="btn btn-outline-gray">
                                    <i class="fas fa-boxes me-2"></i>Каталог товаров
                                </a>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('orders.create') }}" class="btn btn-outline-gray">
                                    <i class="fas fa-plus me-2"></i>Новая заявка
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection