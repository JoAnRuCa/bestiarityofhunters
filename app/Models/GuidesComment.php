<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuidesComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guide_id',
        'contenido'
    ];

    // Usuario que escribió el comentario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Guía a la que pertenece el comentario
    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }

    // Votos del comentario
    public function votos()
    {
        return $this->hasMany(GuidesCommentVote::class, 'comment_id');
    }

    // Score total del comentario
    public function score()
    {
        return $this->votos()->sum('tipo');
    }

    // Saber si un usuario ya votó este comentario
    public function votoDe($userId)
    {
        return $this->votos()->where('user_id', $userId)->first();
    }
}
