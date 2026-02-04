{{-- resources/views/warehouses/show.blade.php --}}
@extends('layouts.app')

@section('title', $warehouse->name)

@section('content')
<div class="container py-4">
    <!-- Заголовок и действия -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('warehouses.index') }}">Склады</a></li>
                            <li class="breadcrumb-item active">{{ $warehouse->name }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 fw-bold mb-1">
                        <i class="fas fa-warehouse me-2"></i>{{ $warehouse->name }}
                    </h1>
                    <p class="text-muted mb-0">Детальная информация о складе</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('warehouses.index') }}" class="btn btn-outline-gray">
                        <i class="fas fa-arrow-left me-2"></i>Назад
                    </a>
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('warehouses.edit', $warehouse) }}" class="btn btn-outline-gray">
                        <i class="fas fa-edit me-2"></i>Редактировать
                    </a>
                    @endif
                </div>
            </div>
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
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Название склада</label>
                            <p class="fw-bold fs-5">{{ $warehouse->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Полный адрес</label>
                            <p class="fw-bold">{{ $warehouse->full_address }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Страна</label>
                            <p>{{ $warehouse->country }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Город</label>
                            <p>{{ $warehouse->city }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Улица</label>
                            <p>{{ $warehouse->street }}, {{ $warehouse->house_number }}</p>
                        </div>
                    </div>

                    @if($warehouse->hasCoordinates())
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Координаты</label>
                            <p>
                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                Ш: {{ $warehouse->latitude }}, Д: {{ $warehouse->longitude }}
                            </p>
                        </div>
                    </div>
                    @endif
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
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Товарных позиций</span>
                            <span class="badge bg-primary">{{ $stockStats['total_products'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>В наличии</span>
                            <span class="badge bg-success">{{ $stockStats['in_stock'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Мало осталось</span>
                            <span class="badge bg-warning">{{ $stockStats['low_stock'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Нет в наличии</span>
                            <span class="badge bg-danger">{{ $stockStats['out_of_stock'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Общее количество</span>
                            <span class="badge bg-info">{{ $stockStats['total_quantity'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Заявок</span>
                            <span class="badge bg-secondary">{{ $warehouse->orders->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Карта -->
    @if($warehouse->hasCoordinates())
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-custom px-3 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-map me-2"></i>Расположение на карте
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 400px; border-radius: 0 0 8px 8px;"></div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Товары на складе -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center px-3 py-2">
                    <h5 class="fw-bold mb-0">
                        <i></i>Товары на складе
                    </h5>
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('stocks.create') }}?warehouse_id={{ $warehouse->id }}" class="btn btn-sm btn-gray">
                        <i class="fas fa-plus me-1"></i>Добавить товар
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($warehouse->stocks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Товар</th>
                                    <th>Категория</th>
                                    <th>Производитель</th>
                                    <th class="text-center">Количество</th>
                                    <th>Позиция</th>
                                    <th class="text-end">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($warehouse->stocks as $stock)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3">
                                                <i class="fas fa-box text-gray-500"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $stock->product->name }}</h6>
                                                <small class="text-muted">SKU: {{ $stock->product->sku ?? '---' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $stock->product->category->name }}</td>
                                    <td>{{ $stock->product->manufacturer->name }}</td>
                                    <td class="text-center">
                                        <span class="fw-bold @if($stock->quantity == 0) text-danger
                                            @elseif($stock->quantity < 10) text-warning
                                            @else text-success @endif">
                                            {{ $stock->quantity }}
                                        </span>
                                    </td>
                                    <td>{{ $stock->position ?? '---' }}</td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('stocks.show', $stock) }}" 
                                               class="btn btn-sm btn-outline-gray" title="Просмотр">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('stocks.edit', $stock) }}" 
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
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">На складе нет товаров</h5>
                        @if(Auth::user()->isAdmin())
                        <p class="text-muted">Добавьте первый товар на склад</p>
                        <a href="{{ route('stocks.create') }}?warehouse_id={{ $warehouse->id }}" class="btn btn-gray">
                            <i class="fas fa-plus me-2"></i>Добавить товар
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

@if($warehouse->hasCoordinates())
@push('scripts')
<script src="https://api-maps.yandex.ru/2.1/?apikey={{ env('YANDEX_MAPS_API_KEY') }}&lang=ru_RU" type="text/javascript"></script>
<script>
    ymaps.ready(function() {
        var map = new ymaps.Map('map', {
            center: [{{ $warehouse->latitude }}, {{ $warehouse->longitude }}],
            zoom: 15
        });

        var placemark = new ymaps.Placemark([{{ $warehouse->latitude }}, {{ $warehouse->longitude }}], {
            balloonContent: '<strong>{{ $warehouse->name }}</strong><br>{{ $warehouse->full_address }}'
        }, {
            preset: 'islands#redIcon'
        });

        map.geoObjects.add(placemark);
        placemark.balloon.open();
    });
</script>
@endpush
@endif