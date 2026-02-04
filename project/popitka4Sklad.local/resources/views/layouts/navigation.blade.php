{{-- resources/views/layouts/navigation.blade.php --}}
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('welcome') }}">
            <i class="me-2"></i>Управление складом
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Левая часть навигации -->
            <ul class="navbar-nav me-auto">
    @auth
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i></i>Главная
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('products.index') }}">
                <i></i>Товары
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('orders.index') }}">
                <i></i>Заявки
            </a>
        </li>
        @if(auth()->user()->isAdmin() || auth()->user()->isClient())
        <li class="nav-item">
            <a class="nav-link" href="{{ route('orders.create') }}">
                <i></i>Новая заявка
            </a>
        </li>
        @endif
        <li class="nav-item">
            <a class="nav-link" href="{{ route('warehouses.index') }}">
                <i></i>Склады
            </a>
        </li>
        @if(auth()->user()->isAdmin() || auth()->user()->isEmployee())
            <li class="nav-item">
                <a class="nav-link" href="{{ route('movements.index') }}">
                    <i></i>Перемещения
                </a>
            </li>
        @endif
        @if(auth()->user()->isAdmin() || auth()->user()->isEmployee())
        <li class="nav-item">
            <a class="nav-link" href="{{ route('stocks.index') }}">
                <i></i>Остатки
            </a>
        </li>
        @endif
        @if(auth()->user()->isAdmin())
        <li class="nav-item">
            <a class="nav-link" href="{{ route('reports.index') }}">
                <i></i>Отчеты
            </a>
        </li>
        @endif
        @if(auth()->user()->isAdmin())
            <li class="nav-item">
                <a class="nav-link" href="{{ route('users.index') }}">
                    <i></i>Пользователи
                </a>
            </li>
        @endif
    @endauth
</ul>
            
            <!-- Правая часть навигации -->
            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>{{ auth()->user()->full_name }}
                            <small class="badge bg-secondary ms-1">
                                @if(auth()->user()->role->name == 'employee') Сотрудник
                                    @elseif(auth()->user()->role->name == 'admin') Администратор
                                    @elseif(auth()->user()->role->name == 'client') Клиент
                                @endif
                            </small>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                           
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-edit me-2"></i>Профиль
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Выйти
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>Войти
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i>Регистрация
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>