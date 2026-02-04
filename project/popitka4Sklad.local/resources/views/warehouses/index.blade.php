{{-- resources/views/warehouses/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Управление складами')

@section('content')
<div class="container py-4">
    <!-- Заголовок и кнопки -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold mb-1">
                        <i class="fas "></i>Склады
                    </h1>
                    <p class="text-muted mb-0">Управление складскими помещениями</p>
                </div>
                <div>
                    <a href="{{ route('warehouses.map') }}" class="btn btn-outline-gray me-2">
                        <i class="fas fa-map me-2"></i>На карте
                    </a>
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('warehouses.create') }}" class="btn btn-gray">
                        <i class="fas fa-plus me-2"></i>Добавить склад
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Таблица складов -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-body p-0">
                    @if($warehouses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Название</th>
                                        <th>Адрес</th>
                                        <th class="text-center">Товаров</th>
                                        <th class="text-center">Заявок</th>
                                        <th>Координаты</th>
                                        <th class="text-end">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($warehouses as $warehouse)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-2 me-3">
                                                    <i class="fas fa-warehouse text-gray-500"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $warehouse->name }}</h6>
                                                    <small class="text-muted">Создан: {{ $warehouse->created_at->format('d.m.Y') }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div class="fw-bold">{{ $warehouse->city }}</div>
                                                <div class="text-muted">{{ $warehouse->street }}, {{ $warehouse->house_number }}</div>
                                                <div class="text-muted">{{ $warehouse->country }}</div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">{{ $warehouse->stocks_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">{{ $warehouse->orders_count }}</span>
                                        </td>
                                        <td>
                                            @if($warehouse->hasCoordinates())
                                                <span class="badge bg-success">
                                                    <i class="fas fa-map-marker-alt me-1"></i>Есть
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-map-marker-alt me-1"></i>Нет
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('warehouses.show', $warehouse) }}" 
                                                   class="btn btn-sm btn-outline-gray" title="Просмотр">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(Auth::user()->isAdmin())
                                                <a href="{{ route('warehouses.edit', $warehouse) }}" 
                                                   class="btn btn-sm btn-outline-gray" title="Редактировать">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('warehouses.destroy', $warehouse) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Удалить" 
                                                            onclick="return confirm('Удалить склад?')">
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
                            {{ $warehouses->links() }}
                        </div>
                    @else
                        @if(Auth::user()->isAdmin())
                        <div class="text-center py-5">
                            <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Склады не найдены</h5>
                            <p class="text-muted">Начните с добавления первого склада</p>
                            <a href="{{ route('warehouses.create') }}" class="btn btn-gray">
                                <i class="fas fa-plus me-2"></i>Добавить склад
                            </a>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Статистика -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card-custom px-3 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Статистика складов
                    </h5>
                </div>
                <div class="card-body py-2">
                    <div class="row text-center" style="justify-content: center;">
                        @php
                            $totalStocks = $warehouses->sum('stocks_count');
                            $totalOrders = $warehouses->sum('orders_count');
                            $withCoordinates = $warehouses->where('latitude', '!=', null)->where('longitude', '!=', null)->count();
                        @endphp
                        <div class="col-md-3">
                            <div class="card-custom bg-light ">
                                <div class="card-body px-3 py-2">
                                    <i class="fas fa-warehouse fa-2x text-primary mb-2"></i>
                                    <h3 class="fw-bold text-primary">{{ $warehouses->count() }}</h3>
                                    <p class="text-muted mb-0">Всего складов</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card-custom bg-light">
                                <div class="card-body px-3 py-2">
                                    <i class="fas fa-boxes fa-2x text-success mb-2"></i>
                                    <h3 class="fw-bold text-success">{{ $totalStocks }}</h3>
                                    <p class="text-muted mb-0">Товарных позиций</p>
                                </div>
                            </div>
                        </div>
                        @if(Auth::user()->isAdmin())
                        <div class="col-md-3">
                            <div class="card-custom bg-light">
                                <div class="card-body px-3 py-2">
                                    <i class="fas fa-clipboard-list fa-2x text-info mb-2"></i>
                                    <h3 class="fw-bold text-info">{{ $totalOrders }}</h3>
                                    <p class="text-muted mb-0">Всего заявок</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <div class="card-custom bg-light">
                                <div class="card-body px-3 py-2">
                                    <i class="fas fa-map-marker-alt fa-2x text-warning mb-2"></i>
                                    <h3 class="fw-bold text-warning">{{ $withCoordinates }}</h3>
                                    <p class="text-muted mb-0">С координатами</p>
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