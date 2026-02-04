{{-- resources/views/dashboard/parts/employee-stats.blade.php --}}
<div class="col-12">
    <div class="card-custom p-4">
        <h5 class="fw-bold mb-3">
            <i class="fas fa-tasks me-2"></i>Рабочая статистика
        </h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded">
                    <div class="h4 fw-bold text-warning mb-1">{{ $stats['pending_movements'] }}</div>
                    <div class="text-muted">Ожидают выполнения</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded">
                    <div class="h4 fw-bold text-success mb-1">{{ $stats['completed_movements_today'] }}</div>
                    <div class="text-muted">Выполнено сегодня</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded">
                    <div class="h4 fw-bold text-info mb-1">{{ $stats['active_orders'] }}</div>
                    <div class="text-muted">Активных заявок</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded">
                    <div class="h4 fw-bold text-primary mb-1">{{ $stats['total_movements'] }}</div>
                    <div class="text-muted">Всего перемещений</div>
                </div>
            </div>
        </div>
    </div>
</div>