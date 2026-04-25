<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildsVotesSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('builds_votes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('builds_votes')->insert(array (
            ['id' => 1,  'user_id' => 2,  'build_id' => 3,  'tipo' => 1],
            ['id' => 2,  'user_id' => 3,  'build_id' => 7,  'tipo' => 1],
            ['id' => 3,  'user_id' => 3,  'build_id' => 8,  'tipo' => 1],
            ['id' => 7,  'user_id' => 2,  'build_id' => 11, 'tipo' => 1],
            ['id' => 11, 'user_id' => 6,  'build_id' => 16, 'tipo' => -1],
            ['id' => 12, 'user_id' => 6,  'build_id' => 11, 'tipo' => 1],
            ['id' => 17, 'user_id' => 7,  'build_id' => 13, 'tipo' => 1],
            ['id' => 19, 'user_id' => 8,  'build_id' => 16, 'tipo' => 1],
            ['id' => 20, 'user_id' => 8,  'build_id' => 15, 'tipo' => 1],
            ['id' => 22, 'user_id' => 9,  'build_id' => 16, 'tipo' => 1],
            ['id' => 24, 'user_id' => 9,  'build_id' => 15, 'tipo' => -1],
            ['id' => 26, 'user_id' => 10, 'build_id' => 18, 'tipo' => 1],
            ['id' => 30, 'user_id' => 12, 'build_id' => 18, 'tipo' => 1],
            ['id' => 31, 'user_id' => 12, 'build_id' => 16, 'tipo' => 1],
            ['id' => 33, 'user_id' => 12, 'build_id' => 23, 'tipo' => 1], 
        ));
    }
}