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
        $table->string('apellidos')->after('nombre'); 
        $table->string('avatar')->nullable()->after('apellidos'); 
        $table->date('fecha_nacimiento')->nullable()->after('avatar'); 
        $table->string('role')->default('user')->after('fecha_nacimiento'); });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('users', function (Blueprint $table) {
             $table->dropColumn([ 'apellidos', 'avatar', 'fecha_nacimiento', 'role']);
         });
    }
}
