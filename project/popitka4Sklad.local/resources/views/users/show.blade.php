{{-- resources/views/users/show.blade.php --}}
@extends('layouts.app')

@section('title', $user->full_name)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Заголовок и действия -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Пользователи</a></li>
                            <li class="breadcrumb-item active">{{ $user->full_name }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 fw-bold mb-1">{{ $user->full_name }}</h1>
                    <p class="text-muted mb-0">Детальная информация о пользователе</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-gray">
                        <i class="fas fa-arrow-left me-2"></i>Назад
                    </a>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-gray">
                        <i class="fas fa-edit me-2"></i>Редактировать
                    </a>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" 
                                onclick="return confirm('Удалить пользователя?')">
                            <i class="fas fa-trash me-2"></i>Удалить
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <div class="row">
                <!-- Основная информация -->
                <div class="col-lg-8 mb-4">
                    <div class="card-custom h-100 py-2 px-2">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-info-circle me-2"></i>Основная информация
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">ФИО</label>
                                    <p class="fw-bold">{{ $user->full_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Роль</label>
                                    <p>
                                        @php
                                            $roleColors = [
                                                'admin' => 'danger',
                                                'employee' => 'warning', 
                                                'client' => 'info'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $roleColors[$user->role->name] }} fs-6">
                                            {{ $user->role->name }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Email</label>
                                    <p class="fw-bold">
                                        <i class="fas fa-envelope text-gray-400 me-2"></i>{{ $user->email }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Телефон</label>
                                    <p class="fw-bold">
                                        <i class="fas fa-phone text-gray-400 me-2"></i>{{ $user->phone }}
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Тип аккаунта</label>
                                    <p>
                                        @if($user->yandex_id)
                                            <span class="badge bg-danger">
                                                <i class="fab fa-yandex me-1"></i>Яндекс
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-envelope me-1"></i>Локальный
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Статус</label>
                                    <p>
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-check me-1"></i>Активный
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Дата регистрации</label>
                                    <p>{{ $user->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Последнее обновление</label>
                                    <p>{{ $user->updated_at->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Статистика -->
                <div class="col-lg-4 mb-4">
                    <div class="card-custom h-100 py-2 px-2">
                        <div class="card-header bg-transparent border-bottom-0">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Статистика
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px;">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" alt="Avatar" class="rounded-circle" width="80" height="80">
                                    @else
                                        <i class="fas fa-user fa-2x text-gray-500"></i>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Всего заявок</span>
                                    <span class="badge bg-primary">{{ $user->orders->count() }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Ожидают</span>
                                    <span class="badge bg-warning">{{ $user->orders->where('status', 'pending')->count() }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Подтверждены</span>
                                    <span class="badge bg-info">{{ $user->orders->where('status', 'confirmed')->count() }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>Выполнены</span>
                                    <span class="badge bg-success">{{ $user->orders->where('status', 'completed')->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- История заявок -->
            <div class="row">
                <div class="col-12">
                    <div class="card-custom ">
                        <div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center py-2 px-2">
                            <h5 class="fw-bold mb-0">
                                <i></i>История заявок
                            </h5>
                            <span class="badge bg-secondary">{{ $user->orders->count() }} заявок</span>
                        </div>
                        <div class="card-body">
                            @if($user->orders->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID заявки</th>
                                                <th>Склад</th>
                                                <th class="text-center">Товаров</th>
                                                <th>Статус</th>
                                                <th>Дата создания</th>
                                                <th class="text-end">Действия</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->orders->sortByDesc('created_at')->take(10) as $order)
                                            <tr>
                                                <td class="fw-bold">#{{ $order->id }}</td>
                                                <td>{{ $order->warehouse->name }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark">{{ $order->products->count() }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'pending' => 'warning',
                                                            'confirmed' => 'info',
                                                            'rejected' => 'danger',
                                                            'completed' => 'success'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColors[$order->status] }}">
                                                        @if($order->status == 'pending') Ожидает
                                                        @elseif($order->status == 'confirmed') Подтверждена
                                                        @elseif($order->status == 'rejected') Отклонена
                                                        @elseif($order->status == 'completed') Выполнена
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('orders.show', $order) }}" 
                                                       class="btn btn-sm btn-outline-gray">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if($user->orders->count() > 10)
                                <div class="text-center mt-3">
                                    <a href="{{ route('orders.index') }}?user={{ $user->id }}" class="btn btn-outline-gray">
                                        <i class="fas fa-list me-2"></i>Показать все заявки
                                    </a>
                                </div>
                                @endif
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-clipboard-list fa-2x text-muted mb-3"></i>
                                    <h5 class="text-muted">Заявки отсутствуют</h5>
                                    <p class="text-muted">Пользователь еще не создавал заявок</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection