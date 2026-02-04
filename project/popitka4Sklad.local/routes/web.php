<?php
// routes/web.php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\Auth\YandexAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request; 

// Главная страница
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Яндекс OAuth
Route::get('/auth/yandex/redirect', [YandexAuthController::class, 'redirect'])->name('yandex.redirect');
Route::get('/auth/yandex/callback', [YandexAuthController::class, 'callback'])->name('yandex.callback');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Единые страницы для всех ролей
Route::middleware(['auth'])->group(function () {

    // Dashboard
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Товары
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    // Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    // Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    // Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    // Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Склады
    // Статические маршруты (без параметров) - ДОЛЖНЫ БЫТЬ ПЕРВЫМИ
    Route::get('/warehouses/geocode', [WarehouseController::class, 'geocode'])->name('warehouses.geocode');
    Route::get('/warehouses/map', [WarehouseController::class, 'map'])->name('warehouses.map');
    Route::get('/warehouses/map/data', [WarehouseController::class, 'mapData'])->name('warehouses.map.data');
    // Route::get('/warehouses/create', [WarehouseController::class, 'create'])->name('warehouses.create');
    // Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
    
    // Динамические маршруты (с параметрами) - ДОЛЖНЫ БЫТЬ ПОСЛЕ статических
    // Route::get('/warehouses/{warehouse}', [WarehouseController::class, 'show'])->name('warehouses.show');
    // Route::get('/warehouses/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('warehouses.edit');
    // Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');
    // Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');

    
//     Route::get('/test-geocode', function (Request $request) { // Используем инъекцию Request
//     $address = $request->input('address', 'Россия, Москва, Красная площадь, 1');
//     $apiKey = env('YANDEX_GEOCODER_API_KEY') ?? env('YANDEX_MAPS_API_KEY');
    
//     // Проверяем наличие ключа
//     if (!$apiKey) {
//         return response()->json([
//             'error' => 'API ключ не настроен',
//             'geocoder_key' => env('YANDEX_GEOCODER_API_KEY') ? 'set' : 'not set',
//             'maps_key' => env('YANDEX_MAPS_API_KEY') ? 'set' : 'not set'
//         ]);
//     }
    
//     try {
//         $response = Http::timeout(10)->get('https://geocode-maps.yandex.ru/1.x/', [
//             'apikey' => $apiKey,
//             'geocode' => $address,
//             'format' => 'json',
//             'results' => 1,
//         ]);
        
//         $data = $response->json();
        
//         return response()->json([
//             'success' => $response->successful(),
//             'status' => $response->status(),
//             'api_key_set' => !empty($apiKey),
//             'api_key_prefix' => substr($apiKey, 0, 8) . '...',
//             'request_address' => $address,
//             'response_found' => $data['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['found'] ?? 0,
//             'has_results' => isset($data['response']['GeoObjectCollection']['featureMember'][0]),
//             'raw_response' => $data // Полный ответ для отладки
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'error' => $e->getMessage(),
//             'api_key_set' => !empty($apiKey),
//             'api_key_prefix' => $apiKey ? substr($apiKey, 0, 8) . '...' : 'not set'
//         ], 500);
//     }
// });

    // Заявки
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    // Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    
    // Остатки
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
    // Route::get('/stocks/create', [StockController::class, 'create'])->name('stocks.create');
    // Route::post('/stocks', [StockController::class, 'store'])->name('stocks.store');
    Route::get('/stocks/{stock}', [StockController::class, 'show'])->name('stocks.show');
    // Route::get('/stocks/{stock}/edit', [StockController::class, 'edit'])->name('stocks.edit');
    // Route::put('/stocks/{stock}', [StockController::class, 'update'])->name('stocks.update');
    // Route::delete('/stocks/{stock}', [StockController::class, 'destroy'])->name('stocks.destroy');

    // Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    // Route::get('/reports/orders', [ReportController::class, 'ordersReport'])->name('reports.orders');
    // Route::get('/reports/movements', [ReportController::class, 'movementsReport'])->name('reports.movements');
    // Route::get('/reports/stocks', [ReportController::class, 'stocksReport'])->name('reports.stocks');
});

// Пользователи (только для админа)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/warehouses/create', [WarehouseController::class, 'create'])->name('warehouses.create');
    Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
        Route::get('/warehouses/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('warehouses.edit');
    Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/show', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/manufacturers', [ManufacturerController::class, 'index'])->name('manufacturers.index');
    Route::get('/manufacturers/create', [ManufacturerController::class, 'create'])->name('manufacturers.create');
    Route::post('/manufacturers', [ManufacturerController::class, 'store'])->name('manufacturers.store');    
    Route::get('/manufacturers/{manufacturer}/edit',[ManufacturerController::class,'edit'])->name('manufacturers.edit');
    Route::put('/manufacturers/{manufacturer}', [ManufacturerController::class, 'update'])->name('manufacturers.update');
    Route::get('/manufacturers/{manufacturer}', [ManufacturerController::class, 'show'])->name('manufacturers.show');
    Route::delete('/manufacturers/{manufacturer}', [ManufacturerController::class, 'destroy'])->name('manufacturers.destroy');

    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders/{order}/check-movements', [OrderController::class, 'checkMovements'])->name('orders.checkMovements');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/orders', [ReportController::class, 'ordersReport'])->name('reports.orders');
    Route::get('/reports/movements', [ReportController::class, 'movementsReport'])->name('reports.movements');
    Route::get('/reports/stocks', [ReportController::class, 'stocksReport'])->name('reports.stocks');
});

// Перемещения (для сотрудников и админа)
Route::middleware(['auth', 'role:admin,employee'])->group(function () {
    Route::get('/movements', [MovementController::class, 'index'])->name('movements.index');
    Route::get('/movements/create', [MovementController::class, 'create'])->name('movements.create');
    Route::post('/movements', [MovementController::class, 'store'])->name('movements.store');
    Route::get('/movements/{movement}', [MovementController::class, 'show'])->name('movements.show');
    Route::patch('/movements/{movement}/complete', [MovementController::class, 'complete'])->name('movements.complete');

        Route::get('/stocks/create', [StockController::class, 'create'])->name('stocks.create');
    Route::post('/stocks', [StockController::class, 'store'])->name('stocks.store');
    Route::get('/stocks/{stock}/edit', [StockController::class, 'edit'])->name('stocks.edit');
    Route::put('/stocks/{stock}', [StockController::class, 'update'])->name('stocks.update');
    Route::delete('/stocks/{stock}', [StockController::class, 'destroy'])->name('stocks.destroy');

});

// Перемещения - редактирование и удаление (только для админа)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/movements/{movement}/edit', [MovementController::class, 'edit'])->name('movements.edit');
    Route::put('/movements/{movement}', [MovementController::class, 'update'])->name('movements.update');
    Route::delete('/movements/{movement}', [MovementController::class, 'destroy'])->name('movements.destroy');
});

// Профиль
Route::middleware('auth')->group(function () {

        Route::get('/warehouses/{warehouse}', [WarehouseController::class, 'show'])->name('warehouses.show');
    // Route::get('/warehouses/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('warehouses.edit');
    // Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');
    Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';