<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Role::updateOrCreate(
            ['name' => 'superadmin'],
            ['description' => 'Super Administrator']
        );
        \App\Models\Role::updateOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator']
        );
        \App\Models\Role::updateOrCreate(
            ['name' => 'user'],
            ['description' => 'Regular User']
        );
    }
}
