<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Movement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $stats = $this->getDashboardStats($user);
        
        return view('dashboard', compact('user', 'stats'));
    }
    
    private function getDashboardStats($user)
    {
        $stats = [];
        
        if ($user->isAdmin()) {
            $stats = [
                'total_users' => User::count(),
                'total_products' => Product::count(),
                'total_warehouses' => Warehouse::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'total_orders' => Order::count(),
                'active_movements' => Movement::where('status', 'in_progress')->count(),
                'recent_orders' => Order::with(['user', 'warehouse'])
                    ->latest()
                    ->take(5)
                    ->get(),
                'low_stock_products' => Product::whereHas('stocks', function($query) {
                    $query->where('quantity', '<', 10);
                })->take(5)->get()
            ];
        } elseif ($user->isEmployee()) {
            $stats = [
                'pending_movements' => Movement::where('status', 'in_progress')->count(),
                'completed_movements_today' => Movement::where('status', 'complete')
                    ->whereDate('created_at', today())
                    ->count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'active_movements' => Movement::with(['fromWarehouse', 'toWarehouse', 'product'])
                    ->where('status', 'in_progress')
                    ->latest()
                    ->take(5)
                    ->get()
            ];
        } elseif ($user->isClient()) {
            $stats = [
                'my_orders' => Order::where('user_id', $user->id)->count(),
                'pending_orders' => Order::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->count(),
                'completed_orders' => Order::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->count(),
                'recent_orders' => Order::with('warehouse')
                    ->where('user_id', $user->id)
                    ->latest()
                    ->take(5)
                    ->get()
            ];
        }
        
        return $stats;
    }
}