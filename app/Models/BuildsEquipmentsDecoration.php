<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildsEquipmentsDecoration extends Model
{
    use HasFactory;

    // FORZAR EL NOMBRE DE LA TABLA (Indispensable para evitar el error 1146)
    protected $table = 'builds_equipments_decorations';

    protected $fillable = ['build_equipment_id', 'decoration_id'];

    // Relación hacia arriba (a qué pieza de equipo pertenece)
    public function buildEquipment()
    {
        return $this->belongsTo(BuildsEquipment::class, 'build_equipment_id');
    }
}