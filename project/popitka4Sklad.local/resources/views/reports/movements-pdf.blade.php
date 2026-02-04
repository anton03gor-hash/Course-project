{{-- resources/views/reports/movements-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Отчет по перемещениям</title>
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
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: black; }
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
        <h1>Отчет по перемещениям товаров</h1>
        <div class="subtitle">
            Период: {{ $startDate->format('d.m.Y') }} - {{ $endDate->format('d.m.Y') }}
            @if($selectedWarehouse)
                <br>Склад: {{ $selectedWarehouse->name }}
            @endif
            @if($request->status)
                <br>Статус: {{ $request->status == 'complete' ? 'Выполнено' : 'В процессе' }}
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
                <strong>Всего перемещений:</strong> {{ $movements->count() }}
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <strong>Общее количество:</strong> {{ $movements->sum('quantity') }}
            </div>
            <div class="col-6">
                <strong>Выполнено:</strong> {{ $movements->where('status', 'complete')->count() }}
            </div>
        </div>
    </div>

    @if($movements->count() > 0)
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Товар</th>
                    <th>Из склада</th>
                    <th>В склад</th>
                    <th class="text-center">Количество</th>
                    <th>Тип</th>
                    <th>Статус</th>
                    <th>Дата создания</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movements as $movement)
                <tr>
                    <td class="text-bold">#{{ $movement->id }}</td>
                    <td>{{ $movement->product->name }}</td>
                    <td>{{ $movement->fromWarehouse->name }}</td>
                    <td>{{ $movement->toWarehouse->name }}</td>
                    <td class="text-center">{{ $movement->quantity }}</td>
                    <td>
                        @if($movement->type == 'for_order')
                            <span class="badge badge-info">Для заявки</span>
                        @else
                            <span class="badge badge-info">Между складами</span>
                        @endif
                    </td>
                    <td>
                        @if($movement->status == 'complete')
                            <span class="badge badge-success">Выполнено</span>
                        @else
                            <span class="badge badge-warning">В процессе</span>
                        @endif
                    </td>
                    <td>{{ $movement->created_at->format('d.m.Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <h3>Итоги</h3>
            <div class="row">
                <div class="col-3">
                    <strong>Всего перемещений:</strong> {{ $movements->count() }}
                </div>
                <div class="col-3">
                    <strong>Выполнено:</strong> {{ $movements->where('status', 'complete')->count() }}
                </div>
                <div class="col-3">
                    <strong>В процессе:</strong> {{ $movements->where('status', 'in_progress')->count() }}
                </div>
                <div class="col-3">
                    <strong>Общее количество:</strong> {{ $movements->sum('quantity') }}
                </div>
            </div>
        </div>
    @else
        <div class="text-center" style="padding: 40px; color: #6c757d;">
            <h3>Нет данных за выбранный период</h3>
            <p>За указанный период не было перемещений</p>
        </div>
    @endif

    <div class="footer">
        Сформировано: {{ now()->format('d.m.Y H:i') }} | 
        Система управления складом и запасами
    </div>
</body>
</html>