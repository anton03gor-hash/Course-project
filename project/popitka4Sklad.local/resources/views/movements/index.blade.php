{{-- resources/views/movements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Управление перемещениями')

@section('content')
<div class="container py-4">
    <!-- Заголовок и кнопки -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold mb-1">
                        <i></i>Перемещения
                    </h1>
                    <p class="text-muted mb-0">
                        @if($user->isAdmin())
                            Все перемещения системы
                        @else
                            Активные перемещения для выполнения
                        @endif
                    </p>
                </div>
                @if($user->isAdmin())
                <div>
                    <a href="{{ route('movements.create') }}" class="btn btn-gray">
                        <i class="fas fa-plus me-2"></i>Новое перемещение
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
            <form action="{{ route('movements.index') }}" method="GET" id="movementFilterForm">
                <div class="row g-3 align-items-end">
                    <!-- Статус -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Статус</label>
                        <select name="status" class="form-select" onchange="document.getElementById('movementFilterForm').submit()">
                            <option value="">Все статусы</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>В процессе</option>
                            <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Выполнено</option>
                        </select>
                    </div>
                    
                    <!-- Тип -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Тип</label>
                        <select name="type" class="form-select" onchange="document.getElementById('movementFilterForm').submit()">
                            <option value="">Все типы</option>
                            <option value="between_warehouses" {{ request('type') == 'between_warehouses' ? 'selected' : '' }}>Между складами</option>
                            <option value="for_order" {{ request('type') == 'for_order' ? 'selected' : '' }}>Для заявки</option>
                        </select>
                    </div>
                    
                    <!-- Склад -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Склад</label>
                        <select name="warehouse" class="form-select" onchange="document.getElementById('movementFilterForm').submit()">
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
                        <select name="product" class="form-select" onchange="document.getElementById('movementFilterForm').submit()">
                            <option value="">Все товары</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Даты -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Дата с</label>
                        <input type="date" name="date_from" class="form-control" 
                               value="{{ request('date_from') }}"
                               onchange="document.getElementById('movementFilterForm').submit()">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Дата по</label>
                        <input type="date" name="date_to" class="form-control" 
                               value="{{ request('date_to') }}"
                               onchange="document.getElementById('movementFilterForm').submit()">
                    </div>
                    
                    <!-- Кнопки -->
                    <div class="col-md-12 mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if(request()->hasAny(['status', 'type', 'warehouse', 'product', 'date_from', 'date_to']))
                                    <small class="text-muted">
                                        Найдено: {{ $movements->total() }} перемещений
                                    </small>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                
                                @if(request()->hasAny(['status', 'type', 'warehouse', 'product', 'date_from', 'date_to']))
                                    <a href="{{ route('movements.index') }}" class="btn btn-outline-secondary">
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



    <!-- Таблица перемещений -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-body p-0">
                    @if($movements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Товар</th>
                                        <th>Из склада</th>
                                        <th>В склад</th>
                                        <th class="text-center">Количество</th>
                                        <th>Тип</th>
                                        <th>Статус</th>
                                        <th>Дата создания</th>
                                        <th class="text-end">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($movements as $movement)
                                    <tr>
                                        <td class="fw-bold">#{{ $movement->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-2 me-2">
                                                    <i class="fas fa-box text-gray-500"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $movement->product->name }}</div>
                                                    <small class="text-muted">{{ $movement->product->category->name }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-warehouse text-danger me-2"></i>
                                                {{ $movement->fromWarehouse->name }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-warehouse text-success me-2"></i>
                                                {{ $movement->toWarehouse->name }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold text-primary">{{ $movement->quantity }}</span>
                                        </td>
                                        <td>
                                            @if($movement->type == 'for_order')
                                                <span class="badge bg-info">
                                                    <i></i>Для заявки
                                                </span>
                                                @if($movement->order)
                                                    <small class="d-block text-muted">#{{ $movement->order->id }}</small>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i></i>Между складами
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($movement->status == 'complete')
                                                <span class="badge bg-success">
                                                    <i></i>Выполнено
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i></i>В процессе
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $movement->created_at->format('d.m.Y H:i') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('movements.show', $movement) }}" 
                                                   class="btn btn-sm btn-outline-gray" title="Просмотр">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($user->isAdmin())
                                                <a href="{{ route('movements.edit', $movement) }}" 
                                                   class="btn btn-sm btn-outline-gray" title="Редактировать">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endif
                                                @if($movement->status == 'in_progress' && ($user->isAdmin() || $user->isEmployee()))
                                                <form action="{{ route('movements.complete', $movement) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success" 
                                                            title="Отметить выполненным">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                @endif
                                                @if($user->isAdmin() && $movement->status == 'in_progress')
                                                <form action="{{ route('movements.destroy', $movement) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Удалить" 
                                                            onclick="return confirm('Удалить перемещение?')">
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
                            {{ $movements->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-truck-moving fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Перемещения не найдены</h5>
                            <p class="text-muted">
                                @if($user->isAdmin())
                                    Начните с создания первого перемещения
                                @else
                                    Нет активных перемещений для выполнения
                                @endif
                            </p>
                            @if($user->isAdmin())
                                <a href="{{ route('movements.create') }}" class="btn btn-gray">
                                    <i class="fas fa-plus me-2"></i>Создать перемещение
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