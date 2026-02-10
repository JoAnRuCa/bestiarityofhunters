<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('build_tags', function (Blueprint $table) {
            $table->id();

            $table->foreignId('build_id')
                  ->constrained('builds')
                  ->onDelete('cascade');

            $table->foreignId('tag_id')
                  ->constrained('tags')
                  ->onDelete('cascade');

            // Evita duplicados build_id + tag_id
            $table->unique(['build_id', 'tag_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('build_tags');
    }
};
