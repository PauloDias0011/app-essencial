<?php

namespace Database\Seeders;

use Althinect\FilamentSpatieRolesPermissions\Commands\Permission;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cria ou garante que as roles existam
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
    
        // Cria os usuários
        $paulo = User::factory()->create([
            'name' => 'Paulo Dias',
            'email' => 'pauloricardo.silvadias@espacodigitall.com.br',
            'password' => Hash::make('m20b30a0'),
        ]);
    

    
        // Atribui as roles
        $paulo->assignRole($superAdminRole);
        
    }
}
