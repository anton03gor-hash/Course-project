{{-- resources/views/reports/orders-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Отчет по выданным заявкам</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header .subtitle {
            color: #666;
            font-size: 14px;
        }
        .info-section {
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .info-section h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 16px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #343a40;
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        .table td {
            padding: 8px 10px;
            border: 1px solid #dee2e6;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,.05);
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .mb-3 { margin-bottom: 15px; }
        .mt-3 { margin-top: 15px; }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: black; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-info { background: #17a2b8; color: white; }
        .summary {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Отчет по выданным заявкам</h1>
        <div class="subtitle">
            Период: {{ $startDate->format('d.m.Y') }} - {{ $endDate->format('d.m.Y') }}
            @if($selectedWarehouse)
                <br>Склад: {{ $selectedWarehouse->name }}
            @endif
        </div>
    </div>

    <div class="info-section">
        <h3>Общая информация</h3>
        <div class="row">
            <div class="col-6">
                <strong>Дата формирования:</strong> {{ now()->format('d.m.Y H:i') }}
            </div>
            <div class="col-6">
                <strong>Всего заявок:</strong> {{ $orders->count() }}
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <strong>Общее количество товаров:</strong> {{ $orders->sum(function($order) { return $order->products->sum('pivot.quantity'); }) }}
            </div>
            <div class="col-6">
                <strong>Уникальных товаров:</strong> {{ $orders->flatMap->products->unique('id')->count() }}
            </div>
        </div>
    </div>

    @if($orders->count() > 0)
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID заявки</th>
                    <th>Клиент</th>
                    <th>Склад</th>
                    <th class="text-center">Товаров</th>
                    <th class="text-center">Количество</th>
                    <th>Дата создания</th>
                    <th>Дата выполнения</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td class="text-bold">#{{ $order->id }}</td>
                    <td>{{ $order->user->full_name }}</td>
                    <td>{{ $order->warehouse->name }}</td>
                    <td class="text-center">{{ $order->products->count() }}</td>
                    <td class="text-center">{{ $order->products->sum('pivot.quantity') }}</td>
                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                    <td>{{ $order->updated_at->format('d.m.Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <h3>Итоги</h3>
            <div class="row">
                <div class="col-4">
                    <strong>Всего заявок:</strong> {{ $orders->count() }}
                </div>
                <div class="col-4">
                    <strong>Общее количество товаров:</strong> {{ $orders->sum(function($order) { return $order->products->sum('pivot.quantity'); }) }}
                </div>
                <div class="col-4">
                    <strong>Уникальных клиентов:</strong> {{ $orders->unique('user_id')->count() }}
                </div>
            </div>
        </div>
    @else
        <div class="text-center" style="padding: 40px; color: #6c757d;">
            <h3>Нет данных за выбранный период</h3>
            <p>За указанный период не было выполненных заявок</p>
        </div>
    @endif

    <div class="footer">
        Сформировано: {{ now()->format('d.m.Y H:i') }} | 
        Система управления складом и запасами
    </div>
</body>
</html>