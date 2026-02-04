{{-- resources/views/manufacturers/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Добавить производителя')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-custom px-3 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-plus me-2"></i>Добавить производителя
                        </h5>
                        <a href="{{ route('manufacturers.index') }}" class="btn btn-sm btn-outline-gray">
                            <i class="fas fa-arrow-left me-1"></i>Назад
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('manufacturers.store') }}" method="POST">
                        @csrf
                        
                        <!-- Основная информация -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-muted mb-3">Основная информация</h6>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="name" class="form-label">Название производителя *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="Введите название производителя" required maxlength="100">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Адрес -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-muted mb-3">Адрес производителя</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">Страна *</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                       id="country" name="country" value="{{ old('country') }}" 
                                       placeholder="Например: Россия" required maxlength="45">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">Город *</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                       id="city" name="city" value="{{ old('city') }}" 
                                       placeholder="Например: Москва" required maxlength="60">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="street" class="form-label">Улица *</label>
                                <input type="text" class="form-control @error('street') is-invalid @enderror"
                                       id="street" name="street" value="{{ old('street') }}" 
                                       placeholder="Название улицы" required maxlength="60">
                                @error('street')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="house_number" class="form-label">Дом *</label>
                                <input type="text" class="form-control @error('house_number') is-invalid @enderror"
                                       id="house_number" name="house_number" value="{{ old('house_number') }}" 
                                       placeholder="Номер дома" required maxlength="10">
                                @error('house_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('manufacturers.index') }}" class="btn btn-outline-gray">
                                <i class="fas fa-times me-2"></i>Отмена
                            </a>
                            <button type="submit" class="btn btn-gray">
                                <i class="fas fa-save me-2"></i>Сохранить производителя
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection