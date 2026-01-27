<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class BuildEditorController extends Controller
{
    public function index()
    {
        // Cargar datos crudos
        $weapons = json_decode(Storage::get('data/weapons.json'));
        $armors = json_decode(Storage::get('data/armors.json'));
        $charmsRaw = json_decode(Storage::get('data/charms.json'));
        $decorations = json_decode(Storage::get('data/decorations.json'));
        $skills = json_decode(Storage::get('data/skills.json'));

        // Normalizar charms: convertir cada rango en un charm independiente
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

        return view('seccion.buildEditor', [
            'weapons' => $weapons,
            'armors' => $armors,
            'charms' => $normalizedCharms,
            'decorations' => $decorations,
            'skills' => $skills,
        ]);
    }
}
