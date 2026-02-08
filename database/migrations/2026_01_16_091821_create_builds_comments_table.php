<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildsCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('builds_comments', function (Blueprint $table) {
        $table->id();

        // Texto del comentario
        $table->text('comentario');

        // Relación con users
        $table->unsignedBigInteger('user_id');

        // Relación con builds
        $table->unsignedBigInteger('build_id');

        // Comentario padre (0 = comentario sin padre)
        $table->unsignedBigInteger('padre')->nullable();

        // Foreign keys
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
        Schema::dropIfExists('builds_comments');
    }

}
