<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('builds')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('builds')->insert(array (
            ['id' => 1,  'titulo' => 'a<xas',         'slug' => 'axas',            'playstyle' => NULL,     'user_id' => 2],
            ['id' => 2,  'titulo' => 'asasas',        'slug' => 'asasas',          'playstyle' => NULL,     'user_id' => 2],
            ['id' => 3,  'titulo' => 'xcsdadsdsd',    'slug' => 'xcsdadsdsd',      'playstyle' => NULL,     'user_id' => 2],
            ['id' => 4,  'titulo' => 'asdasd',        'slug' => 'asdasd-4',        'playstyle' => NULL,     'user_id' => 2],
            ['id' => 5,  'titulo' => '67',            'slug' => '67',              'playstyle' => NULL,     'user_id' => 2],
            ['id' => 6,  'titulo' => 'erererer',      'slug' => 'erererer-6',      'playstyle' => NULL,     'user_id' => 2],
            ['id' => 7,  'titulo' => 'name2567',      'slug' => 'name2567',        'playstyle' => NULL,     'user_id' => 3],
            ['id' => 8,  'titulo' => 'sdfdfgdfg',     'slug' => 'sdfdfgdfg-8',     'playstyle' => NULL,     'user_id' => 3],
            ['id' => 10, 'titulo' => 'tyu',           'slug' => 'tyu',             'playstyle' => NULL,     'user_id' => 2],
            ['id' => 11, 'titulo' => 'Nombre3',       'slug' => 'nombre3-11',      'playstyle' => NULL,     'user_id' => 3],
            ['id' => 13, 'titulo' => 'Admin2',        'slug' => 'admin2-13',       'playstyle' => 'sdfsdf', 'user_id' => 2],
            ['id' => 15, 'titulo' => 'Nombre',        'slug' => 'nombre',          'playstyle' => NULL,     'user_id' => 5],
            ['id' => 16, 'titulo' => 'BuildOriginal', 'slug' => 'buildoriginal-16', 'playstyle' => NULL,    'user_id' => 6],
            ['id' => 18, 'titulo' => 'Rompon',        'slug' => 'rompon',          'playstyle' => NULL,     'user_id' => 8],
            ['id' => 20, 'titulo' => 'Nombre',        'slug' => 'nombre-20',       'playstyle' => NULL,     'user_id' => 2],
            ['id' => 22, 'titulo' => 'Una guia a',    'slug' => 'una-guia-a-22',   'playstyle' => NULL,     'user_id' => 2],
            ['id' => 23, 'titulo' => 'RathalosDreak', 'slug' => 'rathalosdreak-23','playstyle' => NULL,     'user_id' => 12],
        ));
    }
}