<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildComment extends Model
{
    use HasFactory;

    // Nombre explícito de la tabla
    protected $table = 'builds_comments';

    protected $fillable = [
        'user_id',
        'build_id',
        'comentario', // Igual que en GuidesComment
        'padre'       // Para respuestas
    ];

    // --- RELACIONES DE JERARQUÍA ---

    /**
     * Respuestas a este comentario.
     */
    public function respuestas()
    {
        return $this->hasMany(BuildComment::class, 'padre')->orderBy('created_at', 'asc');
    }

    /**
     * Comentario padre si este es una respuesta.
     */
    public function comentarioPadre()
    {
        return $this->belongsTo(BuildComment::class, 'padre');
    }

    // --- RELACIONES DE ENTIDAD ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function build()
    {
        return $this->belongsTo(Build::class);
    }

    // --- SISTEMA DE VOTOS ---

    public function votos()
    {
        return $this->hasMany(BuildCommentVote::class, 'comment_id');
    }

    public function score()
    {
        return (int) $this->votos()->sum('tipo');
    }

    public function votoDe($userId)
    {
        return $this->votos()->where('user_id', $userId)->first();
    }
}
