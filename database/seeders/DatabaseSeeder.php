<?php

namespace Database\Seeders;

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
        // User::factory(10)->create();

        $user = User::firstOrCreate([
            'email' => 'pauloricardo.silvadias@gmail.com',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('m20b30a0'),
        ]);

        // Criar papel Super Admin se não existir
        $role = Role::firstOrCreate(['name' => 'Super Admin']);

        // Atribuir papel ao usuário
        $user->assignRole($role);

        echo "✅ Super Admin criado com sucesso!\n";
    
    }
}
