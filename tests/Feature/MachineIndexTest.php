<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\Plant;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MachineIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_machine_index_can_filter_by_category(): void
    {
        $role = Role::create([
            'name' => 'viewer',
            'description' => 'Viewer',
        ]);

        $plant = Plant::create(['name' => 'All']);
        $status = Status::create(['name' => 'Operational']);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'plant_id' => $plant->id,
        ]);

        Machine::factory()->create([
            'category' => 'Production',
            'plant_id' => $plant->id,
            'status_id' => $status->id,
        ]);

        Machine::factory()->create([
            'category' => 'Facility',
            'plant_id' => $plant->id,
            'status_id' => $status->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('machines.index', ['category' => 'Production']));

        $response->assertOk();

        $machines = $response->viewData('machines');

        $this->assertCount(1, $machines);
        $this->assertSame('Production', $machines->first()->category);
    }
}
