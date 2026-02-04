<?php
// app/Http\Controllers/Auth/YandexAuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class YandexAuthController extends Controller
{
    public function redirect()
    {
        try {
            return Socialite::driver('yandex')
                ->redirect();
        } catch (\Exception $e) {
            \Log::error('Yandex redirect error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Ошибка подключения к Яндекс OAuth');
        }
    }

    public function callback(Request $request)
    {
        try {
            \Log::info('Yandex callback received', [
                'all_params' => $request->all(),
                'has_code' => $request->has('code'),
                'code' => $request->get('code'),
                'error' => $request->get('error'),
                'error_description' => $request->get('error_description')
            ]);
            
            // проверка ошибок от яндекса
            if ($request->has('error')) {
                $error = $request->error;
                $errorDescription = $request->error_description ?? 'Неизвестная ошибка';
                
                \Log::error("Yandex OAuth Error: {$error} - {$errorDescription}");
                
                return redirect()->route('login')
                    ->with('error', "Ошибка авторизации: {$errorDescription}");
            }

            // проверка кода авторизации
            if (!$request->has('code')) {
                \Log::error('Yandex OAuth: No authorization code received');
                return redirect()->route('login')
                    ->with('error', 'Не получен код авторизации от Яндекс');
            }

            // ============================================
            // АЛЬТЕРНАТИВНАЯ ПРОВЕРКА: Прямой запрос к API Яндекса
            // ============================================
            
            $code = $request->get('code');
            $client = new \GuzzleHttp\Client();
            
            \Log::info('Starting direct API call to Yandex...');
            
            // 1. Получаем access token
            $tokenResponse = $client->post('https://oauth.yandex.ru/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'client_id' => env('YANDEX_CLIENT_ID'),
                    'client_secret' => env('YANDEX_CLIENT_SECRET'),
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);
            
            $tokenData = json_decode($tokenResponse->getBody(), true);
            \Log::info('Token response from Yandex', $tokenData);
            
            if (!isset($tokenData['access_token'])) {
                throw new \Exception('Не получен access_token от Яндекс. Ответ: ' . json_encode($tokenData));
            }
            
            $accessToken = $tokenData['access_token'];
            
            // 2. Получаем данные пользователя
            $userResponse = $client->get('https://login.yandex.ru/info', [
                'headers' => [
                    'Authorization' => 'OAuth ' . $accessToken,
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'format' => 'json',
                ],
            ]);
            
            $yandexData = json_decode($userResponse->getBody(), true);
            \Log::info('User data from Yandex API', $yandexData);
            
            // 3. Создаем объект пользователя, совместимый с Socialite
            $yandexUser = (new \Laravel\Socialite\Two\User())->setRaw($yandexData)->map([
                'id' => $yandexData['id'] ?? null,
                'email' => $yandexData['default_email'] ?? ($yandexData['emails'][0] ?? null),
                'name' => trim(($yandexData['last_name'] ?? '') . ' ' . ($yandexData['first_name'] ?? '')),
                'nickname' => $yandexData['login'] ?? null,
                'avatar' => isset($yandexData['default_avatar_id']) 
                    ? 'https://avatars.yandex.net/get-yapic/' . $yandexData['default_avatar_id'] . '/islands-200' 
                    : null,
            ]);
            
            // Добавляем дополнительные данные, если они нужны
            $yandexUser->accessTokenResponseBody = $tokenData;
            
            \Log::info('Created Socialite-compatible user object', [
                'id' => $yandexUser->getId(),
                'email' => $yandexUser->getEmail(),
                'name' => $yandexUser->getName(),
            ]);
            
            // ============================================
            // КОНЕЦ АЛЬТЕРНАТИВНОЙ ПРОВЕРКИ
            // ============================================
            
            // Продолжаем стандартную обработку...
            if (!$yandexUser->getEmail()) {
                throw new \Exception('Не удалось получить email от Яндекс');
            }

            // поиск Яндекс ID или email
            $user = User::where('yandex_id', $yandexUser->getId())
                ->orWhere('email', $yandexUser->getEmail())
                ->first();

            if ($user) {
                // Обновляем Яндекс ID если пользователь уже существует
                if (!$user->yandex_id) {
                    $user->update(['yandex_id' => $yandexUser->getId()]);
                }
                
                // Обновляем аватар если он изменился
                if ($yandexUser->getAvatar() && $user->avatar !== $yandexUser->getAvatar()) {
                    $user->update(['avatar' => $yandexUser->getAvatar()]);
                }
            } else {
                // Создаем нового пользователя
                $nameParts = $this->parseYandexName($yandexUser->getName());
                
                $user = User::create([
                    'yandex_id' => $yandexUser->getId(),
                    'name' => $nameParts['name'],
                    'surname' => $nameParts['surname'],
                    'fathername' => $nameParts['fathername'],
                    'email' => $yandexUser->getEmail(),
                    'phone' => $this->generateTemporaryPhone(),
                    'avatar' => $yandexUser->getAvatar(),
                    'password' => Hash::make(Str::random(32)),
                    'role_id' => Role::where('name', 'client')->first()->id,
                    'email_verified_at' => now(),
                ]);

                \Log::info("New user created via Yandex OAuth: {$user->email}");
            }
            
            // Авторизуем пользователя
            Auth::login($user, true);
            
            \Log::info("User logged in via Yandex OAuth: {$user->email}");

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Успешный вход через Яндекс!');
            
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Логируем детали HTTP-ошибки
            $response = $e->getResponse();
            \Log::error('HTTP Client Exception', [
                'status_code' => $response->getStatusCode(),
                'response_body' => (string) $response->getBody(),
                'request_url' => $e->getRequest()->getUri(),
                'request_headers' => $e->getRequest()->getHeaders(),
            ]);
            
            return redirect()->route('login')
                ->with('error', 'Ошибка подключения к API Яндекс: ' . $response->getStatusCode());
                
        } catch (\Exception $e) {
            \Log::error('Yandex OAuth Callback Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('login')
                ->with('error', 'Ошибка авторизации через Яндекс: ' . $e->getMessage());
        }
    }
    
    private function parseYandexName(string $fullName): array
    {
        $parts = array_filter(explode(' ', trim($fullName)));
        
        return [
            'surname' => $parts[0] ?? '',
            'name' => $parts[1] ?? '',
            'fathername' => $parts[2] ?? '',
        ];
    }
    
    private function generateTemporaryPhone(): string
    {
        return '70000000000';
    }
}