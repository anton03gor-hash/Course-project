{{-- resources/views/welcome.blade.php --}}
@extends('layouts.app')

@section('title', 'Система управления складом и запасами')

@section('content')
    <!-- Главная секция -->
    <section class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Система управления складом и запасами</h1>
                    <p class="lead mb-4 text-muted">Автоматизация процессов учёта товаров, управления складскими операциями и контроля остатков</p>
                    @guest
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="{{ route('register') }}" class="btn btn-gray btn-lg px-4">
                                <i></i>Начать работу
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-gray btn-lg px-4">
                                <i></i>Войти в систему
                            </a>
                        </div>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-gray btn-lg px-4">
                            <i></i>Перейти в систему
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- Функционал системы -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col">
                    <h2 class="fw-bold mb-3">Основные возможности системы</h2>
                    <p class="lead text-muted">Все необходимое для эффективного управления складом</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card-custom p-4 h-100 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Учет товаров</h4>
                        <p class="text-muted">Полный контроль над товарными остатками на всех складах с детализацией по позициям</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-custom p-4 h-100 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Управление заявками</h4>
                        <p class="text-muted">Оформление и отслеживание заявок на получение товаров с автоматическим формированием перемещений</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-custom p-4 h-100 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Перемещения</h4>
                        <p class="text-muted">Контроль перемещений товаров между складами с отслеживанием статусов выполнения</p>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <div class="card-custom p-4 h-100 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Аналитика</h4>
                        <p class="text-muted">Детальная аналитика движения товаров, формирование отчетов для принятия решений</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-custom p-4 h-100 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Геолокация</h4>
                        <p class="text-muted">Интеграция с картами для визуализации расположения складов и оптимизации логистики</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-custom p-4 h-100 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Безопасность</h4>
                        <p class="text-muted">Многоуровневая система безопасности с разграничением прав доступа для разных ролей</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Карта складов -->
    <!-- <section class="py-5 bg-white">
        <div class="container">
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
                                <button type="button" class="btn btn-sm btn-outline-gray" onclick="toggleCluster()" id="clusterToggle">
                                    <i class="fas fa-layer-group me-1"></i>Кластеризация
                                </button>
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
    </section> -->

    <!-- Преимущества -->
    <!-- <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Почему выбирают нашу систему?</h2>
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                        <div>
                            <h5 class="fw-bold">Автоматизация процессов</h5>
                            <p class="text-muted mb-0">Сокращение ручного труда и минимизация ошибок при учете</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                        <div>
                            <h5 class="fw-bold">Реальное время</h5>
                            <p class="text-muted mb-0">Актуальная информация о остатках и перемещениях</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                        <div>
                            <h5 class="fw-bold">Масштабируемость</h5>
                            <p class="text-muted mb-0">Подходит как для малого бизнеса, так и для крупных предприятий</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-custom p-5 text-center">
                        <i class="fas fa-chart-line fa-4x text-gray-500 mb-4"></i>
                        <h4 class="fw-bold mb-3">Повышение эффективности</h4>
                        <p class="text-muted">Наши клиенты отмечают увеличение производительности складских операций на 40% после внедрения системы</p>
                    </div>
                </div>
            </div>
        </div>
    </section> -->
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