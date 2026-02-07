<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildsVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('builds_votes', function (Blueprint $table) {
        // Foreign keys
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('build_id');

        // Campo tipo (solo 0 o 1)
        $table->tinyInteger('tipo');

        // Primary key compuesta
        $table->primary(['user_id', 'build_id']);

        // Relaciones
        $table->foreign('user_id')
              ->references('id')->on('users')
              ->onDelete('restrict');

        $table->foreign('build_id')
              ->references('id')->on('builds')
              ->onDelete('restrict');

        $table->timestamps();
    });
}

public function down()
    {
        Schema::dropIfExists('builds_votes');
    }

}
