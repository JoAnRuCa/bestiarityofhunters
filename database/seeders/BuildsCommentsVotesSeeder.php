<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildsCommentsVotesSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('builds_comments_votes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('builds_comments_votes')->insert(array (
            ['id' => 1, 'user_id' => 8,  'comment_id' => 10, 'tipo' => 1],
            ['id' => 2, 'user_id' => 9,  'comment_id' => 10, 'tipo' => -1],
            ['id' => 3, 'user_id' => 9,  'comment_id' => 9,  'tipo' => 1],
            ['id' => 4, 'user_id' => 10, 'comment_id' => 13, 'tipo' => 1],
            ['id' => 5, 'user_id' => 11, 'comment_id' => 14, 'tipo' => 1],
            ['id' => 7, 'user_id' => 11, 'comment_id' => 15, 'tipo' => 1],
        ));
    }
}