<?php
// app/Http/Controllers/WarehouseController.php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::withCount(['stocks', 'orders'])
            ->orderBy('name')
            ->paginate(15);

        return view('warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'country' => 'required|string|max:45',
            'city' => 'required|string|max:60',
            'street' => 'required|string|max:60',
            'house_number' => 'required|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $warehouse = Warehouse::create($validated);

        return redirect()->route('warehouses.show', $warehouse)
            ->with('success', 'Склад успешно создан.');
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['stocks.product.category', 'stocks.product.manufacturer', 'orders.user']);
        
        // Статистика по остаткам
        $stockStats = [
            'total_products' => $warehouse->stocks->count(),
            'in_stock' => $warehouse->stocks->where('quantity', '>', 0)->count(),
            'low_stock' => $warehouse->stocks->where('quantity', '<', 10)->where('quantity', '>', 0)->count(),
            'out_of_stock' => $warehouse->stocks->where('quantity', 0)->count(),
            'total_quantity' => $warehouse->stocks->sum('quantity'),
        ];

        return view('warehouses.show', compact('warehouse', 'stockStats'));
    }

    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'country' => 'required|string|max:45',
            'city' => 'required|string|max:60',
            'street' => 'required|string|max:60',
            'house_number' => 'required|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $warehouse->update($validated);

        return redirect()->route('warehouses.show', $warehouse)
            ->with('success', 'Склад успешно обновлен.');
    }

    public function destroy(Warehouse $warehouse)
    {
        // Проверяем, есть ли связанные записи
        if ($warehouse->stocks()->exists() || $warehouse->orders()->exists() || 
            $warehouse->fromMovements()->exists() || $warehouse->toMovements()->exists()) {
            return redirect()->route('warehouses.index')
                ->with('error', 'Невозможно удалить склад: имеются связанные товары, заявки или перемещения.');
        }

        $warehouse->delete();

        return redirect()->route('warehouses.index')
            ->with('success', 'Склад успешно удален.');
    }

    /**
     * Получение координат по адресу через Яндекс Геокодер
     */
            public function geocode(Request $request)
    {
        $address = $request->input('address');
        
        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Адрес не указан'
            ], 400);
        }

        // Пробуем разные ключи
        $apiKey = env('YANDEX_GEOCODER_API_KEY') ?? env('YANDEX_MAPS_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API ключ не настроен. Проверьте YANDEX_GEOCODER_API_KEY в .env файле.'
            ], 400);
        }

        Log::info('Geocoding attempt', ['address' => $address]);

        try {
            $response = Http::timeout(10)
                ->get('https://geocode-maps.yandex.ru/1.x/', [
                    'apikey' => $apiKey,
                    'geocode' => $address,
                    'format' => 'json',
                    'results' => 1,
                    'lang' => 'ru_RU',
                ]);

            $data = $response->json();

            // Логируем полный ответ для отладки
            Log::debug('Geocoder full response', ['response' => $data]);

            // Проверяем наличие ошибки в ответе Яндекса
            if (isset($data['error'])) {
                $errorMessage = $this->getYandexErrorMessage($data['error']);
                Log::error('Yandex API error', ['error' => $data['error']]);
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error_code' => $data['error']['code'] ?? 'unknown'
                ]);
            }

            // Проверяем структуру ответа
            if (!isset($data['response']['GeoObjectCollection'])) {
                Log::error('Invalid response structure', ['data' => $data]);
                return response()->json([
                    'success' => false,
                    'message' => 'Неверный формат ответа от сервиса геокодирования'
                ]);
            }

            $found = $data['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['found'] ?? 0;

            if ($found > 0 && isset($data['response']['GeoObjectCollection']['featureMember'][0])) {
                $geoObject = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'];
                $pos = $geoObject['Point']['pos'];
                list($longitude, $latitude) = explode(' ', $pos);

                $fullAddress = $geoObject['metaDataProperty']['GeocoderMetaData']['text'] ?? $address;

                Log::info('Geocoding successful', [
                    'address' => $address,
                    'coordinates' => [$latitude, $longitude]
                ]);

                return response()->json([
                    'success' => true,
                    'latitude' => (float)$latitude,
                    'longitude' => (float)$longitude,
                    'address' => $fullAddress,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Адрес не найден. Проверьте правильность написания.',
                'found_count' => $found
            ]);

        } catch (\Exception $e) {
            Log::error('Geocoding exception', [
                'message' => $e->getMessage(),
                'address' => $address
            ]);

            $errorMessage = 'Ошибка соединения: ' . $e->getMessage();
            
            if (str_contains($e->getMessage(), 'cURL error 28')) {
                $errorMessage = 'Таймаут запроса. Попробуйте еще раз.';
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }
    }

    /**
     * Преобразует код ошибки Яндекса в читаемое сообщение
     */
    private function getYandexErrorMessage($error)
    {
        $code = $error['code'] ?? 'unknown';
        
        $messages = [
            '403' => 'Доступ запрещен. Проверьте API ключ.',
            '400' => 'Неверный запрос. Проверьте формат адреса.',
            '500' => 'Внутренняя ошибка сервера Яндекса.',
            'unknown' => 'Неизвестная ошибка сервиса геокодирования.'
        ];

        return $messages[$code] ?? $messages['unknown'];
    }


    /**
     * Получение всех складов для карты
     */
    public function map()
    {
        $warehouses = Warehouse::withCount(['stocks', 'orders'])
            ->orderBy('name')
            ->get();

        return view('warehouses.map', compact('warehouses'));
    }

    public function mapData()
    {
        $warehouses = Warehouse::withCount(['stocks', 'orders'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($warehouse) {
                return [
                    'id' => $warehouse->id,
                    'name' => $warehouse->name,
                    'latitude' => $warehouse->latitude,
                    'longitude' => $warehouse->longitude,
                    'address' => $warehouse->getFullAddressAttribute(),
                    'stocks_count' => $warehouse->stocks_count,
                    'orders_count' => $warehouse->orders_count,
                    'url' => route('warehouses.show', $warehouse),
                ];
            });

        return response()->json($warehouses);
    }
}