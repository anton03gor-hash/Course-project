{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card-custom p-4">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Регистрация</h2>
                    <p class="text-muted">Создайте новый аккаунт</p>
                </div>

                <!-- Яндекс OAuth -->
                <div class="mb-4">
                    <a href="{{ route('yandex.redirect') }}" 
                       class="btn btn-outline-danger w-100 py-2 d-flex align-items-center justify-content-center">
                        <i class="fab fa-yandex me-2 fa-lg"></i>
                        Зарегистрироваться через Яндекс
                    </a>
                </div>

                <div class="position-relative text-center mb-4">
                    <hr>
                    <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted">
                        или
                    </span>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Personal Info -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="surname" class="form-label">Фамилия *</label>
                            <input id="surname" type="text" class="form-control @error('surname') is-invalid @enderror" 
                                   name="surname" value="{{ old('surname') }}" required>
                            @error('surname')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="name" class="form-label">Имя *</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="fathername" class="form-label">Отчество</label>
                            <input id="fathername" type="text" class="form-control @error('fathername') is-invalid @enderror" 
                                   name="fathername" value="{{ old('fathername') }}">
                            @error('fathername')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон *</label>
                        <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" 
                               name="phone" value="{{ old('phone') }}" required placeholder="8 (XXX) XXX-XX-XX">
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Пароль *</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password-confirm" class="form-label">Подтверждение *</label>
                            <input id="password-confirm" type="password" class="form-control" 
                                   name="password_confirmation" required>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-gray py-2">Зарегистрироваться</button>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center mt-3">
                        <span class="text-muted">Уже есть аккаунт?</span>
                        <a class="text-decoration-none fw-bold text-gray-600" href="{{ route('login') }}">
                            Войти
                        </a>
                    </div>
                </form>
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