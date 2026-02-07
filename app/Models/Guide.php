<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'contenido',
        'user_id',
    ];

    // Tags
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'guide_tags');
    }

    // Autor
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación correcta con el modelo GuidesVote
    public function votos()
    {
        return $this->hasMany(GuidesVote::class, 'guide_id');
    }

    // Score total (suma de tipo: 1 y -1)
    public function score()
    {
        return $this->votos()->sum('tipo');
    }

    // Saber si un usuario ya votó esta guía
    public function votoDe($userId)
    {
        return $this->votos()->where('user_id', $userId)->first();
    }
}
