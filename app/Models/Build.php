<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Añadido para mayor seguridad en el borrado

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

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'build_tags');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function votos()
    {
        return $this->hasMany(BuildVote::class, 'build_id');
    }

    public function comments()
    {
        return $this->hasMany(BuildComment::class, 'build_id');
    }

    /**
     * RELACIÓN CORREGIDA: Especificamos 'builds_equipments' 
     * para evitar el error de "table not found"
     */
    public function equipments()
    {
        return $this->hasMany(BuildsEquipment::class, 'build_id', 'id');
    }

    // -----------------------------
    // SCORE TOTAL
    // -----------------------------
    public function score()
    {
        return $this->votos()->sum('tipo');
    }

    public function votoDe($userId)
    {
        return $this->votos()->where('user_id', $userId)->first();
    }

    // -----------------------------
    // EVENTOS DE MODELO (BOOT)
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

        /**
         * BORRADO EN CASCADA CORREGIDO
         */
        static::deleting(function ($build) {
            // Usamos una transacción para asegurar que no queden datos huérfanos
            DB::transaction(function () use ($build) {
                
                // 1. Obtenemos los IDs de los equipos antes de borrarlos
                // Esto usa la relación ya corregida arriba
                $equipmentIds = $build->equipments()->pluck('id');

                if ($equipmentIds->isNotEmpty()) {
                    // 2. Borramos decoraciones de esos equipos
                    DB::table('builds_equipments_decorations')
                        ->whereIn('build_equipment_id', $equipmentIds)
                        ->delete();

                    // 3. Borramos los equipos
                    $build->equipments()->delete();
                }

                // 4. Limpiamos el resto de relaciones
                $build->votos()->delete();
                $build->comments()->delete();
                $build->tags()->detach();
                
                // 5. Limpiar de guardados (favoritos)
                DB::table('saved_builds')->where('build_id', $build->id)->delete();
            });
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

    public function getRouteKeyName()
{
    return 'slug';
}
}