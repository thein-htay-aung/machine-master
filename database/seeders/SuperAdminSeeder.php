<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Plant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'noreply.wty@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role_id' => Role::query()->where('name', 'superadmin')->value('id'),
                'email_verified_at' => now(),
                'status' => true,
                'department_id' => Department::query()->where('name', 'System')->value('id'),
                'plant_id' => Plant::query()->where('name', 'All')->value('id'),
            ]
        );
    }
}
