<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuideTagSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('guide_tags')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('guide_tags')->insert(array (
            ['id' => 1,  'guide_id' => 1,  'tag_id' => 1],
            ['id' => 2,  'guide_id' => 1,  'tag_id' => 4],
            ['id' => 3,  'guide_id' => 1,  'tag_id' => 7],
            ['id' => 4,  'guide_id' => 2,  'tag_id' => 2],
            ['id' => 5,  'guide_id' => 2,  'tag_id' => 4],
            ['id' => 6,  'guide_id' => 3,  'tag_id' => 1],
            ['id' => 7,  'guide_id' => 3,  'tag_id' => 5],
            ['id' => 8,  'guide_id' => 3,  'tag_id' => 14],
            ['id' => 9,  'guide_id' => 4,  'tag_id' => 1],
            ['id' => 10, 'guide_id' => 4,  'tag_id' => 4],
            ['id' => 11, 'guide_id' => 5,  'tag_id' => 4],
            ['id' => 12, 'guide_id' => 5,  'tag_id' => 22],
            ['id' => 13, 'guide_id' => 6,  'tag_id' => 5],
            ['id' => 14, 'guide_id' => 6,  'tag_id' => 8],
            ['id' => 15, 'guide_id' => 7,  'tag_id' => 1],
            ['id' => 16, 'guide_id' => 7,  'tag_id' => 4],
            ['id' => 17, 'guide_id' => 8,  'tag_id' => 1],
            ['id' => 18, 'guide_id' => 8,  'tag_id' => 4],
            ['id' => 19, 'guide_id' => 9,  'tag_id' => 1],
            ['id' => 20, 'guide_id' => 9,  'tag_id' => 4],
            ['id' => 21, 'guide_id' => 10, 'tag_id' => 1],
            ['id' => 22, 'guide_id' => 10, 'tag_id' => 4],
            ['id' => 23, 'guide_id' => 11, 'tag_id' => 1],
            ['id' => 24, 'guide_id' => 11, 'tag_id' => 4],
            ['id' => 25, 'guide_id' => 11, 'tag_id' => 7],
            ['id' => 26, 'guide_id' => 12, 'tag_id' => 1],
            ['id' => 27, 'guide_id' => 12, 'tag_id' => 4],
            ['id' => 28, 'guide_id' => 13, 'tag_id' => 1],
            ['id' => 29, 'guide_id' => 13, 'tag_id' => 4],
            ['id' => 30, 'guide_id' => 14, 'tag_id' => 1],
            ['id' => 31, 'guide_id' => 14, 'tag_id' => 4],
            ['id' => 32, 'guide_id' => 15, 'tag_id' => 1],
            ['id' => 33, 'guide_id' => 15, 'tag_id' => 4],
            ['id' => 34, 'guide_id' => 15, 'tag_id' => 2],
            // Se eliminó el ID 35 que referenciaba al tag_id 23
            ['id' => 36, 'guide_id' => 16, 'tag_id' => 1],
            ['id' => 37, 'guide_id' => 16, 'tag_id' => 5],
            ['id' => 38, 'guide_id' => 16, 'tag_id' => 8],
            ['id' => 39, 'guide_id' => 17, 'tag_id' => 1],
            ['id' => 40, 'guide_id' => 17, 'tag_id' => 4],
            ['id' => 41, 'guide_id' => 18, 'tag_id' => 1],
            ['id' => 42, 'guide_id' => 18, 'tag_id' => 2],
            ['id' => 43, 'guide_id' => 18, 'tag_id' => 4],
            ['id' => 44, 'guide_id' => 18, 'tag_id' => 5],
            ['id' => 45, 'guide_id' => 19, 'tag_id' => 11],
            ['id' => 46, 'guide_id' => 19, 'tag_id' => 14],
            ['id' => 47, 'guide_id' => 19, 'tag_id' => 17],
            ['id' => 48, 'guide_id' => 19, 'tag_id' => 6],
            ['id' => 49, 'guide_id' => 20, 'tag_id' => 17],
            ['id' => 50, 'guide_id' => 20, 'tag_id' => 20],
            ['id' => 58, 'guide_id' => 22, 'tag_id' => 5],
            ['id' => 61, 'guide_id' => 22, 'tag_id' => 3],
            ['id' => 62, 'guide_id' => 24, 'tag_id' => 2],
            ['id' => 63, 'guide_id' => 24, 'tag_id' => 5],
            ['id' => 64, 'guide_id' => 25, 'tag_id' => 8],
            ['id' => 65, 'guide_id' => 25, 'tag_id' => 11],
            ['id' => 71, 'guide_id' => 27, 'tag_id' => 3],
            ['id' => 72, 'guide_id' => 27, 'tag_id' => 6],
            ['id' => 73, 'guide_id' => 27, 'tag_id' => 7],
            ['id' => 74, 'guide_id' => 27, 'tag_id' => 10],
            ['id' => 75, 'guide_id' => 27, 'tag_id' => 9],
        ));
    }
}