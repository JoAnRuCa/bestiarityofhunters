<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuidesComment extends Model
{
    use HasFactory;

    // Forzamos el nombre de la tabla por seguridad
    protected $table = 'guides_comments';

    protected $fillable = [
        'user_id',
        'guide_id',
        'comentario', // Cambiado de 'contenido' a 'comentario' según tu tabla
        'padre'       // Añadido para soportar respuestas
    ];

    // --- RELACIONES DE JERARQUÍA ---

    /**
     * Obtener las respuestas de este comentario.
     */
    public function respuestas()
    {
        return $this->hasMany(GuidesComment::class, 'padre')->orderBy('created_at', 'asc');
    }

    /**
     * Obtener el comentario original si este es una respuesta.
     */
    public function comentarioPadre()
    {
        return $this->belongsTo(GuidesComment::class, 'padre');
    }

    // --- RELACIONES DE ENTIDAD ---

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }

    // --- SISTEMA DE VOTOS (UNIVERSAL) ---

    public function votos()
    {
        return $this->hasMany(GuidesCommentVote::class, 'comment_id');
    }

    public function score()
    {
        // Forzamos a int para evitar problemas en PHP 7.4 si el sum es null
        return (int) $this->votos()->sum('tipo');
    }

    public function votoDe($userId)
    {
        return $this->votos()->where('user_id', $userId)->first();
    }
}