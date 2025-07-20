<?php

namespace Database\Seeders;

use App\Models\Areas;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $area = Areas::firstOrCreate([
            'name_area' => 'Área de Administración',
        ]);

        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'document' => 'abc123',
            'password' => Hash::make('admin'),
            'last_name' => 'Admin',
            'role' => 'admin',
            'area_id' => $area->id,
        ]);

        $admin->assignRole($adminRole);

        $user = User::factory()->create([
            'name' => 'user',
            'email' => 'user@gmail.com',
            'document' => 'def456',
            'password' => Hash::make('user'),
            'last_name' => 'User',
            'role' => 'user',
            'area_id' => $area->id,
        ]);

        $user->assignRole($userRole);
    }
}
