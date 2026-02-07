<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavedGuidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('saved_guides', function (Blueprint $table) {
        $table->id();
        // Foreign keys
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('guide_id');

        // No duplicados
        $table->unique(['user_id', 'guide_id']);

        // Relaciones
        $table->foreign('user_id')
              ->references('id')->on('users')
              ->onDelete('restrict');

        $table->foreign('guide_id')
              ->references('id')->on('guides')
              ->onDelete('restrict');

        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('saved_guides');
    }

}
