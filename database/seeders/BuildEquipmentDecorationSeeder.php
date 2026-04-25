<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildEquipmentDecorationSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('builds_equipments_decorations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('builds_equipments_decorations')->insert(array (
            ['id' => 1,  'build_equipment_id' => 11,  'decoration_id' => 19],
            ['id' => 2,  'build_equipment_id' => 13,  'decoration_id' => 11],
            ['id' => 3,  'build_equipment_id' => 13,  'decoration_id' => 19],
            ['id' => 4,  'build_equipment_id' => 44,  'decoration_id' => 52],
            ['id' => 5,  'build_equipment_id' => 44,  'decoration_id' => 23],
            ['id' => 6,  'build_equipment_id' => 45,  'decoration_id' => 39],
            ['id' => 13, 'build_equipment_id' => 142, 'decoration_id' => 26],
            ['id' => 15, 'build_equipment_id' => 155, 'decoration_id' => 15],
            ['id' => 16, 'build_equipment_id' => 159, 'decoration_id' => 20],
            ['id' => 17, 'build_equipment_id' => 159, 'decoration_id' => 47],
            ['id' => 22, 'build_equipment_id' => 177, 'decoration_id' => 14],
            ['id' => 23, 'build_equipment_id' => 178, 'decoration_id' => 47],
            ['id' => 28, 'build_equipment_id' => 200, 'decoration_id' => 7],
            ['id' => 29, 'build_equipment_id' => 202, 'decoration_id' => 13],
            ['id' => 30, 'build_equipment_id' => 202, 'decoration_id' => 20],
            ['id' => 31, 'build_equipment_id' => 204, 'decoration_id' => 47],
            ['id' => 37, 'build_equipment_id' => 225, 'decoration_id' => 20],
            ['id' => 49, 'build_equipment_id' => 301, 'decoration_id' => 13],
            ['id' => 57, 'build_equipment_id' => 331, 'decoration_id' => 7],
            ['id' => 58, 'build_equipment_id' => 335, 'decoration_id' => 47],
        ));
    }
}