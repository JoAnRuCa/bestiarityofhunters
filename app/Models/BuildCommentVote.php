<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildCommentVote extends Model
{
    use HasFactory;

    // Nombre de la tabla en tu base de datos
    protected $table = 'builds_comments_votes'; 

    protected $fillable = [
        'user_id',
        'comment_id',
        'tipo'
    ];

    // Relación con el usuario que votó
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el comentario votado
    public function comentario()
    {
        return $this->belongsTo(BuildComment::class, 'comment_id');
    }
}
