{{-- resources/views/warehouses/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Добавить склад')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-custom px-3 py-2">
                <div class="card-header bg-transparent border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-plus me-2"></i>Добавить склад
                        </h5>
                        <a href="{{ route('warehouses.index') }}" class="btn btn-sm btn-outline-gray">
                            <i class="fas fa-arrow-left me-1"></i>Назад
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('warehouses.store') }}" method="POST" id="warehouseForm">
                        @csrf

                        <!-- Основная информация -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-muted mb-3">Основная информация</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Название склада *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required
                                    placeholder="Введите название склада">
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
                                    id="country" name="country" value="{{ old('country', 'Россия') }}" required>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">Город *</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                    id="city" name="city" value="{{ old('city') }}" required
                                    placeholder="Например: Москва">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="street" class="form-label">Улица *</label>
                                <input type="text" class="form-control @error('street') is-invalid @enderror"
                                    id="street" name="street" value="{{ old('street') }}" required
                                    placeholder="Например: Ленина">
                                @error('street')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="house_number" class="form-label">Номер дома *</label>
                                <input type="text" class="form-control @error('house_number') is-invalid @enderror"
                                    id="house_number" name="house_number" value="{{ old('house_number') }}" required
                                    placeholder="Например: 15">
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
                                    id="latitude" name="latitude" value="{{ old('latitude') }}"
                                    placeholder="55.755826">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Долгота</label>
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror"
                                    id="longitude" name="longitude" value="{{ old('longitude') }}"
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
                                        Для изменения положения переместите метку на карте или введите координаты вручную
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('warehouses.index') }}" class="btn btn-outline-gray">
                                <i class="fas fa-times me-2"></i>Отмена
                            </a>
                            <button type="submit" class="btn btn-gray">
                                <i class="fas fa-save me-2"></i>Создать склад
                            </button>
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
            map = new ymaps.Map('map', {
                center: [55.755826, 37.617300], // Москва по умолчанию
                zoom: 10
            });

            // Создаем пустую метку
            placemark = new ymaps.Placemark([55.755826, 37.617300], {
                balloonContent: 'Новый склад',
                hintContent: 'Перетащите метку в нужное место'
            }, {
                draggable: true,
                preset: 'islands#blueIcon'
            });

            // Обработчик перемещения метки
            placemark.events.add('dragend', function (e) {
                const coords = placemark.geometry.getCoordinates();
                document.getElementById('latitude').value = coords[0].toFixed(6);
                document.getElementById('longitude').value = coords[1].toFixed(6);
            });

            map.geoObjects.add(placemark);

            // Обработчик клика по карте
            map.events.add('click', function (e) {
                const coords = e.get('coords');
                placemark.geometry.setCoordinates(coords);
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
                    placemark.geometry.setCoordinates([lat, lon]);
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
            showAlert('Пожалуйста, заполните все поля адреса', 'error');
            return;
        }

        const address = `${country}, ${city}, ${street}, ${houseNumber}`;
        const button = document.getElementById('geocodeBtn');
        const originalText = button.innerHTML;
        
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Определение...';
        button.disabled = true;

        const encodedAddress = encodeURIComponent(address);
        
        fetch(`/warehouses/geocode?address=${encodedAddress}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    let errorMessage = `HTTP error ${response.status}`;
                    try {
                        const errorData = JSON.parse(text);
                        errorMessage = errorData.message || errorMessage;
                    } catch (e) {}
                    throw new Error(errorMessage);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.getElementById('latitude').value = data.latitude;
                document.getElementById('longitude').value = data.longitude;
                
                if (map && placemark) {
                    const newCoords = [data.latitude, data.longitude];
                    map.setCenter(newCoords, 15);
                    placemark.geometry.setCoordinates(newCoords);
                    placemark.properties.set({ balloonContent: data.address });
                }
                
                showAlert(`Координаты определены! Адрес: ${data.address}`, 'success');
            } else {
                throw new Error(data.message || 'Не удалось определить координаты');
            }
        })
        .catch(error => {
            console.error('Geocoding error:', error);
            showAlert('Ошибка при определении координат: ' + error.message, 'error');
        })
        .finally(() => {
            button.innerHTML = '<i class="fas fa-map-marker-alt me-2"></i>Определить координаты';
            button.disabled = false;
        });
    }

    // Установка ручных координат
    // function setManualCoordinates(city) {
    //     const coordinates = {
    //         'moscow': { lat: 55.755826, lon: 37.617300 },
    //         'saint-petersburg': { lat: 59.934280, lon: 30.335098 },
    //         'novosibirsk': { lat: 55.008353, lon: 82.935732 },
    //         'yekaterinburg': { lat: 56.838011, lon: 60.597465 },
    //         'kazan': { lat: 55.796127, lon: 49.106405 }
    //     };
        
    //     if (coordinates[city]) {
    //         document.getElementById('latitude').value = coordinates[city].lat;
    //         document.getElementById('longitude').value = coordinates[city].lon;
            
    //         if (map && placemark) {
    //             const newCoords = [coordinates[city].lat, coordinates[city].lon];
    //             map.setCenter(newCoords, 12);
    //             placemark.geometry.setCoordinates(newCoords);
    //         }
            
    //         const cityNames = {
    //             'moscow': 'Москвы',
    //             'saint-petersburg': 'Санкт-Петербурга',
    //             'novosibirsk': 'Новосибирска',
    //             'yekaterinburg': 'Екатеринбурга',
    //             'kazan': 'Казани'
    //         };
            
    //         showAlert(`Координаты для ${cityNames[city]} установлены`, 'success');
    //     }
    // }

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
        
        const form = document.getElementById('warehouseForm');
        form.insertAdjacentHTML('beforebegin', alertHtml);
        
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
    });
</script>
@endpush