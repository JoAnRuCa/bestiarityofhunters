<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugToGuidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::table('guides', function (Blueprint $table) {
        $table->string('slug')->nullable()->after('titulo');
    });
}

public function down()
{
    Schema::table('guides', function (Blueprint $table) {
        $table->dropColumn('slug');
    });
}

}
