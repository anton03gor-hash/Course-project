{{-- resources/views/reports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Отчеты')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-1">
                        <i class="fas fa-chart-line me-2"></i>Отчеты
                    </h1>
                    <p class="text-muted mb-0">Генерация отчетов в формате PDF</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Отчет по заявкам -->
        <div class="col-lg-4 mb-4">
            <div class="card-custom h-100 px-3 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <h5 class="fw-bold mb-0">
                        <i class="fas "></i>Отчет по заявкам
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Отчет по выполненным заявкам за выбранный период</p>
                    
                    <form action="{{ route('reports.orders') }}" method="GET">
                        <div class="mb-3">
                            <label for="orders_start_date" class="form-label">Начальная дата *</label>
                            <input type="date" class="form-control" id="orders_start_date" name="start_date" 
                                   value="{{ now()->subMonth()->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="orders_end_date" class="form-label">Конечная дата *</label>
                            <input type="date" class="form-control" id="orders_end_date" name="end_date" 
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="orders_warehouse" class="form-label">Склад (опционально)</label>
                            <select class="form-select" id="orders_warehouse" name="warehouse_id">
                                <option value="">Все склады</option>
                                @foreach(\App\Models\Warehouse::all() as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" name="download" value="1" class="btn btn-outline-gray w-100">
                            <i></i>Скачать
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Отчет по перемещениям -->
        <div class="col-lg-4 mb-4">
            <div class="card-custom h-100 px-3 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <h5 class="fw-bold mb-0">
                        <i class="fas  "></i>Отчет по перемещениям
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Отчет по перемещениям товаров за выбранный период</p>
                    
                    <form action="{{ route('reports.movements') }}" method="GET">
                        <div class="mb-3">
                            <label for="movements_start_date" class="form-label">Начальная дата *</label>
                            <input type="date" class="form-control" id="movements_start_date" name="start_date" 
                                   value="{{ now()->subMonth()->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="movements_end_date" class="form-label">Конечная дата *</label>
                            <input type="date" class="form-control" id="movements_end_date" name="end_date" 
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="movements_status" class="form-label">Статус</label>
                            <select class="form-select" id="movements_status" name="status">
                                <option value="">Все статусы</option>
                                <option value="in_progress">В процессе</option>
                                <option value="complete">Выполнено</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="movements_warehouse" class="form-label">Склад (опционально)</label>
                            <select class="form-select" id="movements_warehouse" name="warehouse_id">
                                <option value="">Все склады</option>
                                @foreach(\App\Models\Warehouse::all() as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" name="download" value="1" class="btn btn-outline-gray w-100">
                            <i></i>Скачать
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Отчет по остаткам -->
        <div class="col-lg-4 mb-4">
            <div class="card-custom h-100 px-3 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <h5 class="fw-bold mb-0">
                        <i></i>Отчет по остаткам
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Текущие остатки товаров на складах</p>
                    
                    <form action="{{ route('reports.stocks') }}" method="GET">
                        <div class="mb-3">
                            <label for="stocks_warehouse" class="form-label">Склад</label>
                            <select class="form-select" id="stocks_warehouse" name="warehouse_id">
                                <option value="">Все склады</option>
                                @foreach(\App\Models\Warehouse::all() as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="stocks_status" class="form-label">Статус остатков</label>
                            <select class="form-select" id="stocks_status" name="status">
                                <option value="">Все статусы</option>
                                <option value="in_stock">В наличии</option>
                                <option value="low_stock">Мало осталось</option>
                                <option value="out_of_stock">Нет в наличии</option>
                            </select>
                        </div>
                        
                        <button type="submit" name="download" value="1" class="btn btn-outline-gray w-100">
                            <i></i>Скачать
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection