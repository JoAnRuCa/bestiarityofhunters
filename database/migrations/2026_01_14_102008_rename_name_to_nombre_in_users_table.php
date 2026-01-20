<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameNameToNombreInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() 
    { 
        Schema::table('users', function (Blueprint $table) { 
            $table->renameColumn('name', 'nombre'); 
        }); 
    }
    public function down() 
    { 
        Schema::table('users', function (Blueprint $table) { 
            $table->renameColumn('nombre', 'name'); 
        }); 
    }
}
