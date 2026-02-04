{{-- resources/views/orders/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Управление заявками')

@section('content')
<div class="container py-4">
    <!-- Заголовок и кнопки -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold mb-1">
                        <i></i>Заявки
                    </h1>
                    <p class="text-muted mb-0">
                        @if($user->isAdmin())
                            Все заявки системы
                        @elseif($user->isEmployee())
                            Заявки клиентов для обработки
                        @else
                            Мои заявки
                        @endif
                    </p>
                </div>
                @if($user->isClient() || $user->isAdmin())
                <div>
                    <a href="{{ route('orders.create') }}" class="btn btn-gray">
                        <i class="fas fa-plus me-2"></i>Новая заявка
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Фильтры -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-custom p-3">
                <form action="{{ route('orders.index') }}" method="GET" id="orderFilterForm">
                    <div class="row g-3 align-items-end">
                        <!-- Поиск по ID -->
                        <div class="col-md-2">
                            <label class="form-label small text-muted">ID заявки</label>
                            <input type="number" name="search" class="form-control" 
                                placeholder="№ заявки" value="{{ request('search') }}"
                                onchange="document.getElementById('orderFilterForm').submit()">
                        </div>
                        
                        <!-- Статус -->
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Статус</label>
                            <select name="status" class="form-select" onchange="document.getElementById('orderFilterForm').submit()">
                                <option value="">Все статусы</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Ожидает</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>В работе</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Выполнено</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Отменено</option>
                            </select>
                        </div>
                        
                        <!-- Склад -->
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Склад</label>
                            <select name="warehouse" class="form-select" onchange="document.getElementById('orderFilterForm').submit()">
                                <option value="">Все склады</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ request('warehouse') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Клиент (только для админов и сотрудников) -->
                        @if($user->isAdmin() || $user->isEmployee())
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Клиент</label>
                            <select name="user" class="form-select" onchange="document.getElementById('orderFilterForm').submit()">
                                <option value="">Все клиенты</option>
                                @foreach($users as $client)
                                    <option value="{{ $client->id }}" {{ request('user') == $client->id ? 'selected' : '' }}>
                                        {{ $client->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        
                        <!-- Даты -->
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Дата с</label>
                            <input type="date" name="date_from" class="form-control" 
                                value="{{ request('date_from') }}"
                                onchange="document.getElementById('orderFilterForm').submit()">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Дата по</label>
                            <input type="date" name="date_to" class="form-control" 
                                value="{{ request('date_to') }}"
                                onchange="document.getElementById('orderFilterForm').submit()">
                        </div>
                        
                        <!-- Кнопки -->
                        <div class="col-md-12 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if(request()->hasAny(['search', 'status', 'warehouse', 'user', 'date_from', 'date_to']))
                                        <small class="text-muted">
                                            Найдено: {{ $orders->total() }} заявок
                                        </small>
                                    @endif
                                </div>
                                <div class="d-flex gap-2">
                                    
                                    @if(request()->hasAny(['search', 'status', 'warehouse', 'user', 'date_from', 'date_to']))
                                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
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
    <!-- Таблица заявок -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-body p-0">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        @if($user->isAdmin() || $user->isEmployee())
                                        <th>Клиент</th>
                                        @endif
                                        <th>Склад</th>
                                        <th class="text-center">Товаров</th>
                                        <th>Статус</th>
                                        <th>Дата создания</th>
                                        <th class="text-end">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td class="fw-bold">#{{ $order->id }}</td>
                                        @if($user->isAdmin() || $user->isEmployee())
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 32px; height: 32px;">
                                                    <i class="fas fa-user text-gray-500"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $order->user->full_name }}</div>
                                                    <small class="text-muted">{{ $order->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        @endif
                                        <td>{{ $order->warehouse->name }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">
                                                {{ $order->products->count() }} шт.
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'confirmed' => 'info',
                                                    'rejected' => 'danger',
                                                    'completed' => 'success',
                                                    'received' => 'info'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$order->status] }}">
                                                @if($order->status == 'pending') Ожидает
                                                @elseif($order->status == 'confirmed') Подтверждена
                                                @elseif($order->status == 'rejected') Отклонена
                                                @elseif($order->status == 'completed') Выполнена
                                                @elseif($order->status == 'received') Получен
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('orders.show', $order) }}" 
                                                   class="btn btn-sm btn-outline-gray" title="Просмотр">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(!($order->status!='pending' && $user->isClient() || $user->isEmployee()))
                                                <a href="{{ route('orders.edit', $order) }}" 
                                                   class="btn btn-sm btn-outline-gray" title="Редактировать">
                                                    <i class="fas fa-edit"></i>
                                                </a>@endif
                                                @if($user->isAdmin())
                                                <form action="{{ route('orders.destroy', $order) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Удалить" 
                                                            onclick="return confirm('Удалить заявку?')">
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
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Заявки не найдены</h5>
                            <p class="text-muted">
                                @if($user->isClient())
                                    У вас пока нет заявок
                                @else
                                    Заявки отсутствуют
                                @endif
                            </p>
                            @if($user->isClient() || $user->isAdmin())
                                <a href="{{ route('orders.create') }}" class="btn btn-gray">
                                    <i class="fas fa-plus me-2"></i>Создать заявку
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