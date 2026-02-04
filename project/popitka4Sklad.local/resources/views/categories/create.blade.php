{{-- resources/views/categories/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Добавить категорию')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-custom  px-3 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-plus me-2"></i>Добавить категорию
                        </h5>
                        <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-gray">
                            <i class="fas fa-arrow-left me-1"></i>Назад
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        
                        <!-- Основная информация -->
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="name" class="form-label">Название категории *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="Введите название категории" required maxlength="50">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="Подробное описание категории..." 
                                      maxlength="500">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                        </div>

                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-gray">
                                <i class="fas fa-times me-2"></i>Отмена
                            </a>
                            <button type="submit" class="btn btn-gray">
                                <i class="fas fa-save me-2"></i>Сохранить категорию
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection