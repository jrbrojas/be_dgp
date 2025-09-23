<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'avatar' => '/img/avatars/thumb-2.jpg',
            'name' => 'Administrador',
            'email' => 'admin@cenepred.gob.pe',
            'password' => '12345',
        ])->assignRole(Role::firstWhere('name', 'admin'));;

        User::create([
            'avatar' => '/img/avatars/thumb-1.jpg',
            'name' => 'Usuario',
            'email' => 'usuario@cenepred.gob.pe',
            'password' => '12345',
        ])->assignRole(Role::firstWhere('name', 'usuario'));;
    }
}
