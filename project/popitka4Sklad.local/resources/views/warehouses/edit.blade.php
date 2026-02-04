{{-- resources/views/warehouses/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Редактировать склад')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-custom px-3 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-edit me-2"></i>Редактировать склад
                        </h5>
                        <a href="{{ route('warehouses.index') }}" class="btn btn-sm btn-outline-gray">
                            <i class="fas fa-arrow-left me-1"></i>Назад
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('warehouses.update', $warehouse) }}" method="POST" id="warehouseForm">
                        @csrf
                        @method('PUT')

                        <!-- Основная информация -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-muted mb-3">Основная информация</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Название склада *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $warehouse->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Адрес -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-muted mb-3">Адрес</h6>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="country" class="form-label">Страна *</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                    id="country" name="country" value="{{ old('country', $warehouse->country) }}" required>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">Город *</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                    id="city" name="city" value="{{ old('city', $warehouse->city) }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="street" class="form-label">Улица *</label>
                                <input type="text" class="form-control @error('street') is-invalid @enderror"
                                    id="street" name="street" value="{{ old('street', $warehouse->street) }}" required>
                                @error('street')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="house_number" class="form-label">Номер дома *</label>
                                <input type="text" class="form-control @error('house_number') is-invalid @enderror"
                                    id="house_number" name="house_number" value="{{ old('house_number', $warehouse->house_number) }}" required>
                                @error('house_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-outline-gray w-100" onclick="geocodeAddress()" id="geocodeBtn">
                                    <i class="fas fa-map-marker-alt me-2"></i>Определить координаты
                                </button>
                            </div>
                        </div>

                        <!-- Координаты -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-muted mb-3">Координаты на карте</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label">Широта</label>
                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror"
                                    id="latitude" name="latitude" value="{{ old('latitude', $warehouse->latitude) }}"
                                    placeholder="55.755826">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Долгота</label>
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror"
                                    id="longitude" name="longitude" value="{{ old('longitude', $warehouse->longitude) }}"
                                    placeholder="37.617300">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Карта -->
                        <div class="card-custom bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold text-muted mb-3">Расположение на карте</h6>
                                <div id="map" style="height: 300px; border-radius: 8px;"></div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Для изменения положения переместите метку на карте
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Информация о складе -->
                        <div class="card-custom bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold text-muted mb-3">Информация о складе</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Дата создания:</strong> {{ $warehouse->created_at->format('d.m.Y H:i') }}</p>
                                        <p class="mb-1"><strong>Последнее обновление:</strong> {{ $warehouse->updated_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Товарных позиций:</strong> {{ $warehouse->stocks_count ?? $warehouse->stocks->count() }}</p>
                                        <p class="mb-0"><strong>Заявок:</strong> {{ $warehouse->orders_count ?? $warehouse->orders->count() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('warehouses.show', $warehouse) }}" class="btn btn-outline-gray">
                                <i class="fas fa-eye me-2"></i>Просмотр
                            </a>
                            <div>
                                <a href="{{ route('warehouses.index') }}" class="btn btn-outline-gray me-2">
                                    <i class="fas fa-times me-2"></i>Отмена
                                </a>
                                <button type="submit" class="btn btn-gray">
                                    <i class="fas fa-save me-2"></i>Обновить склад
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://api-maps.yandex.ru/2.1/?apikey={{ env('YANDEX_MAPS_API_KEY') }}&lang=ru_RU" type="text/javascript"></script>
<script>
    let map;
    let placemark;

    // Инициализация карты
    function initMap() {
        ymaps.ready(function() {
            const defaultLat = {{ $warehouse->latitude ?: '55.755826' }};
            const defaultLon = {{ $warehouse->longitude ?: '37.617300' }};
            
            map = new ymaps.Map('map', {
                center: [defaultLat, defaultLon],
                zoom: {{ $warehouse->hasCoordinates() ? 15 : 10 }}
            });

            // Добавляем метку если есть координаты
            @if($warehouse->hasCoordinates())
            placemark = new ymaps.Placemark([defaultLat, defaultLon], {
                balloonContent: '{{ $warehouse->name }}',
                hintContent: '{{ $warehouse->name }}'
            }, {
                draggable: true, // Разрешаем перемещение метки
                preset: 'islands#redIcon'
            });

            // Обработчик перемещения метки
            placemark.events.add('dragend', function (e) {
                const coords = placemark.geometry.getCoordinates();
                document.getElementById('latitude').value = coords[0].toFixed(6);
                document.getElementById('longitude').value = coords[1].toFixed(6);
            });

            map.geoObjects.add(placemark);
            @else
            // Создаем пустую метку для нового склада
            placemark = new ymaps.Placemark([defaultLat, defaultLon], {
                balloonContent: 'Новый склад',
                hintContent: 'Перетащите метку в нужное место'
            }, {
                draggable: true,
                preset: 'islands#blueIcon'
            });

            placemark.events.add('dragend', function (e) {
                const coords = placemark.geometry.getCoordinates();
                document.getElementById('latitude').value = coords[0].toFixed(6);
                document.getElementById('longitude').value = coords[1].toFixed(6);
            });

            map.geoObjects.add(placemark);
            @endif

            // Обработчик клика по карте для добавления/перемещения метки
            map.events.add('click', function (e) {
                const coords = e.get('coords');
                
                if (placemark) {
                    placemark.geometry.setCoordinates(coords);
                } else {
                    placemark = new ymaps.Placemark(coords, {
                        balloonContent: '{{ $warehouse->name }}',
                        hintContent: 'Перетащите метку в нужное место'
                    }, {
                        draggable: true,
                        preset: 'islands#redIcon'
                    });

                    placemark.events.add('dragend', function (e) {
                        const newCoords = placemark.geometry.getCoordinates();
                        document.getElementById('latitude').value = newCoords[0].toFixed(6);
                        document.getElementById('longitude').value = newCoords[1].toFixed(6);
                    });

                    map.geoObjects.add(placemark);
                }

                document.getElementById('latitude').value = coords[0].toFixed(6);
                document.getElementById('longitude').value = coords[1].toFixed(6);
            });

            // Следим за изменением координат в полях ввода
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');

            function updateMapFromInputs() {
                const lat = parseFloat(latitudeInput.value);
                const lon = parseFloat(longitudeInput.value);
                
                if (!isNaN(lat) && !isNaN(lon)) {
                    map.setCenter([lat, lon], 15);
                    
                    if (placemark) {
                        placemark.geometry.setCoordinates([lat, lon]);
                    } else {
                        placemark = new ymaps.Placemark([lat, lon], {
                            balloonContent: '{{ $warehouse->name }}'
                        }, {
                            draggable: true,
                            preset: 'islands#redIcon'
                        });
                        
                        placemark.events.add('dragend', function (e) {
                            const newCoords = placemark.geometry.getCoordinates();
                            document.getElementById('latitude').value = newCoords[0].toFixed(6);
                            document.getElementById('longitude').value = newCoords[1].toFixed(6);
                        });
                        
                        map.geoObjects.add(placemark);
                    }
                }
            }

            latitudeInput.addEventListener('change', updateMapFromInputs);
            longitudeInput.addEventListener('change', updateMapFromInputs);
        });
    }

    // Геокодирование адреса
    function geocodeAddress() {
        const country = document.getElementById('country').value;
        const city = document.getElementById('city').value;
        const street = document.getElementById('street').value;
        const houseNumber = document.getElementById('house_number').value;

        if (!country || !city || !street || !houseNumber) {
            showAlert('Пожалуйста, заполните все поля адреса: страна, город, улица и номер дома', 'error');
            return;
        }

        const address = `${country}, ${city}, ${street}, ${houseNumber}`;
        const button = document.getElementById('geocodeBtn');
        const originalText = button.innerHTML;
        
        // Показываем индикатор загрузки
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Определение...';
        button.disabled = true;

        // Кодируем адрес для URL и используем GET запрос
        const encodedAddress = encodeURIComponent(address);
        
        fetch(`{{ route("warehouses.geocode") }}?address=${encodedAddress}`, {
            method: 'GET', // Используем GET вместо POST
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || `Ошибка HTTP: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Обновляем поля координат
                document.getElementById('latitude').value = data.latitude;
                document.getElementById('longitude').value = data.longitude;
                
                // Обновляем карту
                if (map && placemark) {
                    const newCoords = [data.latitude, data.longitude];
                    map.setCenter(newCoords, 15);
                    placemark.geometry.setCoordinates(newCoords);
                    
                    // Обновляем балун
                    placemark.properties.set({
                        balloonContent: data.address
                    });
                }
                
                showAlert(`Координаты успешно определены для адреса: ${data.address}`, 'success');
            } else {
                throw new Error(data.message || 'Не удалось определить координаты');
            }
        })
        .catch(error => {
            console.error('Geocoding error:', error);
            showAlert('Ошибка при определении координат: ' + error.message, 'error');
        })
        .finally(() => {
            // Восстанавливаем кнопку
            button.innerHTML = '<i class="fas fa-map-marker-alt me-2"></i>Определить координаты';
            button.disabled = false;
        });
    }

    // Функция для показа уведомлений
    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Вставляем уведомление перед формой
        const form = document.getElementById('warehouseForm');
        form.insertAdjacentHTML('beforebegin', alertHtml);
        
        // Автоматически скрываем через 5 секунд
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        
        // Добавляем обработчик для формы
        document.getElementById('warehouseForm').addEventListener('submit', function(e) {
            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;
            
            if (!latitude || !longitude) {
                if (!confirm('Координаты не указаны. Склад не будет отображаться на карте. Продолжить сохранение?')) {
                    e.preventDefault();
                    return;
                }
            }
        });
    });
</script>
@endpush