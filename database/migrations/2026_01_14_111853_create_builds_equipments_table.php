<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildsEquipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('builds_equipments', function (Blueprint $table) {
        $table->id();

        // Foreign key hacia builds.id
        $table->unsignedBigInteger('build_id');

        $table->unsignedBigInteger('equipment_id');
        $table->boolean('tipo');

        // Relación con builds
        $table->foreign('build_id')
              ->references('id')->on('builds')
              ->onDelete('restrict'); 

        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('builds_equipments');
    }

}
