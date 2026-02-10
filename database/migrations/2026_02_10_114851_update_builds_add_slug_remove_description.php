<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('builds', function (Blueprint $table) {
            // Eliminar columna descripcion
            if (Schema::hasColumn('builds', 'descripcion')) {
                $table->dropColumn('descripcion');
            }

            // Agregar slug único después del nombre
            $table->string('slug')->unique()->after('nombre');
        });
    }

    public function down()
    {
        Schema::table('builds', function (Blueprint $table) {
            // Restaurar descripcion
            $table->text('descripcion')->nullable();

            // Quitar slug
            $table->dropColumn('slug');
        });
    }
};
