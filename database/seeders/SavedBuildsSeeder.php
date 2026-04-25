<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SavedBuildsSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('saved_builds')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('saved_builds')->insert(array (
            ['id' => 1,  'user_id' => 2,  'build_id' => 1],
            ['id' => 2,  'user_id' => 2,  'build_id' => 2],
            ['id' => 4,  'user_id' => 4,  'build_id' => 10],
            ['id' => 5,  'user_id' => 2,  'build_id' => 11],
            ['id' => 6,  'user_id' => 5,  'build_id' => 13],
            ['id' => 7,  'user_id' => 6,  'build_id' => 15],
            ['id' => 13, 'user_id' => 8,  'build_id' => 16],
            ['id' => 16, 'user_id' => 9,  'build_id' => 15],
            ['id' => 17, 'user_id' => 10, 'build_id' => 18],
            ['id' => 19, 'user_id' => 12, 'build_id' => 16],
            ['id' => 20, 'user_id' => 12, 'build_id' => 22],
            ['id' => 22, 'user_id' => 12, 'build_id' => 13],
            // Se eliminó la entrada ID 24 porque el usuario 12 ya tenía guardada la build 16
        ));
    }
}