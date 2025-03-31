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
    public function run()
    {
        // Cria o papel de Super Admin caso não exista
        $role = Role::firstOrCreate(['name' => 'super-admin']);

        // Cria o usuário Super Admin
        $user = User::firstOrCreate(
            ['email' => 'pauloricardo.silvadias@gmail.com'],
            [
                'name' => 'Super Admin',
                'email' => 'pauloricardo.silvadias@gmail.com',
                'password' => Hash::make('password123'), // Troque pela senha desejada
            ]
        );

        // Atribui o papel de Super Admin ao usuário
        $user->assignRole($role);

        // Caso você queira adicionar permissões ao super admin
        $permissions = Permission::all();
        $role->syncPermissions($permissions);
    }
}
