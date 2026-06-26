<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['System', 'Engineering', 'Production', 'Management', 'Admin', 'Account', 'Procurement'] as $name) {
            Department::updateOrCreate(
                ['name' => $name],
                ['name' => $name],
            );
        }
    }
}
