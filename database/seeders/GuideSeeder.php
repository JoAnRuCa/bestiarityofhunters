<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuideSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('guides')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('guides')->insert(array (
            ['id' => 1,  'titulo' => 'Titulo',             'slug' => 'titulo',             'contenido' => 'Lorem ipsum dolor sit amet...', 'user_id' => 2],
            ['id' => 2,  'titulo' => 'sdasd',              'slug' => 'sdasd',              'contenido' => 'asdasdas',                      'user_id' => 2],
            ['id' => 3,  'titulo' => '3',                  'slug' => '3',                  'contenido' => 'asdasd',                        'user_id' => 2],
            ['id' => 4,  'titulo' => '5',                  'slug' => '5',                  'contenido' => 'sdasdsa',                       'user_id' => 2],
            ['id' => 5,  'titulo' => 'dfsdf',              'slug' => 'dfsdf',              'contenido' => 'sdfsdf',                        'user_id' => 2],
            ['id' => 6,  'titulo' => '6',                  'slug' => '6',                  'contenido' => 'sadasdasd',                     'user_id' => 2],
            ['id' => 7,  'titulo' => '89',                 'slug' => '89',                 'contenido' => 't7uyu',                         'user_id' => 2],
            ['id' => 8,  'titulo' => '90',                 'slug' => '90',                 'contenido' => 'sasd',                          'user_id' => 2],
            ['id' => 9,  'titulo' => '91',                 'slug' => '91',                 'contenido' => 'qwe',                           'user_id' => 2],
            ['id' => 10, 'titulo' => 'sad',                'slug' => 'sad',                'contenido' => 'asd',                           'user_id' => 2],
            ['id' => 11, 'titulo' => 'das',                'slug' => 'das',                'contenido' => 'asdasd',                        'user_id' => 2],
            ['id' => 12, 'titulo' => 'tyu',                'slug' => 'tyu',                'contenido' => 'sdasd',                         'user_id' => 2],
            ['id' => 13, 'titulo' => '54',                 'slug' => '54',                 'contenido' => 'weqwe',                         'user_id' => 2],
            ['id' => 14, 'titulo' => '43',                 'slug' => '43',                 'contenido' => 'eqweqw',                        'user_id' => 2],
            ['id' => 15, 'titulo' => 'Titulo guía',        'slug' => 'titulo-guia',        'contenido' => 'Lorem Ipsum',                   'user_id' => 4],
            ['id' => 16, 'titulo' => 'GuiaNombre',         'slug' => 'guianombre',         'contenido' => 'Descripcion',                   'user_id' => 5],
            ['id' => 17, 'titulo' => 'Rathian',            'slug' => 'rathian',            'contenido' => 'Contenido',                     'user_id' => 6],
            ['id' => 18, 'titulo' => 'NombreGuiaOriginal', 'slug' => 'nombreguiaoriginal', 'contenido' => 'Aasdsd',                        'user_id' => 7],
            ['id' => 19, 'titulo' => 'Insect',             'slug' => 'insect',             'contenido' => 'Glaive',                        'user_id' => 9],
            ['id' => 20, 'titulo' => 'TYUR',               'slug' => 'tyur',               'contenido' => 'Asd',                           'user_id' => 2],
            ['id' => 22, 'titulo' => 'RathalosQueen',      'slug' => 'rathalosqueen',      'contenido' => 'Dreadking',                     'user_id' => 12],
            ['id' => 24, 'titulo' => 'NombreGuiaSeguro',   'slug' => 'nombreguiaseguro',   'contenido' => 'Ejemplo',                       'user_id' => 2],
            ['id' => 25, 'titulo' => 'asdasd',             'slug' => 'asdasd',             'contenido' => 'asdasd',                        'user_id' => 3],
            ['id' => 27, 'titulo' => 'Adf',                'slug' => 'adf',                'contenido' => 'adsad',                         'user_id' => 2],
        ));
    }
}