<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'member', 'guard_name' => 'web']);

        $super_admin = User::create([
            'username' => 'dava27',
            'email' => 'davamoreno28@gmail.com',
            'password' => bcrypt('22446688'),
        ]);

        $super_admin->assignRole(UserRole::SUPER_ADMIN->value);
    }
}
