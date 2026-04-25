<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuideCommentSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('guides_comments')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('guides_comments')->insert(array (
            ['id' => 1, 'comentario' => 'Aasasasdcasdfsdd', 'user_id' => 2, 'guide_id' => 1, 'padre' => NULL],
            ['id' => 2, 'comentario' => 'rtrthynbyb',        'user_id' => 2, 'guide_id' => 1, 'padre' => NULL],
            ['id' => 3, 'comentario' => '9097687686867',     'user_id' => 2, 'guide_id' => 1, 'padre' => NULL],
        ));
    }
}