<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildsEquipment extends Model
{
    protected $fillable = ['build_id', 'equipment_id', 'tipo'];

// Relación hacia arriba (a qué Build pertenece)
public function build()
{
    return $this->belongsTo(Build::class, 'build_id');
}

// Relación hacia abajo (qué decoraciones tiene esta pieza)
public function decorations()
{
    return $this->hasMany(BuildsEquipmentsDecoration::class, 'build_equipment_id');
}

use HasFactory;
}
