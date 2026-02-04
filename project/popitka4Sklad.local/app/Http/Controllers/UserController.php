<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    //     public function index(Request $request)
    // {
    //     $query = User::with(['role', 'orders'])
    //         ->withCount('orders')
    //         ->orderBy('created_at', 'desc');

    //     // Фильтр по роли
    //     if ($request->has('role') && $request->role) {
    //         $query->whereHas('role', function($q) use ($request) {
    //             $q->where('name', $request->role);
    //         });
    //     }

    //     // Фильтр по типу авторизации
    //     if ($request->has('auth_type') && $request->auth_type) {
    //         if ($request->auth_type === 'yandex') {
    //             $query->whereNotNull('yandex_id');
    //         } elseif ($request->auth_type === 'local') {
    //             $query->whereNull('yandex_id');
    //         }
    //     }

    //     // Поиск по имени или email
    //     if ($request->has('search') && $request->search) {
    //         $search = $request->search;
    //         $query->where(function($q) use ($search) {
    //             $q->where('name', 'like', "%{$search}%")
    //               ->orWhere('surname', 'like', "%{$search}%")
    //               ->orWhere('email', 'like', "%{$search}%")
    //               ->orWhereRaw("CONCAT(name, ' ', surname) LIKE ?", ["%{$search}%"]);
    //         });
    //     }

    //     $users = $query->paginate(15);
    //     $roles = Role::all();
        
    //     return view('users.index', compact('users', 'roles'));
    // }
    public function index(Request $request){
        $query = User::withCount('role');
                // Поиск по названию
        if ($request->has('search') && $request->search) {
            // $query->where('name', 'like', '%' . $request->search . '%');
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(name, ' ', surname) LIKE ?", ["%{$search}%"]);
            });
        }
        // Поиск по типу авторизации
        if ($request->has('auth_type') && $request->auth_type) {
            if ($request->auth_type === 'yandex') {
                $query->whereNotNull('yandex_id');
            } elseif ($request->auth_type === 'local') {
                $query->whereNull('yandex_id');
            }
            // $query->where('name', 'like', '%' . $request->auth_type . '%');
        }

        // Поиск по роли
        if ($request->has('role') && $request->role) {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->latest()->paginate(10);
        // $categories = Category::withCount('products')
        //     ->orderBy('name')
        //     ->paginate(15);

        return view('users.index', compact('users'));
    }
    // public function index()
    // {
    //     $users = User::with('role')
    //         ->orderBy('surname')
    //         ->orderBy('name')
    //         ->paginate(15);
            
    //     $roles = Role::all();
        
    //     return view('users.index', compact('users', 'roles'));
    // }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'surname' => 'required|string|max:45',
            'name' => 'required|string|max:45',
            'fathername' => 'nullable|string|max:45',
            'phone' => 'required|string|unique:users',
            'email' => 'required|string|email|max:60|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'surname' => $validated['surname'],
            'name' => $validated['name'],
            'fathername' => $validated['fathername'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
        ]);

        return redirect()->route('users.show', $user)
            ->with('success', 'Пользователь успешно создан.');
    }

    public function show(User $user)
    {
        $user->load(['role', 'orders.warehouse', 'orders.products']);
        
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'surname' => 'required|string|max:45',
            'name' => 'required|string|max:45',
            'fathername' => 'nullable|string|max:45',
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'email' => 'required|string|email|max:60|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id',
        ]);

        $updateData = [
            'surname' => $validated['surname'],
            'name' => $validated['name'],
            'fathername' => $validated['fathername'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
        ];

        // Обновляем пароль только если он указан
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('users.show', $user)
            ->with('success', 'Пользователь успешно обновлен.');
    }

    public function destroy(User $user)
    {
        // Нельзя удалить самого себя
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Нельзя удалить собственный аккаунт.');
        }

        // Проверяем, есть ли связанные заявки
        if ($user->orders()->exists()) {
            return redirect()->route('users.index')
                ->with('error', 'Невозможно удалить пользователя: имеются связанные заявки.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Пользователь успешно удален.');
    }

}