<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'admin'],
            ['name' => 'employee'], 
            ['name' => 'client'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}