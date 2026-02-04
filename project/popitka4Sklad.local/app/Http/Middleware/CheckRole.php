<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Если пользователь не авторизован и требуется гостевая роль
        if (!$user) {
            if (in_array('guest', $roles)) {
                return $next($request);
            }
            return redirect()->route('login');
        }

        // Проверяем роли пользователя
        foreach ($roles as $role) {
            if ($user->role->name === $role) {
                return $next($request);
            }
        }

        // Если роль не подходит - редирект на главную
        return redirect()->route('welcome')->with('error', 'Доступ запрещен');
    }
}