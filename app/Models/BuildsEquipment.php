<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildsEquipment extends Model
{
    use HasFactory;

    // ESTA LÍNEA ES LA QUE SOLUCIONA EL ERROR 1146
    // Fuerza a Laravel a usar el nombre exacto de tu tabla con "s"
    protected $table = 'builds_equipments';

    protected $fillable = ['build_id', 'equipment_id', 'tipo'];

    // Relación hacia arriba (a qué Build pertenece)
    public function build()
    {
        return $this->belongsTo(Build::class, 'build_id');
    }

    // Relación hacia abajo (qué decoraciones tiene esta pieza)
    public function decorations()
    {
        // Asegúrate de que el modelo BuildsEquipmentsDecoration también 
        // tenga definida su tabla 'builds_equipments_decorations'
        return $this->hasMany(BuildsEquipmentsDecoration::class, 'build_equipment_id');
    }
}