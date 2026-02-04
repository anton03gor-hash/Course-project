{{-- resources/views/dashboard/parts/admin-stats.blade.php --}}
<div class="col-12">
    <div class="card-custom p-4">
        <h5 class="fw-bold mb-3">
            <i class="fas fa-chart-bar me-2"></i>Общая статистика системы
        </h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded">
                    <div class="h4 fw-bold text-primary mb-1">{{ $stats['total_users'] }}</div>
                    <div class="text-muted">Пользователей</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded">
                    <div class="h4 fw-bold text-success mb-1">{{ $stats['total_products'] }}</div>
                    <div class="text-muted">Товаров</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded">
                    <div class="h4 fw-bold text-warning mb-1">{{ $stats['total_warehouses'] }}</div>
                    <div class="text-muted">Складов</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded">
                    <div class="h4 fw-bold text-info mb-1">{{ $stats['total_orders'] }}</div>
                    <div class="text-muted">Заявок</div>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4 mb-3">
                <div class="text-center p-3 border rounded">
                    <div class="h5 fw-bold text-danger mb-1">{{ $stats['pending_orders'] }}</div>
                    <div class="text-muted">Ожидают обработки</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="text-center p-3 border rounded">
                    <div class="h5 fw-bold text-secondary mb-1">{{ $stats['active_movements'] }}</div>
                    <div class="text-muted">Активных перемещений</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="text-center p-3 border rounded">
                    <div class="h5 fw-bold text-warning mb-1">{{ $stats['low_stock_products'] }}</div>
                    <div class="text-muted">Товаров с низким запасом</div>
                </div>
            </div>
        </div>
    </div>
</div>