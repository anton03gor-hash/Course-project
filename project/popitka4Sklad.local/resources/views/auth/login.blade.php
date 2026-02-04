{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('title', 'Вход в систему')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card-custom p-4">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Вход в систему</h2>
                    <p class="text-muted">Войдите в свой аккаунт</p>
                </div>

                <!-- Яндекс OAuth -->
                <div class="mb-4">
                    <a href="{{ route('yandex.redirect') }}" 
                       class="btn btn-outline-danger w-100 py-2 d-flex align-items-center justify-content-center">
                        <i class="fab fa-yandex me-2 fa-lg"></i>
                        Войти через Яндекс
                    </a>
                </div>

                <div class="position-relative text-center mb-4">
                    <hr>
                    <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted">
                        или
                    </span>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Запомнить меня</label>
                    </div>

                    <!-- Submit -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-gray py-2">Войти</button>
                    </div>

                    <!-- Links -->
                    <div class="text-center mt-3">
                        @if (Route::has('password.request'))
                            <a class="text-decoration-none text-muted" href="{{ route('password.request') }}">
                                Забыли пароль?
                            </a>
                        @endif
                        
                        <div class="mt-2">
                            <span class="text-muted">Нет аккаунта?</span>
                            <a class="text-decoration-none fw-bold text-gray-600" href="{{ route('register') }}">
                                Зарегистрироваться
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection