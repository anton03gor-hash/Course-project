{{-- resources/views/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Редактировать пользователя')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom px-2 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-edit me-2"></i>Редактировать пользователя
                        </h5>
                        
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Персональная информация -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-muted mb-3">Персональная информация</h6>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="surname" class="form-label">Фамилия *</label>
                                <input type="text" class="form-control @error('surname') is-invalid @enderror" 
                                       id="surname" name="surname" value="{{ old('surname', $user->surname) }}" required>
                                @error('surname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="name" class="form-label">Имя *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="fathername" class="form-label">Отчество</label>
                                <input type="text" class="form-control @error('fathername') is-invalid @enderror" 
                                       id="fathername" name="fathername" value="{{ old('fathername', $user->fathername) }}">
                                @error('fathername')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Контактная информация -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-muted mb-3">Контактная информация</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Телефон *</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Безопасность и роль -->
                        @if($user->yandex_id)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-muted mb-3">Безопасность и роль</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="">Вы не можете изменить польщователю пароль</p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="role_id" class="form-label">Роль *</label>
                                @if($user->id !== auth()->id())
                                <select class="form-select @error('role_id') is-invalid @enderror" 
                                        id="role_id" name="role_id" required>
                                    <option value="">Выберите роль</option>
                                    @foreach($roles as $role)
                                        
                                        <option value="{{ $role->id }}" 
                                            {{ (old('role_id') ?? $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                        
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @else
                                <p class="">Вы не можете изменить свою роль самостоятельно!</p>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-muted mb-3">Безопасность и роль</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Новый пароль</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" 
                                       placeholder="Оставьте пустым, если не меняется">
                                <div class="form-text">Минимум 8 символов</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="role_id" class="form-label">Роль *</label>
                                @if($user->id !== auth()->id())
                                <select class="form-select @error('role_id') is-invalid @enderror" 
                                        id="role_id" name="role_id" required>
                                    <option value="">Выберите роль</option>
                                    @foreach($roles as $role)
                                        
                                        <option value="{{ $role->id }}" 
                                            {{ (old('role_id') ?? $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                        
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @else
                                <p class="">Вы не можете изменить свою роль самостоятельно!</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Информация о пользователе -->
                        <div class="card-custom bg-light mb-4">
                            <div class="card-body px-2 py-2">
                                <h6 class="fw-bold text-muted mb-3">Информация о пользователе</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Дата регистрации:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</p>
                                        <p class="mb-1"><strong>Последнее обновление:</strong> {{ $user->updated_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Тип входа:</strong> 
                                            @if($user->yandex_id)
                                                <span class="badge bg-danger">Через Яндекс</span>
                                            @else
                                                <span class="badge bg-secondary">По паролю</span>
                                            @endif
                                        </p>
                                        <p class="mb-0"><strong>Заявок:</strong> {{ $user->orders->count() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-outline-gray">
                                <i class="fas fa-eye me-2"></i>Просмотр
                            </a>
                            <div>
                                <a href="{{ route('users.index') }}" class="btn btn-outline-gray me-2">
                                    <i class="fas fa-times me-2"></i>Отмена
                                </a>
                                <button type="submit" class="btn btn-gray">
                                    <i class="fas fa-save me-2"></i>Обновить пользователя
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Маска для телефона
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function(e) {
        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
        e.target.value = '+7' + (x[2] ? ' (' + x[2] : '') + (x[3] ? ') ' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
    });
});
</script>
@endsection