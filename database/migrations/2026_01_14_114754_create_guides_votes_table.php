<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuidesVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::create('guides_votes', function (Blueprint $table) {
        // Foreign keys
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('guide_id');

        // Campo tipo (solo 0 o 1)
        $table->boolean('tipo');

        // Primary key compuesta
        $table->primary(['user_id', 'guide_id']);

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
        Schema::dropIfExists('guides_votes');
    }

}
