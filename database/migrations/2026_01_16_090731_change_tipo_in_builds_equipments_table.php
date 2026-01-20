<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTipoInBuildsEquipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() { 
        Schema::table('builds_equipments', function (Blueprint $table) { 
            $table->integer('tipo')->change(); 
        }); 
    } 

    public function down() { 
        Schema::table('builds_equipments', function (Blueprint $table) { 
            $table->boolean('tipo')->change(); 
        }); 
    }
}
