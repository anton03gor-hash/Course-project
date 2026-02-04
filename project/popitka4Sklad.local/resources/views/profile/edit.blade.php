{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Редактирование профиля')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-custom  py-2 px-3">
                <div class="card-header bg-transparent border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-user-edit me-2"></i>Редактирование профиля
                        </h5>
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-gray">
                            <i class="fas fa-arrow-left me-1"></i>Назад
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Уведомления -->
                    @if (session('status') === 'profile-updated')
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            Профиль успешно обновлен!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Ошибка!</strong> Пожалуйста, проверьте введенные данные.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <!-- Аватар и основная информация -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-muted mb-3">Основная информация</h6>
                            </div>
                            <div class="col-md-3 text-center mb-3">
                                <div class="position-relative d-inline-block">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" alt="Avatar" 
                                             class="rounded-circle" width="100" height="100">
                                    @else
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 100px; height: 100px;">
                                            <i class="fas fa-user fa-2x text-gray-500"></i>
                                        </div>
                                    @endif
                                    @if($user->yandex_id)
                                        <span class="position-absolute bottom-0 end-0 badge bg-danger" 
                                              title="Привязан Яндекс аккаунт">
                                            <i class="fab fa-yandex"></i>
                                        </span>
                                    @endif
                                </div>
                                
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Имя *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="surname" class="form-label">Фамилия *</label>
                                        <input type="text" class="form-control @error('surname') is-invalid @enderror" 
                                               id="surname" name="surname" value="{{ old('surname', $user->surname) }}" required>
                                        @error('surname')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fathername" class="form-label">Отчество</label>
                                        <input type="text" class="form-control @error('fathername') is-invalid @enderror" 
                                               id="fathername" name="fathername" value="{{ old('fathername', $user->fathername) }}">
                                        @error('fathername')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Роль</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-{{ $user->role->name === 'admin' ? 'danger' : ($user->role->name === 'employee' ? 'warning' : 'info') }}">
                                                {{ $user->role->name }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
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
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required
                                       {{ $user->yandex_id ? 'readonly' : '' }}>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($user->yandex_id)
                                    <div class="form-text">
                                        <i class="fab fa-yandex text-danger me-1"></i>
                                        Email управляется через Яндекс аккаунт
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Телефон *</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required
                                       placeholder="+7 (XXX) XXX-XX-XX">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Смена пароля -->
                        @if(!$user->yandex_id)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-muted mb-3">Смена пароля</h6>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Введите следующие данные для изменения пароля. Оставтье поля незаполненными чтобы оставить прежний пароль
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="current_password" class="form-label">Текущий пароль</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Новый пароль</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="password_confirmation" class="form-label">Подтверждение нового пароля</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                        @else
                        <div class="card-custom bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold text-muted mb-3">
                                    <i class="fab fa-yandex text-danger me-2"></i>Управление через Яндекс
                                </h6>
                                <p class="mb-2">Ваш аккаунт привязан к Яндекс ID. Для изменения пароля и email используйте Яндекс аккаунт.</p>
                                <a href="https://passport.yandex.ru/profile" target="_blank" class="btn btn-sm btn-outline-danger">
                                    <i class="fab fa-yandex me-2"></i>Управление Яндекс аккаунтом
                                </a>
                            </div>
                        </div>
                        @endif

                        <!-- Информация о профиле -->
                        
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>Дата регистрации:</strong> 
                                {{ $user->created_at->format('d.m.Y H:i') }}
                            </p>
                        </div>
                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between align-items-center">
                           
                            <button type="submit" class="btn btn-gray">
                                <i class="fas fa-save me-2"></i>Сохранить изменения
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Удаление аккаунта -->
            @if(!$user->yandex_id)
            <div class="card-custom mt-4 border-danger">               
                <div class="card-body py-2 px-3">
                    <p class="text-muted mb-3">
                        После удаления аккаунта все ваши данные будут безвозвратно удалены. 
                        Пожалуйста, сохраните важную информацию перед удалением.
                    </p>
                    <form action="{{ route('profile.destroy') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Вы уверены, что хотите удалить свой аккаунт? Это действие нельзя отменить.')">
                            <i class="fas fa-trash me-2"></i>Удалить аккаунт
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Маска для телефона
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.startsWith('7') || value.startsWith('8')) {
                    value = value.substring(1);
                }
                
                let formattedValue = '+7 (';
                if (value.length > 0) {
                    formattedValue += value.substring(0, 3);
                }
                if (value.length > 3) {
                    formattedValue += ') ' + value.substring(3, 6);
                }
                if (value.length > 6) {
                    formattedValue += '-' + value.substring(6, 8);
                }
                if (value.length > 8) {
                    formattedValue += '-' + value.substring(8, 10);
                }
                
                e.target.value = formattedValue;
            });
        }
    });
</script>
@endpush