{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Управление пользователями')

@section('content')
<div class="container py-4">
    <!-- Заголовок и кнопки -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold mb-1">
                        <i></i>Пользователи
                    </h1>
                    <p class="text-muted mb-0">Управление пользователями системы</p>
                </div>
                <div>
                    <a href="{{ route('users.create') }}" class="btn btn-gray">
                        <i class="fas fa-plus me-2"></i>Добавить пользователя
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Уведомления -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Фильтры и поиск -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-custom p-3">
                <form action="{{ route('users.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select name="role" class="form-select" onchange="this.form.submit()">
                                <option value="">Все роли</option>
                                @foreach(\App\Models\Role::all() as $role)
                                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="auth_type" class="form-select" onchange="this.form.submit()">
                                <option value="">Все типы</option>
                                <option value="yandex" {{ request('auth_type') == 'yandex' ? 'selected' : '' }}>Яндекс OAuth</option>
                                <option value="local" {{ request('auth_type') == 'local' ? 'selected' : '' }}>Локальные</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Поиск по имени или email..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-gray w-100">
                                <i class="fas fa-filter me-2"></i>Фильтровать
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Таблица пользователей -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-body p-0">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Пользователь</th>
                                        <th>Контактная информация</th>
                                        <th>Роль</th>
                                        <th class="text-center">Заявки</th>
                                        <th>Дата регистрации</th>
                                        <th class="text-end">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 40px; height: 40px;">
                                                    @if($user->avatar)
                                                        <img src="{{ $user->avatar }}" alt="Avatar" class="rounded-circle" width="40" height="40">
                                                    @else
                                                        <i class="fas fa-user text-gray-500"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $user->full_name }}</div>
                                                    <small class="text-muted">
                                                        @if($user->yandex_id)
                                                            <i class="fab fa-yandex text-danger me-2"></i>Яндекс                                                        
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div class="text-muted">
                                                    <i class="fas fa-envelope me-1"></i>{{ $user->email }}
                                                </div>
                                                <div class="text-muted">
                                                    <i class="fas fa-phone me-1"></i>{{ $user->phone }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $roleColors = [
                                                    'admin' => 'danger',
                                                    'employee' => 'warning',
                                                    'client' => 'info'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $roleColors[$user->role->name] ?? 'secondary' }}">
                                                {{ $user->role->name }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">
                                                {{ $user->orders_count ?? $user->orders->count() }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->format('d.m.Y') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('users.show', $user) }}" 
                                                   class="btn btn-sm btn-outline-gray" title="Просмотр">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('users.edit', $user) }}" 
                                                   class="btn btn-sm btn-outline-gray" title="Редактировать">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($user->id !== auth()->id())
                                                <form action="{{ route('users.destroy', $user) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Удалить" 
                                                            onclick="return confirm('Удалить пользователя «{{ $user->full_name }}»?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Пагинация -->
                        <div class="card-footer bg-transparent">
                            {{ $users->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Пользователи не найдены</h5>
                            <p class="text-muted">Начните с добавления первого пользователя</p>
                            <a href="{{ route('users.create') }}" class="btn btn-gray">
                                <i class="fas fa-plus me-2"></i>Добавить пользователя
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Статистика -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card-custom py-2 px-3">
                <div class="card-header bg-transparent border-bottom-0">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Статистика пользователей
                    </h5>
                </div>
                <div class="card-body py-2">
                    <div class="row text-center justify-content-center">
                        @php
                            $totalUsers = $users->total();
                            $adminUsers = $users->where('role.name', 'admin')->count();
                            $employeeUsers = $users->where('role.name', 'employee')->count();
                            $clientUsers = $users->where('role.name', 'client')->count();
                            $yandexUsers = $users->where('yandex_id', '!=', null)->count();
                            $localUsers = $totalUsers - $yandexUsers;
                        @endphp
                        <div class="col-md-2">
                            <div class="card-custom bg-light">
                                <div class="card-body py-3">
                                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                    <h4 class="fw-bold text-primary">{{ $totalUsers }}</h4>
                                    <p class="text-muted mb-0">Всего</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card-custom bg-light">
                                <div class="card-body py-3">
                                    <i class="fas fa-crown fa-2x text-danger mb-2"></i>
                                    <h4 class="fw-bold text-danger">{{ $adminUsers }}</h4>
                                    <p class="text-muted mb-0">Администраторы</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card-custom bg-light">
                                <div class="card-body py-3">
                                    <i class="fas fa-user-tie fa-2x text-warning mb-2"></i>
                                    <h4 class="fw-bold text-warning">{{ $employeeUsers }}</h4>
                                    <p class="text-muted mb-0">Сотрудники</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card-custom bg-light">
                                <div class="card-body py-3">
                                    <i class="fas fa-user fa-2x text-info mb-2"></i>
                                    <h4 class="fw-bold text-info">{{ $clientUsers }}</h4>
                                    <p class="text-muted mb-0">Клиенты</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card-custom bg-light">
                                <div class="card-body py-3">
                                    <i class="fab fa-yandex fa-2x text-danger mb-2"></i>
                                    <h4 class="fw-bold text-danger">{{ $yandexUsers }}</h4>
                                    <p class="text-muted mb-0">Вошли через Яндекс</p>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection