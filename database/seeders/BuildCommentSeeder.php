<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildCommentSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('builds_comments')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('builds_comments')->insert(array (
            ['id' => 1,  'comentario' => 'asasasasas', 'user_id' => 1,  'build_id' => 1,  'padre' => NULL],
            ['id' => 2,  'comentario' => 'asasas',     'user_id' => 1,  'build_id' => 1,  'padre' => 1],
            ['id' => 3,  'comentario' => 'asdsdddd',   'user_id' => 2,  'build_id' => 11, 'padre' => NULL],
            ['id' => 4,  'comentario' => 'sd',         'user_id' => 2,  'build_id' => 11, 'padre' => 3],
            ['id' => 5,  'comentario' => 'Hola',       'user_id' => 4,  'build_id' => 15, 'padre' => NULL],
            ['id' => 6,  'comentario' => 'B',          'user_id' => 4,  'build_id' => 15, 'padre' => 5],
            ['id' => 7,  'comentario' => 'A',          'user_id' => 6,  'build_id' => 16, 'padre' => NULL],
            ['id' => 8,  'comentario' => 'B',          'user_id' => 6,  'build_id' => 16, 'padre' => 7],
            ['id' => 9,  'comentario' => 'C',          'user_id' => 6,  'build_id' => 16, 'padre' => NULL],
            ['id' => 10, 'comentario' => 'D',          'user_id' => 8,  'build_id' => 16, 'padre' => 9],
            ['id' => 11, 'comentario' => 'R',          'user_id' => 8,  'build_id' => 16, 'padre' => NULL],
            ['id' => 12, 'comentario' => 'T',          'user_id' => 9,  'build_id' => 16, 'padre' => NULL],
            ['id' => 13, 'comentario' => 'u',          'user_id' => 10, 'build_id' => 16, 'padre' => 11],
            ['id' => 14, 'comentario' => 'P',          'user_id' => 10, 'build_id' => 16, 'padre' => NULL],
            ['id' => 15, 'comentario' => 'asddNo',     'user_id' => 2,  'build_id' => 22, 'padre' => NULL],
        ));
    }
}