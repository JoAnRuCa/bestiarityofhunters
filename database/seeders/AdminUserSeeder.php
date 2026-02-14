<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Master Hunter',
            'email' => 'admin@bestiarityofhunters.com', // Cambia esto por el email que quieras
            'password' => Hash::make('contrasena'), // Cambia esto por una contraseña segura
            'role' => 'admin',
        ]);
    }
}