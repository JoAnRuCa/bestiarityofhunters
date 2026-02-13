<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedBuild extends Model
{
    use HasFactory;

    // Esta es la línea que falta para solucionar el error:
    protected $fillable = [
        'user_id',
        'build_id',
    ];

    /**
     * Relación con la Build (Opcional, pero recomendada)
     */
    public function build()
    {
        return $this->belongsTo(Build::class);
    }
}