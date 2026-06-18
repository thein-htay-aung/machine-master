<?php

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\Part;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MachinePartSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $machines = Machine::all();
        $parts = Part::pluck('id')->all();

        if (empty($machines) || empty($parts)) {
            return;
        }

        foreach ($machines as $machine) {
            $selected = (array) array_rand(array_flip($parts), rand(1, min(6, count($parts))));
            $sync = [];
            foreach ($selected as $partId) {
                $sync[$partId] = ['quantity' => rand(1, 10)];
            }
            $machine->parts()->sync($sync);
        }
    }
}
