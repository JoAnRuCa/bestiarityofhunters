<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Build extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'titulo',
        'playstyle',
        'user_id',
    ];

    // -----------------------------
    // RELACIONES
    // -----------------------------

    // Tags (muchos a muchos)
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'build_tags');
    }

    // Autor
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Votos (igual que en Guide)
    public function votos()
    {
        return $this->hasMany(BuildVote::class, 'build_id');
    }

    // Comentarios (igual que en Guide)
    public function comments()
    {
        return $this->hasMany(BuildComment::class, 'build_id');
    }

    public function equipments()
    {
        return $this->hasMany(BuildsEquipment::class, 'build_id');
    }

    // -----------------------------
    // SCORE TOTAL
    // -----------------------------
    public function score()
    {
        return $this->votos()->sum('tipo'); // tipo = 1 o -1
    }

    // Saber si un usuario ya votó esta build
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

        static::creating(function ($build) {
            $build->slug = static::generateUniqueSlug($build->titulo);
        });

        static::updating(function ($build) {
            if ($build->isDirty('titulo')) {
                $build->slug = static::generateUniqueSlug($build->titulo, $build->id);
            }
        });

        static::deleting(function ($build) {
        // Al borrar la build, recorremos sus equipos
        foreach ($build->equipments as $equipment) {
            // 1. Borramos las decoraciones de ese equipo
            $equipment->decorations()->delete();
            // 2. Borramos el equipo en sí
            $equipment->delete();
        }
        
        // También limpiamos votos y comentarios si existen
        $build->votos()->delete();
        $build->comments()->delete();
        $build->tags()->detach();
    });
    }

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
