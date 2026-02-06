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

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'guide_tags');
    }
}
