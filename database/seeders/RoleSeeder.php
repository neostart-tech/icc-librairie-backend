<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['superadmin', 'admin', 'user'];

        foreach ($roles as $role) {
            Role::updateOrCreate([
                'id' => (string) Str::uuid(),
                'role' => $role,
            ]);
        }
    }
}
