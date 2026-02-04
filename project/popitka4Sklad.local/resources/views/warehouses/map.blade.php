{{-- resources/views/warehouses/map.blade.php --}}
@extends('layouts.app')

@section('title', 'Карта складов')

@section('content')
<div class="container py-4">
    <!-- Заголовок и действия -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold mb-1">
                        <i class="fas fa-map me-2"></i>Карта складов
                    </h1>
                    <p class="text-muted mb-0">Географическое расположение всех складов компании</p>
                </div>
                <div>
                    <a href="{{ route('warehouses.index') }}" class="btn btn-outline-gray me-2">
                        <i class="fas fa-list me-2"></i>Список складов
                    </a>
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('warehouses.create') }}" class="btn btn-gray">
                        <i class="fas fa-plus me-2"></i>Добавить склад
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-body">
                    <div class="row text-center">
                        @php
                            $totalWarehouses = $warehouses->count();
                            $withCoordinates = $warehouses->where('latitude', '!=', null)->where('longitude', '!=', null)->count();
                            $withoutCoordinates = $totalWarehouses - $withCoordinates;
                            $totalStocks = \App\Models\Stock::count();
                            $totalOrders = \App\Models\Order::count();
                        @endphp
                        <div class="col-md-2 ">
                            <div class="card-custom bg-light">
                                <div class="card-body py-3">
                                    <i class="fas fa-warehouse fa-2x text-primary mb-2"></i>
                                    <h4 class="fw-bold text-primary">{{ $totalWarehouses }}</h4>
                                    <p class="text-muted mb-0 small">Всего складов</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card-custom bg-light">
                                <div class="card-body py-3">
                                    <i class="fas fa-map-marker-alt fa-2x text-success mb-2"></i>
                                    <h4 class="fw-bold text-success">{{ $withCoordinates }}</h4>
                                    <p class="text-muted mb-0 small">С координатами</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card-custom bg-light">
                                <div class="card-body py-3">
                                    <i class="fas fa-map-marker-alt fa-2x text-warning mb-2"></i>
                                    <h4 class="fw-bold text-warning">{{ $withoutCoordinates }}</h4>
                                    <p class="text-muted mb-0 small">Без координат</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card-custom bg-light">
                                <div class="card-body py-3">
                                    <i class="fas fa-boxes fa-2x text-info mb-2"></i>
                                    <h4 class="fw-bold text-info">{{ $totalStocks }}</h4>
                                    <p class="text-muted mb-0 small">Товарных позиций</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card-custom bg-light">
                                <div class="card-body py-3">
                                    <i class="fas fa-clipboard-list fa-2x text-secondary mb-2"></i>
                                    <h4 class="fw-bold text-secondary">{{ $totalOrders }}</h4>
                                    <p class="text-muted mb-0 small">Всего заявок</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card-custom bg-light">
                                <div class="card-body py-3">
                                    <i class="fas fa-chart-line fa-2x text-danger mb-2"></i>
                                    <h4 class="fw-bold text-danger">{{ $withCoordinates > 0 ? round(($withCoordinates / $totalWarehouses) * 100, 1) : 0 }}%</h4>
                                    <p class="text-muted mb-0 small">Охват картой</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Карта -->
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-header bg-transparent border-bottom-0 px-2 py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-map-marked-alt me-2"></i>Географическое расположение складов
                        </h5>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-gray" onclick="zoomToAll()">
                                <i class="fas fa-search me-1"></i>Показать все
                            </button>
                            <!-- <button type="button" class="btn btn-sm btn-outline-gray" onclick="toggleCluster()" id="clusterToggle">
                                <i class="fas fa-layer-group me-1"></i>Кластеризация
                            </button> -->
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 600px; border-radius: 0 0 8px 8px;"></div>
                </div>
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
    let objectManager;
    let clusterEnabled = true;

    // Инициализация карты
    ymaps.ready(function() {
        map = new ymaps.Map('map', {
            center: [55.755826, 37.617300], // Москва
            zoom: 4,
            controls: ['zoomControl', 'fullscreenControl', 'typeSelector']
        });

        // Создаем менеджер объектов для кластеризации
        objectManager = new ymaps.ObjectManager({
            clusterize: clusterEnabled,
            gridSize: 32,
            clusterDisableClickZoom: true
        });

        // Настройка кластеров
        objectManager.clusters.options.set({
            preset: 'islands#invertedDarkBlueClusterIcons',
            clusterDisableClickZoom: true,
            clusterBalloonContentLayout: 'cluster#balloonCarousel'
        });

        // Настройка меток
        objectManager.objects.options.set({
            preset: 'islands#greenDotIcon',
            openBalloonOnClick: true
        });

        // Обработчик клика по метке
        objectManager.objects.events.add('click', function (e) {
            const objectId = e.get('objectId');
            const object = objectManager.objects.getById(objectId);
            
            if (object) {
                // Можно добавить дополнительную логику при клике на метку
                console.log('Clicked warehouse:', object);
            }
        });

        // Обработчик клика по кластеру
        objectManager.clusters.events.add('click', function (e) {
            const clusterId = e.get('objectId');
            const cluster = objectManager.clusters.getById(clusterId);
            
            // Показываем балун с информацией о кластере
            map.balloon.open(cluster.geometry.coordinates, {
                contentHeader: `Группа складов (${cluster.properties.geoObjects.length})`,
                contentBody: getClusterContent(cluster),
                contentFooter: '<small>Нажмите на метку для подробной информации</small>'
            });
        });

        map.geoObjects.add(objectManager);

        // Загрузка данных складов
        loadWarehousesData();
    });

    // Загрузка данных складов
    function loadWarehousesData() {
        fetch('{{ route("warehouses.map.data") }}')
            .then(response => response.json())
            .then(warehouses => {
                const features = warehouses.map(warehouse => ({
                    type: 'Feature',
                    id: warehouse.id,
                    geometry: {
                        type: 'Point',
                        coordinates: [warehouse.latitude, warehouse.longitude]
                    },
                    properties: {
                        balloonContent: getBalloonContent(warehouse),
                        clusterCaption: warehouse.name,
                        hintContent: warehouse.name,
                        // Цвет иконки в зависимости от наличия товаров
                        iconContent: warehouse.stocks_count > 0 ? '✓' : '0',
                        preset: warehouse.stocks_count > 0 ? 'islands#greenIcon' : 'islands#orangeIcon'
                    }
                }));

                objectManager.add(features);

                // Автоматическое подстраивание карты под объекты
                if (features.length > 0) {
                    zoomToAll();
                }
            })
            .catch(error => {
                console.error('Error loading warehouses data:', error);
                showAlert('Ошибка загрузки данных складов', 'error');
            });
    }

    // Генерация содержимого балуна для склада
    function getBalloonContent(warehouse) {
        return `
            <div class="balloon-content">
                <h6 class="fw-bold mb-2">${warehouse.name}</h6>
                <p class="mb-1"><i class="fas fa-map-marker-alt text-muted me-1"></i> ${warehouse.address}</p>
                <div class="row mt-2">
                    <div class="col-6">
                        <span class="badge bg-success"><i class="fas fa-boxes me-1"></i> ${warehouse.stocks_count}</span>
                    </div>
                    <div class="col-6">
                        <span class="badge bg-info"><i class="fas fa-clipboard-list me-1"></i> ${warehouse.orders_count}</span>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="${warehouse.url}" class="btn btn-sm btn-primary w-100" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>Подробнее
                    </a>
                </div>
            </div>
        `;
    }

    // Генерация содержимого для кластера
    function getClusterContent(cluster) {
        const warehouses = cluster.properties.geoObjects;
        let content = '<div class="cluster-content">';
        
        warehouses.slice(0, 5).forEach(warehouse => {
            content += `
                <div class="cluster-item mb-2 p-2 border rounded">
                    <strong>${warehouse.properties.clusterCaption}</strong>
                    <br>
                    <small class="text-muted">Товаров: ${warehouse.properties.iconContent === '✓' ? 'есть' : 'нет'}</small>
                </div>
            `;
        });

        if (warehouses.length > 5) {
            content += `<div class="text-center text-muted">... и еще ${warehouses.length - 5} складов</div>`;
        }

        content += '</div>';
        return content;
    }

    // Показать все склады на карте
    function zoomToAll() {
        if (objectManager.getBounds()) {
            map.setBounds(objectManager.getBounds(), {
                checkZoomRange: true,
                zoomMargin: 50
            });
        }
    }

    // Переключение кластеризации
    function toggleCluster() {
        clusterEnabled = !clusterEnabled;
        objectManager.clusterize = clusterEnabled;
        
        const button = document.getElementById('clusterToggle');
        if (clusterEnabled) {
            button.innerHTML = '<i class="fas fa-layer-group me-1"></i>Кластеризация';
            button.classList.remove('btn-gray');
            button.classList.add('btn-outline-gray');
        } else {
            button.innerHTML = '<i class="fas fa-dot-circle me-1"></i>Отдельно';
            button.classList.remove('btn-outline-gray');
            button.classList.add('btn-gray');
        }
    }

    // Функция для показа уведомлений
    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999;" role="alert">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', alertHtml);
        
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
</script>

<style>
.balloon-content {
    min-width: 250px;
}

.cluster-content {
    max-height: 300px;
    overflow-y: auto;
}

.cluster-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6 !important;
}

.ymaps-balloon__content {
    padding: 15px;
    border-radius: 8px;
}

.ymaps-balloon__close {
    right: 8px !important;
    top: 8px !important;
}
</style>
@endpush