<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::create('guides', function (Blueprint $table) {
        $table->id();

        // Título de la guía
        $table->string('titulo');

        // Contenido largo de la guía
        $table->longText('contenido');

        // Usuario que creó la guía
        $table->unsignedBigInteger('user_id');

        // Relación con users
        $table->foreign('user_id')
              ->references('id')->on('users')
              ->onDelete('restrict');

        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('guides');
    }

}
