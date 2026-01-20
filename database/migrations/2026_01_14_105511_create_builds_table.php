<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('builds', function (Blueprint $table) {
            $table->id(); // unsignedBigInteger + primary key
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->text('playstyle')->nullable();

            // Foreign key hacia users.id
            $table->unsignedBigInteger('user_id');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('restrict');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('builds');
    }

}
