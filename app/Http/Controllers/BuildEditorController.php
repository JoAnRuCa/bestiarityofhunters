<?php

namespace App\Http\Controllers;

use App\Models\Build;
use App\Http\Requests\StoreBuildRequest; // Importamos tu nuevo Request
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BuildEditorController extends Controller
{
    public function index()
    {
        return view('seccion.buildEditor');
    }

    /**
     * Guarda la build usando el StoreBuildRequest para validación automática.
     */
    public function store(StoreBuildRequest $request)
    {
        try {
            // Los datos ya están validados aquí gracias a StoreBuildRequest
            $buildData = json_decode($request->input('build_data'), true);
            $decoData = json_decode($request->input('decorations_data'), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['success' => false, 'error' => 'Invalid JSON format'], 400);
            }

            $categoryMap = [
                'weapon1' => 1, 'weapon2' => 1,
                'head'    => 2, 'chest'   => 2, 'arms' => 2, 'waist' => 2, 'legs' => 2,
                'charm'   => 3
            ];

            return DB::transaction(function () use ($request, $buildData, $decoData, $categoryMap) {
                
                $build = Build::create([
                    'titulo'    => $request->name,
                    'playstyle' => $request->playstyle,
                    'user_id'   => Auth::id() ?? 1,
                ]);

                foreach ($buildData as $slot => $item) {
                    if (!$item || !isset($item['id'])) continue;

                    $tipoNumerico = $categoryMap[$slot] ?? 0;

                    $buildEquipmentId = DB::table('builds_equipments')->insertGetId([
                        'build_id'     => $build->id,
                        'equipment_id' => $item['id'],
                        'tipo'         => $tipoNumerico, 
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);

                    // Procesar decoraciones si existen para este slot
                    if (isset($decoData[$slot]) && is_array($decoData[$slot])) {
                        foreach ($decoData[$slot] as $deco) {
                            if ($deco && isset($deco['id'])) {
                                DB::table('builds_equipments_decorations')->insert([
                                    'build_equipment_id' => $buildEquipmentId,
                                    'decoration_id'      => $deco['id'],
                                    'created_at'         => now(),
                                    'updated_at'         => now(),
                                ]);
                            }
                        }
                    }
                }

                // Sincronizar etiquetas de armas
                if ($request->has('tags')) {
                    $build->tags()->sync($request->tags);
                }

                return response()->json([
                    'success' => true,
                    'message' => '¡Build forjada correctamente!',
                    'redirect_url' => url('/build-editor/' . $build->slug)
                ]);
            });

        } catch (\Exception $e) {
            Log::error("Error guardando build: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }

public function show($slug)
{
    $build = Build::with('tags')->where('slug', $slug)->firstOrFail();
    $equipments = DB::table('builds_equipments')->where('build_id', $build->id)->get();

    // Carga de datos desde archivos JSON
    $weapons = json_decode(Storage::get('data/weapons.json'), true) ?: [];
    $armors = json_decode(Storage::get('data/armors.json'), true) ?: [];
    $charms = $this->getNormalizedCharms();
    $allDecorations = json_decode(Storage::get('data/decorations.json'), true) ?: [];
    $skillsData = json_decode(Storage::get('data/skills.json'), true) ?: [];
    
    // Mapeo de niveles máximos para la vista
    $skillMaxLevels = [];
    foreach ($skillsData as $s) {
        if (isset($s['name']) && isset($s['ranks'])) {
            $skillMaxLevels[trim($s['name'])] = count($s['ranks']);
        }
    }

    $totalSkillsRaw = [];
    $weaponSkills = []; // Rastreador de habilidades prioritarias (Armas)

    foreach ($equipments as $eq) {
        $source = [];
        $isWeapon = ((int)$eq->tipo === 1); // Guardamos si es tipo Arma

        switch ((int)$eq->tipo) {
            case 1: $source = $weapons; break;
            case 2: $source = $armors; break;
            case 3: $source = $charms; break;
        }

        $itemData = collect($source)->firstWhere('id', $eq->equipment_id);
        
        if ($itemData) {
            $eq->real_name = $itemData['name'] ?? $itemData['weaponName'] ?? $itemData['charmName'] ?? 'Unknown Item';
            $eq->total_slots = $itemData['slots'] ?? [];

            // Extraer habilidades nativas del equipo
            if (isset($itemData['skill']['name'])) {
                $name = trim($itemData['skill']['name']);
                $totalSkillsRaw[$name] = ($totalSkillsRaw[$name] ?? 0) + ($itemData['level'] ?? 1);
                if ($isWeapon) $weaponSkills[$name] = true;
            } elseif (isset($itemData['skills'])) {
                foreach ($itemData['skills'] as $s) {
                    $name = trim($s['skill']['name'] ?? $s['name'] ?? '');
                    if ($name) {
                        $totalSkillsRaw[$name] = ($totalSkillsRaw[$name] ?? 0) + ($s['level'] ?? 1);
                        if ($isWeapon) $weaponSkills[$name] = true;
                    }
                }
            }
        }

        // Procesar Decoraciones guardadas en este slot
        $savedDecos = DB::table('builds_equipments_decorations')->where('build_equipment_id', $eq->id)->get();
        $eq->attached_decos = [];
        
        foreach ($savedDecos as $d) {
            $decoInfo = collect($allDecorations)->firstWhere('id', $d->decoration_id);
            if ($decoInfo) {
                $eq->attached_decos[] = [
                    'name' => $decoInfo['name'],
                    'level' => $decoInfo['slot'] ?? 1,
                    'is_empty' => false
                ];

                if (isset($decoInfo['skills'])) {
                    foreach ($decoInfo['skills'] as $ds) {
                        $dn = trim($ds['skill']['name'] ?? $ds['name'] ?? '');
                        if ($dn) {
                            $totalSkillsRaw[$dn] = ($totalSkillsRaw[$dn] ?? 0) + ($ds['level'] ?? 1);
                            // Si la decoración está en un arma, también priorizamos la habilidad
                            if ($isWeapon) $weaponSkills[$dn] = true;
                        }
                    }
                }
            }
        }

        // Rellenar slots vacíos para la vista
        if (isset($eq->total_slots) && is_array($eq->total_slots)) {
            $numEquipadas = count($eq->attached_decos);
            $numTotales = count($eq->total_slots);
            
            if ($numEquipadas < $numTotales) {
                for ($i = $numEquipadas; $i < $numTotales; $i++) {
                    $eq->attached_decos[] = [
                        'name' => null,
                        'level' => $eq->total_slots[$i],
                        'is_empty' => true
                    ];
                }
            }
        }
    }

    // ⭐ LÓGICA DE ORDENAMIENTO COMPLETA ⭐
    $totalSkills = collect($totalSkillsRaw)
        ->map(function($lvl, $name) use ($skillMaxLevels, $weaponSkills) {
            return [
                'name'      => $name,
                'lvl'       => $lvl,
                'max'       => $skillMaxLevels[$name] ?? 5,
                'is_weapon' => isset($weaponSkills[$name]) ? 1 : 0
            ];
        })
        ->sortBy([
            ['is_weapon', 'desc'], // 1º Habilidades de Arma
            ['lvl', 'desc'],       // 2º Nivel más alto (ej: 4/5 > 3/3)
            ['name', 'asc']        // 3º Orden alfabético
        ])
        ->pluck('lvl', 'name')
        ->toArray();

    return view('seccion.buildEditorShow', compact('build', 'equipments', 'totalSkills', 'skillMaxLevels', 'skillsData'));
}

    private function getNormalizedCharms() 
    {
        $charmsRaw = json_decode(Storage::get('data/charms.json'), true) ?: [];
        $normalized = [];
        foreach ($charmsRaw as $charm) {
            if (isset($charm['ranks'])) {
                foreach ($charm['ranks'] as $rank) { $normalized[] = $rank; }
            }
        }
        return $normalized;
    }
}