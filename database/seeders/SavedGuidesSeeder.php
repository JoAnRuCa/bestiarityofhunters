<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SavedGuidesSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('saved_guides')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('saved_guides')->insert(array (
            ['id' => 1,  'user_id' => 2,  'guide_id' => 1],
            ['id' => 2,  'user_id' => 4,  'guide_id' => 15],
            ['id' => 3,  'user_id' => 4,  'guide_id' => 2],
            ['id' => 4,  'user_id' => 6,  'guide_id' => 17],
            ['id' => 8,  'user_id' => 7,  'guide_id' => 16],
            ['id' => 10, 'user_id' => 9,  'guide_id' => 16],
            ['id' => 12, 'user_id' => 10, 'guide_id' => 18],
            ['id' => 13, 'user_id' => 12, 'guide_id' => 22],
        ));
    }
}