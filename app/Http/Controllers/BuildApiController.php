<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BuildApiController extends Controller
{
    /**
     * Carga todos los datos del editor (armas, armaduras, charms normalizados, etc.)
     */
    public function getBuildData()
    {
        $weapons = json_decode(Storage::get('data/weapons.json'));
        $armors = json_decode(Storage::get('data/armors.json'));
        $charmsRaw = json_decode(Storage::get('data/charms.json'));
        $decorations = json_decode(Storage::get('data/decorations.json'));
        $skills = json_decode(Storage::get('data/skills.json'));

        // Normalizar charms (cada rango = charm independiente)
        $normalizedCharms = [];

        foreach ($charmsRaw as $charm) {
            if (!isset($charm->ranks))
                continue;

            foreach ($charm->ranks as $rank) {
                $normalizedCharms[] = (object) [
                    'id' => $rank->id,
                    'name' => $rank->name,
                    'level' => $rank->level,
                    'rarity' => $rank->rarity,
                    'skills' => $rank->skills,
                ];
            }
        }

        return response()->json([
            'weapons' => $weapons,
            'armors' => $armors,
            'charms' => $normalizedCharms,
            'decorations' => $decorations,
            'skills' => $skills,
        ]);
    }

    /**
     * Devuelve los ítems según el slot seleccionado
     */
    public function getItemsBySlot($slot)
    {
        $weapons = json_decode(Storage::get('data/weapons.json'));
        $armors = json_decode(Storage::get('data/armors.json'));
        $charmsRaw = json_decode(Storage::get('data/charms.json'));

        // Normalizar charms
        $normalizedCharms = [];
        foreach ($charmsRaw as $charm) {
            if (!isset($charm->ranks))
                continue;

            foreach ($charm->ranks as $rank) {
                $normalizedCharms[] = (object) [
                    'id' => $rank->id,
                    'name' => $rank->name,
                    'level' => $rank->level,
                    'rarity' => $rank->rarity,
                    'skills' => $rank->skills,
                ];
            }
        }

        // PHP 7.4 compatible
        if ($slot === 'weapon1' || $slot === 'weapon2') {
            return $weapons;
        }

        if ($slot === 'charm') {
            return $normalizedCharms;
        }

        // Armors por tipo
        return array_values(array_filter($armors, function ($a) use ($slot) {
            return $a->kind === $slot;
        }));
    }

    /**
     * Guarda el build del usuario
     */
    public function saveBuild(Request $request)
    {
        Storage::put(
            'builds/user-build.json',
            json_encode($request->all(), JSON_PRETTY_PRINT)
        );

        return ['status' => 'ok'];
    }
}
