<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer le rôle superadmin
        $superAdminRole = Role::where('role', 'superadmin')->first();

        if (!$superAdminRole) {
            $this->command->error("Le rôle superadmin n'existe pas !");
            return;
        }

        // Vérifier si le superadmin existe déjà
        $exists = User::where('email', 'admin@neostart.tech')->first();

        if ($exists) {
            $this->command->info("Le super admin existe déjà.");
            return;
        }

        // Créer le super admin
        User::create([
            'id' => (string) Str::uuid(),
            'nom' => 'Super',
            'prenom' => 'Admin',
            'email' => 'admin@neostart.tech',
            'telephone' => '90000000',
            'password' => Hash::make('password'),
            'role_id' => $superAdminRole->id,
        ]);
    }
}
