<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuidesCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('guides_comments', function (Blueprint $table) {
        $table->id();

        // Texto del comentario
        $table->text('comentario');

        // Relación con users
        $table->unsignedBigInteger('user_id');

        // Relación con guides
        $table->unsignedBigInteger('guide_id');

        // Comentario padre (0 = comentario sin padre)
        $table->unsignedBigInteger('padre')->default(0);

        // Foreign keys
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
        Schema::dropIfExists('guides_comments');
    }

}
