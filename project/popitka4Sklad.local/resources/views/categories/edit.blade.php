{{-- resources/views/categories/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Редактировать категорию')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-custom  px-3 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-edit me-2"></i>Редактировать категорию
                        </h5>
                        <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-gray">
                            <i class="fas fa-arrow-left me-1"></i>Назад
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Основная информация -->
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="name" class="form-label">Название категории *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $category->name) }}" 
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
                                      maxlength="500">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                        </div>

                        <!-- Информация о датах -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-plus me-1"></i>
                                    Создано: {{ $category->created_at->format('d.m.Y H:i') }}
                                </small>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-check me-1"></i>
                                    Обновлено: {{ $category->updated_at->format('d.m.Y H:i') }}
                                </small>
                            </div>
                        </div>

                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between align-items-right">                            
                            <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-gray me-2">
                                <i class="fas fa-eye me-2"></i>Просмотр
                            </a>
                            <button type="submit" class="btn btn-gray">
                                <i class="fas fa-save me-2"></i>Обновить категорию
                            </button>                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection