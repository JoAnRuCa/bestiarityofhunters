<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('builds', function (Blueprint $table) {
            // Renombrar columna
            if (Schema::hasColumn('builds', 'nombre')) {
                $table->renameColumn('nombre', 'titulo');
            }
        });
    }

    public function down()
    {
        Schema::table('builds', function (Blueprint $table) {
            // Revertir cambio
            if (Schema::hasColumn('builds', 'titulo')) {
                $table->renameColumn('titulo', 'nombre');
            }
        });
    }
};
