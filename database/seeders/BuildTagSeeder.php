<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildTagSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('build_tags')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('build_tags')->insert(array (
            ['id' => 1, 'build_id' => 1, 'tag_id' => 12],
            ['id' => 2, 'build_id' => 1, 'tag_id' => 16],
            ['id' => 3, 'build_id' => 1, 'tag_id' => 19],
            ['id' => 4, 'build_id' => 2, 'tag_id' => 12],
            ['id' => 5, 'build_id' => 2, 'tag_id' => 15],
            ['id' => 6, 'build_id' => 2, 'tag_id' => 16],
            ['id' => 7, 'build_id' => 3, 'tag_id' => 12],
            ['id' => 8, 'build_id' => 3, 'tag_id' => 16],
            ['id' => 9, 'build_id' => 3, 'tag_id' => 19],
            ['id' => 10, 'build_id' => 4, 'tag_id' => 12],
            ['id' => 11, 'build_id' => 4, 'tag_id' => 16],
            ['id' => 12, 'build_id' => 4, 'tag_id' => 19],
            ['id' => 13, 'build_id' => 5, 'tag_id' => 12],
            ['id' => 14, 'build_id' => 5, 'tag_id' => 15],
            ['id' => 15, 'build_id' => 6, 'tag_id' => 12],
            ['id' => 16, 'build_id' => 6, 'tag_id' => 15],
            ['id' => 17, 'build_id' => 6, 'tag_id' => 18],
            ['id' => 18, 'build_id' => 7, 'tag_id' => 12],
            ['id' => 19, 'build_id' => 7, 'tag_id' => 15],
            ['id' => 20, 'build_id' => 8, 'tag_id' => 12],
            ['id' => 25, 'build_id' => 10, 'tag_id' => 12],
            ['id' => 26, 'build_id' => 10, 'tag_id' => 15],
            ['id' => 27, 'build_id' => 10, 'tag_id' => 16],
            ['id' => 28, 'build_id' => 11, 'tag_id' => 12],
            ['id' => 29, 'build_id' => 11, 'tag_id' => 15],
            ['id' => 30, 'build_id' => 11, 'tag_id' => 16],
            ['id' => 94, 'build_id' => 11, 'tag_id' => 17],
            ['id' => 34, 'build_id' => 13, 'tag_id' => 12],
            ['id' => 35, 'build_id' => 13, 'tag_id' => 15],
            ['id' => 37, 'build_id' => 13, 'tag_id' => 18],
            ['id' => 42, 'build_id' => 15, 'tag_id' => 9],
            ['id' => 43, 'build_id' => 15, 'tag_id' => 12],
            ['id' => 44, 'build_id' => 15, 'tag_id' => 15],
            ['id' => 45, 'build_id' => 15, 'tag_id' => 16],
            ['id' => 46, 'build_id' => 16, 'tag_id' => 12],
            ['id' => 47, 'build_id' => 16, 'tag_id' => 15],
            ['id' => 48, 'build_id' => 16, 'tag_id' => 16],
            ['id' => 50, 'build_id' => 16, 'tag_id' => 17],
            ['id' => 49, 'build_id' => 16, 'tag_id' => 20],
            ['id' => 55, 'build_id' => 18, 'tag_id' => 5],
            ['id' => 56, 'build_id' => 18, 'tag_id' => 11],
            ['id' => 57, 'build_id' => 18, 'tag_id' => 15],
            ['id' => 58, 'build_id' => 18, 'tag_id' => 16],
            ['id' => 59, 'build_id' => 18, 'tag_id' => 19],
            ['id' => 66, 'build_id' => 20, 'tag_id' => 12],
            ['id' => 67, 'build_id' => 20, 'tag_id' => 15],
            ['id' => 68, 'build_id' => 20, 'tag_id' => 16],
            ['id' => 69, 'build_id' => 20, 'tag_id' => 19],
            ['id' => 77, 'build_id' => 22, 'tag_id' => 12],
            ['id' => 78, 'build_id' => 22, 'tag_id' => 17],
            ['id' => 79, 'build_id' => 22, 'tag_id' => 20],
            ['id' => 80, 'build_id' => 23, 'tag_id' => 8],
            ['id' => 81, 'build_id' => 23, 'tag_id' => 12],
            ['id' => 82, 'build_id' => 23, 'tag_id' => 15],
            ['id' => 83, 'build_id' => 23, 'tag_id' => 16],
            ['id' => 92, 'build_id' => 23, 'tag_id' => 17],
            ['id' => 84, 'build_id' => 23, 'tag_id' => 18],
            ['id' => 101, 'build_id' => 23, 'tag_id' => 20],
        ));
    }
}