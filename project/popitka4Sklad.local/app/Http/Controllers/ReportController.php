<?php
// app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Movement;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Отчет по выполненным заявкам
     */
    public function ordersReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $query = Order::with(['user', 'warehouse', 'products.category', 'products.manufacturer'])
            ->where('status', 'received')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $orders = $query->get();
        $warehouses = Warehouse::all();
        $selectedWarehouse = $request->warehouse_id ? Warehouse::find($request->warehouse_id) : null;

        if ($request->has('download')) {
            $pdf = Pdf::loadView('reports.orders-pdf', compact('orders', 'startDate', 'endDate', 'selectedWarehouse'));
            
            $filename = 'orders_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d');
            if ($selectedWarehouse) {
                $filename .= '_' . Str::slug($selectedWarehouse->name);
            }
            $filename .= '.pdf';

            return $pdf->download($filename);
        }

        return view('reports.orders', compact('orders', 'startDate', 'endDate', 'warehouses', 'selectedWarehouse'));
    }

    /**
     * Отчет по перемещениям
     */
    public function movementsReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:in_progress,complete',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $query = Movement::with(['fromWarehouse', 'toWarehouse', 'product', 'order'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->warehouse_id) {
            $query->where(function($q) use ($request) {
                $q->where('from_warehouse_id', $request->warehouse_id)
                  ->orWhere('to_warehouse_id', $request->warehouse_id);
            });
        }

        $movements = $query->get();
        $warehouses = Warehouse::all();
        $selectedWarehouse = $request->warehouse_id ? Warehouse::find($request->warehouse_id) : null;

        if ($request->has('download')) {
            $pdf = Pdf::loadView('reports.movements-pdf', compact('movements', 'startDate', 'endDate', 'selectedWarehouse', 'request'));
            
            $filename = 'movements_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d');
            if ($selectedWarehouse) {
                $filename .= '_' . Str::slug($selectedWarehouse->name);
            }
            $filename .= '.pdf';

            return $pdf->download($filename);
        }

        return view('reports.movements', compact('movements', 'startDate', 'endDate', 'warehouses', 'selectedWarehouse', 'request'));
    }

    /**
     * Отчет по остаткам
     */
    public function stocksReport(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'status' => 'nullable|in:in_stock,low_stock,out_of_stock',
        ]);

        $query = Stock::with(['warehouse', 'product.category', 'product.manufacturer']);

        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->status) {
            switch ($request->status) {
                case 'in_stock':
                    $query->where('quantity', '>', 10);
                    break;
                case 'low_stock':
                    $query->where('quantity', '>', 0)->where('quantity', '<=', 10);
                    break;
                case 'out_of_stock':
                    $query->where('quantity', 0);
                    break;
            }
        }

        $stocks = $query->get();
        $warehouses = Warehouse::all();
        $selectedWarehouse = $request->warehouse_id ? Warehouse::find($request->warehouse_id) : null;

        if ($request->has('download')) {
            $pdf = Pdf::loadView('reports.stocks-pdf', compact('stocks', 'selectedWarehouse', 'request'));
            
            $filename = 'stocks_report_' . now()->format('Y-m-d');
            if ($selectedWarehouse) {
                $filename .= '_' . Str::slug($selectedWarehouse->name);
            }
            $filename .= '.pdf';

            return $pdf->download($filename);
        }

        return view('reports.stocks', compact('stocks', 'warehouses', 'selectedWarehouse', 'request'));
    }

    /**
     * Главная страница отчетов
     */
    public function index()
    {
        return view('reports.index');
    }
}