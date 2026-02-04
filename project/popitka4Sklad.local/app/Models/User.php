<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'surname', 
        'fathername',
        'phone',
        'email',
        'password',
        'role_id',
        'yandex_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin()
    {
        return $this->role->name === 'admin';
    }

    public function isEmployee()
    {
        return $this->role->name === 'employee';
    }

    public function isClient()
    {
        return $this->role->name === 'client';
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->surname} {$this->name} {$this->fathername}");
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}