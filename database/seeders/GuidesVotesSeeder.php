<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuidesVotesSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('guides_votes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('guides_votes')->insert(array (
            ['id' => 1,  'user_id' => 2,  'guide_id' => 3,  'tipo' => 1],
            ['id' => 2,  'user_id' => 2,  'guide_id' => 4,  'tipo' => 1],
            ['id' => 3,  'user_id' => 2,  'guide_id' => 5,  'tipo' => 1],
            ['id' => 4,  'user_id' => 2,  'guide_id' => 7,  'tipo' => 1],
            ['id' => 8,  'user_id' => 7,  'guide_id' => 17, 'tipo' => 1],
            ['id' => 9,  'user_id' => 7,  'guide_id' => 18, 'tipo' => 1],
            ['id' => 10, 'user_id' => 9,  'guide_id' => 19, 'tipo' => 1],
            ['id' => 12, 'user_id' => 12, 'guide_id' => 22, 'tipo' => 1],
            ['id' => 15, 'user_id' => 2,  'guide_id' => 27, 'tipo' => 1],
        ));
    }
}