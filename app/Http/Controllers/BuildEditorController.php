<?php

namespace App\Http\Controllers;

use App\Models\Build;
use App\Http\Requests\StoreBuildRequest;
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

    public function store(StoreBuildRequest $request)
    {
        try {
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

    /**
     * Muestra la build terminada.
     * Restringido únicamente al creador de la misma.
     */
    public function show($slug)
    {
        // 1. Obtener la build o fallar si no existe
        $build = Build::with('tags')->where('slug', $slug)->firstOrFail();

        // 2. SEGURIDAD: Solo el autor puede entrar. 
        // Si no hay usuario logueado o el ID no coincide, lanzamos un 403.
        if (!Auth::check() || Auth::id() !== $build->user_id) {
            abort(403, 'Unauthorized access. This build belongs to another hunter.');
        }

        // 3. Cargar equipamiento asociado
        $equipments = DB::table('builds_equipments')->where('build_id', $build->id)->get();

        // 4. Cargar datos maestros de los JSON
        $weapons = json_decode(Storage::get('data/weapons.json'), true) ?: [];
        $armors = json_decode(Storage::get('data/armors.json'), true) ?: [];
        $charms = $this->getNormalizedCharms();
        $allDecorations = json_decode(Storage::get('data/decorations.json'), true) ?: [];
        $skillsData = json_decode(Storage::get('data/skills.json'), true) ?: [];
        
        // 5. Mapear niveles máximos de habilidades
        $skillMaxLevels = [];
        foreach ($skillsData as $s) {
            if (isset($s['name']) && isset($s['ranks'])) {
                $skillMaxLevels[trim($s['name'])] = count($s['ranks']);
            }
        }

        $totalSkillsRaw = [];
        $weaponSkills = []; 
        $tipoLabels = [1 => 'Weapon', 2 => 'Armor Piece', 3 => 'Charm'];

        // 6. Procesar cada pieza de equipo guardada
        foreach ($equipments as $eq) {
            $source = [];
            $isWeapon = ((int)$eq->tipo === 1);
            $eq->tipo_label = $tipoLabels[(int)$eq->tipo] ?? 'Equipment';

            switch ((int)$eq->tipo) {
                case 1: $source = $weapons; break;
                case 2: $source = $armors; break;
                case 3: $source = $charms; break;
            }

            $itemData = collect($source)->firstWhere('id', $eq->equipment_id);
            
            if ($itemData) {
                $eq->real_name = $itemData['name'] ?? $itemData['weaponName'] ?? $itemData['charmName'] ?? 'Unknown Item';
                $eq->total_slots = $itemData['slots'] ?? [];

                // Extraer habilidades base del equipo
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

            // 7. Procesar decoraciones (Joyas) de esta pieza
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
                                if ($isWeapon) $weaponSkills[$dn] = true;
                            }
                        }
                    }
                }
            }

            // Rellenar huecos vacíos visualmente
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

        // 8. Cálculo final de niveles y descripciones de habilidades
        $totalSkills = collect($totalSkillsRaw)
            ->map(function($lvl, $name) use ($skillMaxLevels, $weaponSkills, $skillsData) {
                $nameClean = trim($name);
                $max = $skillMaxLevels[$nameClean] ?? 5;
                $currentLvl = (int)min($lvl, $max);

                $skillInfo = collect($skillsData)->first(function($item) use ($nameClean) {
                    return trim($item['name'] ?? '') === $nameClean;
                });

                $desc = "Description not found.";
                if ($skillInfo && isset($skillInfo['ranks'][$currentLvl - 1])) {
                    $rank = $skillInfo['ranks'][$currentLvl - 1];
                    $desc = $rank['description'] ?? $rank['desc'] ?? $desc;
                }

                return [
                    'name'      => $nameClean,
                    'lvl'       => $currentLvl,
                    'max'       => $max,
                    'percent'   => ($max > 0) ? ($currentLvl / $max) * 100 : 0,
                    'desc'      => $desc,
                    'is_weapon' => isset($weaponSkills[$nameClean]) ? 1 : 0
                ];
            })
            ->sort(function ($a, $b) {
                if ($a['is_weapon'] !== $b['is_weapon']) {
                    return $b['is_weapon'] <=> $a['is_weapon'];
                }
                if ($a['lvl'] !== $b['lvl']) {
                    return $b['lvl'] <=> $a['lvl'];
                }
                return $a['name'] <=> $b['name'];
            })
            ->values()
            ->toArray();

        return view('seccion.buildEditorShow', compact('build', 'equipments', 'totalSkills'));
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