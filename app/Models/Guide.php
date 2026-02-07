<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Guide extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'contenido',
        'user_id',
        'slug',
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

    // -----------------------------
    // SLUGS ÚNICOS AUTOMÁTICOS
    // -----------------------------
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($guide) {
            $guide->slug = static::generateUniqueSlug($guide->titulo);
        });

        static::updating(function ($guide) {
            // Solo regenerar si el título cambió
            if ($guide->isDirty('titulo')) {
                $guide->slug = static::generateUniqueSlug($guide->titulo, $guide->id);
            }
        });
    }

    // Generador de slugs únicos
    protected static function generateUniqueSlug($titulo, $ignoreId = null)
    {
        $baseSlug = Str::slug($titulo);
        $slug = $baseSlug;
        $counter = 2;

        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
