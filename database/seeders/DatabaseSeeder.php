<?php

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\Plant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(RoleSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(PlantSeeder::class);
        $this->call(StatusSeeder::class);
        // $this->call(UnitSeeder::class);
        // $this->call(CategorySeeder::class);
        // $this->call(MachineSeeder::class);
        $this->call(SuperAdminSeeder::class);
    }
}
