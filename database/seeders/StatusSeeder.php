<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Operational', 'Out of Service', 'Not in Use'] as $name) {
            Status::firstOrCreate(['name' => $name]);
        }
    }
}
