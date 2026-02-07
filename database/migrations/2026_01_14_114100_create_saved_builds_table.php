<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavedBuildsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('saved_builds', function (Blueprint $table) {
        $table->id();
        // Foreign keys
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('build_id');

        // No duplicados    
        $table->unique(['user_id', 'build_id']);

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
        Schema::dropIfExists('saved_builds');
    }

}
