<?php

namespace App\Support;

use Illuminate\Support\Collection;

class SearchHelper
{
    /**
     * Aplica un filtro de búsqueda inteligente a una colección.
     * Busca por nombre, tipo (kind), habilidades y nivel de slot.
     */
    public static function apply(Collection $items, string $query): Collection
    {
        // 1. Guardamos la query original para comparaciones exactas (como el número de slot)
        $originalQuery = trim($query);
        
        // 2. Normalizamos la búsqueda: minúsculas y eliminamos espacios y guiones
        // Esto hace que "insect g" se convierta en "insectg"
        $q = str_replace([' ', '-'], '', strtolower($originalQuery));

        return $items->filter(function ($item) use ($q, $originalQuery) {
            
            // --- Búsqueda por Nombre ---
            if (isset($item['name'])) {
                $cleanName = str_replace([' ', '-'], '', strtolower($item['name']));
                if (str_contains($cleanName, $q)) return true;
            }

            // --- Búsqueda por Tipo/Categoría (Kind) ---
            // Ejemplo: "head", "insect-glaive", "arms"
            if (isset($item['kind'])) {
                $cleanKind = str_replace([' ', '-'], '', strtolower($item['kind']));
                if (str_contains($cleanKind, $q)) return true;
            }

            // --- Búsqueda por Habilidades (Skills) ---
            // Recorre el array de habilidades buscando coincidencias en el nombre
            if (isset($item['skills']) && is_array($item['skills'])) {
                foreach ($item['skills'] as $skillData) {
                    // Soporta estructuras: $s['skill']['name'] o $s['name']
                    $skillName = $skillData['skill']['name'] ?? $skillData['name'] ?? '';
                    $cleanSkill = str_replace([' ', '-'], '', strtolower($skillName));
                    
                    if (str_contains($cleanSkill, $q)) return true;
                }
            }

            // --- Búsqueda por Slot (Específico para Joyas/Armaduras) ---
            // Si el usuario busca "4", comparamos con el nivel del hueco
            if (isset($item['slot']) && (string)$item['slot'] === $originalQuery) {
                return true;
            }

            return false;
        });
    }
}