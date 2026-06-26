<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Air Cleaner',
            'Bearing',
            'Chemical',
            'Common',
            'Conveyor',
            'Electrical',
            'Grease & Oil',
            'Motor Belt',
            'Oil Filter',
            'Pneumatic',
            'Water',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(['name' => $name], ['name' => $name, 'plant_id' => 1]);
        }
    }
}
