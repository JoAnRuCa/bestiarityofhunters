<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('builds_comments_votes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('comment_id')
                  ->constrained('builds_comments')
                  ->onDelete('cascade');

            $table->tinyInteger('tipo'); 

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('builds_comments_votes');
    }
};
