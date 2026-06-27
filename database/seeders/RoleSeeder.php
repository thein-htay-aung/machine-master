<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'viewer', 'description' => 'Can view records and download Excel files'],
            ['name' => 'editor', 'description' => 'Viewer access plus create and edit records'],
            ['name' => 'admin', 'description' => 'Editor access plus delete records'],
            ['name' => 'superadmin', 'description' => 'Admin access plus user management'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }

        $legacyUserRole = Role::where('name', 'user')->first();
        $viewerRole = Role::where('name', 'viewer')->first();

        if ($legacyUserRole && $viewerRole && $legacyUserRole->id !== $viewerRole->id) {
            User::where('role_id', $legacyUserRole->id)->update(['role_id' => $viewerRole->id]);
            $legacyUserRole->delete();
        }
    }
}
