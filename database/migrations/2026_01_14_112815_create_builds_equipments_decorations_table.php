<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildsEquipmentsDecorationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('builds_equipments_decorations', function (Blueprint $table) {
        $table->id();

        // Foreign key hacia builds_equipments.id
        $table->unsignedBigInteger('build_equipment_id');

        // Campo normal
        $table->unsignedBigInteger('decoration_id');

        // Relación correcta
        $table->foreign('build_equipment_id')
              ->references('id')->on('builds_equipments')
              ->onDelete('restrict');

        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('builds_equipments_decorations');
    }

}
