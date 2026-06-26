<?php

namespace Database\Factories;

use App\Models\Machine;
use App\Models\Plant;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Machine>
 */
class MachineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $plantId = Plant::inRandomOrder()->value('id') ?: Plant::create(['name' => $this->faker->company()])->id;
        $statusId = Status::inRandomOrder()->value('id') ?: Status::create(['name' => $this->faker->randomElement(['Operational', 'Out of Service', 'Not in Use'])])->id;

        return [
            'control_no' => $this->faker->unique()->bothify('W-??-###-###'),
            'name' => $this->faker->unique()->word() . ' Machine',
            'brand' => $this->faker->company(),
            'model' => 'Model ' . strtoupper($this->faker->bothify('??')),
            'serial_no' => $this->faker->unique()->regexify('SN[0-9]{6}'),
            'supplier' => $this->faker->company(),
            'arrived_date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'location' => $this->faker->randomElement(['Production', 'Warehouse', 'Assembly', 'Maintenance']),
            'dimension' => $this->faker->numerify('##x##x## cm'),
            'weight' => $this->faker->numerify('### kg'),
            'electrical' => $this->faker->randomElement(['220V', '380V', '110V']),
            'currency' => $this->faker->randomElement(['MMK', 'USD', 'SGD', 'JPY', 'CNY']),
            'unit_price' => $this->faker->randomFloat(2, 100, 100000),
            'is_fixed_asset' => $this->faker->boolean(80),
            'remark' => $this->faker->sentence(),
            'plant_id' => $plantId,
            'status_id' => $statusId,
            'category' => $this->faker->randomElement(['Production', 'Facility', 'Measurement', 'General']),
        ];
    }
}
