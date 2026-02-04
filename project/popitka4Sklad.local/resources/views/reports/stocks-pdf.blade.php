{{-- resources/views/reports/stocks-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Отчет по остаткам</title>
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
            padding: 2px 2px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: black; }
        .badge-danger { background: #dc3545; color: white; }
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
        <h1>Отчет по остаткам товаров</h1>
        <div class="subtitle">
            Дата: {{ now()->format('d.m.Y') }}
            @if($selectedWarehouse)
                <br>Склад: {{ $selectedWarehouse->name }}
            @endif
            @if($request->status)
                <br>Статус: 
                @if($request->status == 'in_stock') В наличии
                @elseif($request->status == 'low_stock') Мало осталось
                @elseif($request->status == 'out_of_stock') Нет в наличии
                @endif
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
                <strong>Всего позиций:</strong> {{ $stocks->count() }}
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <strong>В наличии:</strong> {{ $stocks->where('quantity', '>', 0)->count() }}
            </div>
            <div class="col-6">
                <strong>Нет в наличии:</strong> {{ $stocks->where('quantity', 0)->count() }}
            </div>
        </div>
    </div>

    @if($stocks->count() > 0)
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>Категория</th>
                    <th>Производитель</th>
                    <th>Склад</th>
                    <th class="text-center">Кол-во</th>
                    <th>Позиция</th>
                    <th>Статус</th>
                    <th>Обновлен</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $stock)
                <tr>
                    <td class="text-bold">{{ $stock->product->name }}</td>
                    <td>{{ $stock->product->category->name }}</td>
                    <td>{{ $stock->product->manufacturer->name }}</td>
                    <td>{{ $stock->warehouse->name }}</td>
                    <td class="text-center">{{ $stock->quantity }}</td>
                    <td>{{ $stock->position ?? '—' }}</td>
                    <td>
                        @if($stock->quantity == 0)
                            <span class="badge badge-danger">Нет в наличии</span>
                        @elseif($stock->quantity < 10)
                            <span class="badge badge-warning">Мало осталось</span>
                        @else
                            <span class="badge badge-success">В наличии</span>
                        @endif
                    </td>
                    <td>{{ $stock->last_update->format('d.m.Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <h3>Итоги</h3>
            <div class="row">
                <div class="col-3">
                    <strong>Всего позиций:</strong> {{ $stocks->count() }}
                </div>
                <div class="col-3">
                    <strong>В наличии:</strong> {{ $stocks->where('quantity', '>', 0)->count() }}
                </div>
                <div class="col-3">
                    <strong>Мало осталось:</strong> {{ $stocks->where('quantity', '<', 10)->where('quantity', '>', 0)->count() }}
                </div>
                <div class="col-3">
                    <strong>Нет в наличии:</strong> {{ $stocks->where('quantity', 0)->count() }}
                </div>
            </div>
        </div>
    @else
        <div class="text-center" style="padding: 40px; color: #6c757d;">
            <h3>Нет данных</h3>
            <p>По выбранным критериям остатки не найдены</p>
        </div>
    @endif

    <div class="footer">
        Сформировано: {{ now()->format('d.m.Y H:i') }} | 
        Система управления складом и запасами
    </div>
</body>
</html>