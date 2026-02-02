<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('users', function (Blueprint $table) { // nombre YA existe, no lo añadimos  
        $table->string('avatar')->nullable()->after('nombre');  
        $table->string('role')->default('user')->after('avatar'); });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('users', function (Blueprint $table) {
             $table->dropColumn([ 'avatar', 'role']);
         });
    }
}
