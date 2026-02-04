{{-- resources/views/dashboard/parts/client-stats.blade.php --}}
<div class="col-12">
    <div class="card-custom p-4">
        <h5 class="fw-bold mb-3">
            <i class="fas fa-shopping-cart me-2"></i>Моя статистика
        </h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded">
                    <div class="h4 fw-bold text-primary mb-1">{{ $stats['my_orders'] }}</div>
                    <div class="text-muted">Всего заявок</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded">
                    <div class="h4 fw-bold text-warning mb-1">{{ $stats['pending_my_orders'] }}</div>
                    <div class="text-muted">Ожидают обработки</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded">
                    <div class="h4 fw-bold text-success mb-1">{{ $stats['completed_my_orders'] }}</div>
                    <div class="text-muted">Завершено</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded">
                    <div class="h4 fw-bold text-info mb-1">{{ $stats['total_products_ordered'] }}</div>
                    <div class="text-muted">Товаров заказано</div>
                </div>
            </div>
        </div>
    </div>
</div>