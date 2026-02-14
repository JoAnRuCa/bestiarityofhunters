<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Build;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BuildController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $builds = Build::with(['user', 'tags'])
            ->when($search, function ($query, $search) {
                return $query->where('titulo', 'LIKE', "%{$search}%")
                      ->orWhereHas('user', function ($u) use ($search) {
                          $u->where('name', 'LIKE', "%{$search}%");
                      });
            })
            ->latest()
            ->get();

        return view('admin.builds.index', compact('builds', 'search'));
    }

    public function edit(Build $build)
    {
        $processedData = $this->getProcessedBuildData($build);
        
        $jsPreload = [];
        $weaponCount = 0;
        foreach ($processedData['equipments'] as $eq) {
            $slotKey = null;
            if ($eq->tipo == 1) {
                $weaponCount++;
                $slotKey = 'weapon' . $weaponCount;
            } elseif ($eq->tipo == 2) {
                $slotKey = $this->getArmorSlotName($eq->equipment_id);
            } elseif ($eq->tipo == 3) {
                $slotKey = 'charm';
            }
            
            if ($slotKey) {
                $jsPreload[$slotKey] = [
                    'id' => $eq->equipment_id,
                    'decos' => collect($eq->attached_decos)
                                ->filter(function($d) { return !$d['is_empty']; })
                                ->map(function($d) { return ['name' => $d['name']]; }) 
                                ->values()
                                ->toArray()
                ];
            }
        }

        return view('admin.builds.edit', [
            'build' => $build,
            'equipments' => $processedData['equipments'],
            'totalSkills' => collect($processedData['totalSkills']),
            'jsPreload' => $jsPreload 
        ]);
    }

// Cambia esto:
// public function update(Request $request, Build $build)

// Por esto:
public function update(Request $request, $id)
{
    // Buscamos manualmente para ver si existe
    $build = Build::find($id);

    if (!$build) {
        return response()->json([
            'success' => false, 
            'error' => "La build con ID $id no existe en la base de datos."
        ], 404);
    }

    try {
        // Obtenemos los datos crudos del JS
        $buildData = json_decode($request->input('build_data'), true);
        $decoData = json_decode($request->input('decorations_data'), true);

        if (!$buildData) {
            return response()->json(['success' => false, 'error' => 'Invalid build data'], 400);
        }

        return DB::transaction(function () use ($request, $build, $buildData, $decoData) {
            // 1. Actualizar datos básicos
            $build->update([
                'titulo'    => $request->input('name'),
                'playstyle' => $request->input('playstyle'),
            ]);

            // 2. Limpiar equipamiento actual (Cascada manual para evitar errores de FK)
            $currentEquipIds = DB::table('builds_equipments')->where('build_id', $build->id)->pluck('id');
            DB::table('builds_equipments_decorations')->whereIn('build_equipment_id', $currentEquipIds)->delete();
            DB::table('builds_equipments')->where('build_id', $build->id)->delete();

            // 3. Mapeo de tipos para la DB
            $typeMap = [
                'weapon1' => 1, 'weapon2' => 1,
                'head' => 2, 'chest' => 2, 'arms' => 2, 'waist' => 2, 'legs' => 2,
                'charm' => 3
            ];

            // 4. Guardar Equipamiento
            foreach ($buildData as $slot => $item) {
                if (!$item || !isset($item['id'])) continue;

                $insertedId = DB::table('builds_equipments')->insertGetId([
                    'build_id'     => $build->id,
                    'equipment_id' => $item['id'],
                    'tipo'         => isset($typeMap[$slot]) ? $typeMap[$slot] : 0,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                // 5. Guardar Decoraciones de este slot
                if (isset($decoData[$slot]) && is_array($decoData[$slot])) {
                    foreach ($decoData[$slot] as $deco) {
                        if ($deco && isset($deco['id'])) {
                            DB::table('builds_equipments_decorations')->insert([
                                'build_equipment_id' => $insertedId,
                                'decoration_id'      => $deco['id'],
                                'created_at'         => now(),
                                'updated_at'         => now(),
                            ]);
                        }
                    }
                }
            }

            // 6. Sincronizar Tags
            if ($request->has('tags')) {
                $build->tags()->sync($request->input('tags'));
            }

            return response()->json([
                'success' => true, 
                'redirect_url' => route('admin.builds.index')
            ]);
        });
    } catch (\Exception $e) {
        // Esto ayudará a ver el error real en la consola F12
        return response()->json([
            'success' => false, 
            'error' => $e->getMessage(),
            'line' => $e->getLine()
        ], 500);
    }
}

    private function getArmorSlotName($id) {
        $armors = json_decode(Storage::get('data/armors.json'), true) ?: [];
        foreach($armors as $a) { if($a['id'] == $id) return $a['kind']; }
        return null;
    }

    private function getProcessedBuildData($build) {
        $equipments = DB::table('builds_equipments')->where('build_id', $build->id)->orderBy('id')->get();
        $weapons = json_decode(Storage::get('data/weapons.json'), true) ?: [];
        $armors = json_decode(Storage::get('data/armors.json'), true) ?: [];
        $charms = $this->getNormalizedCharms();
        $allDecorations = json_decode(Storage::get('data/decorations.json'), true) ?: [];
        $skillsData = json_decode(Storage::get('data/skills.json'), true) ?: [];

        $skillMaxLevels = [];
        foreach ($skillsData as $s) {
            if (isset($s['name']) && isset($s['ranks'])) {
                $skillMaxLevels[trim($s['name'])] = count($s['ranks']);
            }
        }

        $totalSkillsRaw = [];
        foreach ($equipments as $eq) {
            $source = [];
            switch((int)$eq->tipo) {
                case 1: $source = $weapons; break;
                case 2: $source = $armors; break;
                case 3: $source = $charms; break;
            }
            
            $itemData = collect($source)->firstWhere('id', $eq->equipment_id);
            if ($itemData) {
                $eq->real_name = isset($itemData['name']) ? $itemData['name'] : (isset($itemData['weaponName']) ? $itemData['weaponName'] : 'Unknown');
                $eq->total_slots = isset($itemData['slots']) ? $itemData['slots'] : [];
                
                $skills = isset($itemData['skills']) ? $itemData['skills'] : (isset($itemData['skill']) ? [$itemData] : []);
                foreach ($skills as $s) {
                    $name = trim(isset($s['skill']['name']) ? $s['skill']['name'] : (isset($s['name']) ? $s['name'] : ''));
                    if ($name) $totalSkillsRaw[$name] = (isset($totalSkillsRaw[$name]) ? $totalSkillsRaw[$name] : 0) + (isset($s['level']) ? $s['level'] : 1);
                }
            }
            $savedDecos = DB::table('builds_equipments_decorations')->where('build_equipment_id', $eq->id)->get();
            $eq->attached_decos = [];
            foreach ($savedDecos as $d) {
                $decoInfo = collect($allDecorations)->firstWhere('id', $d->decoration_id);
                if ($decoInfo) {
                    $eq->attached_decos[] = ['name' => $decoInfo['name'], 'is_empty' => false];
                    $dSkills = isset($decoInfo['skills']) ? $decoInfo['skills'] : [];
                    foreach ($dSkills as $ds) {
                        $dn = trim(isset($ds['skill']['name']) ? $ds['skill']['name'] : (isset($ds['name']) ? $ds['name'] : ''));
                        if ($dn) $totalSkillsRaw[$dn] = (isset($totalSkillsRaw[$dn]) ? $totalSkillsRaw[$dn] : 0) + (isset($ds['level']) ? $ds['level'] : 1);
                    }
                }
            }
        }

        $totalSkills = collect($totalSkillsRaw)->map(function($lvl, $name) use ($skillMaxLevels, $skillsData) {
            $max = isset($skillMaxLevels[$name]) ? $skillMaxLevels[$name] : 5;
            $currentLvl = (int)min($lvl, $max);
            $skillInfo = collect($skillsData)->first(function($item) use ($name) { 
                return trim(isset($item['name']) ? $item['name'] : '') === $name; 
            });
            $desc = isset($skillInfo['ranks'][$currentLvl - 1]['description']) ? $skillInfo['ranks'][$currentLvl - 1]['description'] : "No desc";
            return [
                'name' => $name, 'lvl' => $currentLvl, 'max' => $max, 
                'percent' => ($max > 0) ? ($currentLvl / $max) * 100 : 0, 'desc' => $desc
            ];
        })->sort(function($a, $b) { return $b['lvl'] <=> $a['lvl']; })->values();

        return ['equipments' => $equipments, 'totalSkills' => $totalSkills];
    }

    private function getNormalizedCharms() {
        $charmsRaw = json_decode(Storage::get('data/charms.json'), true) ?: [];
        $normalized = [];
        foreach ($charmsRaw as $charm) {
            if (isset($charm['ranks'])) { foreach ($charm['ranks'] as $rank) { $normalized[] = $rank; } }
        }
        return $normalized;
    }
}