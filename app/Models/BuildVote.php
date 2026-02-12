<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildVote extends Model // Asegúrate de que aquí no diga BuildsVote
{
    use HasFactory;

    protected $table = 'builds_votes'; // Tu tabla en la DB

    protected $fillable = [
        'user_id',
        'build_id',
        'tipo'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function build()
    {
        return $this->belongsTo(Build::class);
    }
}