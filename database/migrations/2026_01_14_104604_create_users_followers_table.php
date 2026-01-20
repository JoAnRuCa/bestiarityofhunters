<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_followers', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('follower_id');

            // Primary key compuesta
            $table->primary(['user_id', 'follower_id']);

            // Foreign keys con RESTRICT
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('restrict');

            $table->foreign('follower_id')
                ->references('id')->on('users')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_followers');
    }
}
