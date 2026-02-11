<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildsEquipmentsDecoration extends Model
{
    // App\Models\BuildsEquipmentsDecoration.php

protected $fillable = ['build_equipment_id', 'decoration_id'];

// Relación hacia arriba (a qué pieza de equipo pertenece)
public function buildEquipment()
{
    return $this->belongsTo(BuildsEquipment::class, 'build_equipment_id');
}

use HasFactory;
}
