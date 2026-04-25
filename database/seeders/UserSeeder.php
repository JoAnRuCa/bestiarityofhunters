<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $password = Hash::make('contrasena');

        
        User::create([ 'name' => 'Master Hunter', 'role' => 'admin', 'email' => 'admin@bestiarityofhunters.com', 'password' => $password]);
        User::create([ 'name' => 'name2', 'role' => 'user', 'email' => 'email2@gmail.com', 'password' => $password]);
        User::create([ 'name' => 'name23457890', 'role' => 'user', 'email' => 'email3@gmail.com', 'password' => $password]);
        User::create([ 'name' => 'name2343', 'role' => 'user', 'email' => 'email4@gmail.com', 'password' => $password]);
        User::create([ 'name' => 'name23434', 'role' => 'user', 'email' => 'email5@gmail.com', 'password' => $password]);
        User::create([ 'name' => 'name23437', 'role' => 'user', 'email' => 'email6@gmail.com', 'password' => $password]);
        User::create([ 'name' => 'name23431', 'role' => 'user', 'email' => 'email7@gmail.com', 'password' => $password]);
        User::create([ 'name' => 'name234321', 'role' => 'user', 'email' => 'email8@gmail.com', 'password' => $password]);
        User::create([ 'name' => 'name2456', 'role' => 'user', 'email' => 'email9@gmail.com', 'password' => $password]);
        User::create([ 'name' => 'name67894', 'role' => 'user', 'email' => 'email10@gmail.com', 'password' => $password]);
        User::create([ 'name' => 'nombreReal', 'role' => 'user', 'email' => 'email11@gmail.com', 'password' => $password]);
        User::create([ 'name' => 'name0986', 'role' => 'user', 'email' => 'email12@gmail.com', 'password' => $password]);
    }
}