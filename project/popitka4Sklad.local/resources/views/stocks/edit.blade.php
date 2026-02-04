{{-- resources/views/stocks/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Редактировать остаток')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom ">
                <div class="card-header bg-transparent border-bottom-0 px-3 py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-edit me-2"></i>Редактировать остаток
                        </h5>
                        <a href="{{ route('stocks.index') }}" class="btn btn-sm btn-outline-gray">
                            <i class="fas fa-arrow-left me-1"></i>Назад
                        </a>
                    </div>
                </div>
                <div class="card-body px-3 py-2">
                    <form action="{{ route('stocks.update', $stock) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Информация о товаре и складе -->
                        <div class="card-custom bg-light mb-4 px-3 py-2">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-muted">Товар</h6>
                                        <p class="mb-2 fw-bold">{{ $stock->product->name }}</p>
                                        <p class="text-muted small mb-0">
                                            {{ $stock->product->category->name }} • {{ $stock->product->manufacturer->name }}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-muted">Склад</h6>
                                        <p class="mb-2 fw-bold">{{ $stock->warehouse->name }}</p>
                                        <p class="text-muted small mb-0">
                                            {{ $stock->warehouse->city }}, {{ $stock->warehouse->street }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Количество и позиция -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="quantity" class="form-label">Количество *</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" name="quantity" value="{{ old('quantity', $stock->quantity) }}" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label">Позиция на складе</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                       id="position" name="position" value="{{ old('position', $stock->position) }}" 
                                       placeholder="Например: Стеллаж A, Полка 2">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Информация -->
                        <div class="card-custom bg-light mb-4 px-3 py-2">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-muted">Информация</h6>
                                        <p class="mb-1 small">Создан: {{ $stock->created_at->format('d.m.Y H:i') }}</p>
                                        <p class="mb-0 small">Обновлен: {{ $stock->updated_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-muted">Статус</h6>
                                        <p class="mb-0">
                                            @if($stock->quantity == 0)
                                                <span class="badge bg-danger">Нет в наличии</span>
                                            @elseif($stock->quantity < 10)
                                                <span class="badge bg-warning">Мало осталось</span>
                                            @else
                                                <span class="badge bg-success">В наличии</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('stocks.show', $stock) }}" class="btn btn-outline-gray">
                                <i class="fas fa-eye me-2"></i>Просмотр
                            </a>
                            <div>
                                <a href="{{ route('stocks.index') }}" class="btn btn-outline-gray me-2">
                                    <i class="fas fa-times me-2"></i>Отмена
                                </a>
                                <button type="submit" class="btn btn-gray">
                                    <i class="fas fa-save me-2"></i>Обновить остаток
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection