<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Build;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreBuildRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BuildController extends Controller
{
    /**
     * Lista todas las builds en el panel de admin.
     */
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

    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {
        return view('admin.builds.create');
    }

    /**
     * Guarda una nueva build (Admin).
     */
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
                    'user_id'   => Auth::id(),
                ]);

                foreach ($buildData as $slot => $item) {
                    if (!$item || !isset($item['id'])) continue;

                    $buildEquipmentId = DB::table('builds_equipments')->insertGetId([
                        'build_id'     => $build->id,
                        'equipment_id' => $item['id'],
                        'tipo'         => $categoryMap[$slot] ?? 0, 
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
                    'message' => '¡Build forjada por el Admin!',
                    'redirect_url' => route('admin.builds.index')
                ]);
            });

        } catch (\Exception $e) {
            Log::error("Error guardando build admin: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Prepara los datos para el editor de edición.
     */
    public function edit($id)
    {
        $build = Build::findOrFail($id);
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
                                ->map(function($d) { return ['id' => $d['id'], 'name' => $d['name']]; }) 
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

    /**
     * Actualiza la build existente.
     */
    public function update(Request $request, $id)
    {
        $build = Build::findOrFail($id);

        try {
            $buildData = json_decode($request->input('build_data'), true);
            $decoData = json_decode($request->input('decorations_data'), true);

            if (!$buildData) {
                return response()->json(['success' => false, 'error' => 'Invalid build data'], 400);
            }

            return DB::transaction(function () use ($request, $build, $buildData, $decoData) {
                $build->update([
                    'titulo'    => $request->input('name'),
                    'playstyle' => $request->input('playstyle'),
                ]);

                // Limpiar relaciones actuales
                $currentEquipIds = DB::table('builds_equipments')->where('build_id', $build->id)->pluck('id');
                DB::table('builds_equipments_decorations')->whereIn('build_equipment_id', $currentEquipIds)->delete();
                DB::table('builds_equipments')->where('build_id', $build->id)->delete();

                $categoryMap = [
                    'weapon1' => 1, 'weapon2' => 1,
                    'head' => 2, 'chest' => 2, 'arms' => 2, 'waist' => 2, 'legs' => 2,
                    'charm' => 3
                ];

                foreach ($buildData as $slot => $item) {
                    if (!$item || !isset($item['id'])) continue;

                    $insertedId = DB::table('builds_equipments')->insertGetId([
                        'build_id'     => $build->id,
                        'equipment_id' => $item['id'],
                        'tipo'         => $categoryMap[$slot] ?? 0,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);

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

                if ($request->has('tags')) {
                    $build->tags()->sync($request->input('tags'));
                }

                return response()->json([
                    'success' => true, 
                    'redirect_url' => route('admin.builds.index')
                ]);
            });
        } catch (\Exception $e) {
            Log::error("Error actualizando build admin: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Elimina la build.
     */
    public function destroy($id)
    {
        try {
            $build = Build::findOrFail($id);
            return DB::transaction(function () use ($build) {
                $equipmentIds = DB::table('builds_equipments')->where('build_id', $build->id)->pluck('id');
                if ($equipmentIds->isNotEmpty()) {
                    DB::table('builds_equipments_decorations')->whereIn('build_equipment_id', $equipmentIds)->delete();
                    DB::table('builds_equipments')->where('build_id', $build->id)->delete();
                }
                $build->tags()->detach();
                $build->delete();
                return redirect()->route('admin.builds.index')->with('success', 'Build eliminada.');
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    // --- MÉTODOS PRIVADOS DE APOYO ---

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
                $eq->real_name = $itemData['name'] ?? $itemData['weaponName'] ?? 'Unknown';
                $eq->total_slots = $itemData['slots'] ?? [];
                
                $itemSkills = $itemData['skills'] ?? (isset($itemData['skill']) ? [$itemData['skill']] : []);
                foreach ($itemSkills as $s) {
                    $name = trim($s['skill']['name'] ?? $s['name'] ?? '');
                    if ($name) $totalSkillsRaw[$name] = ($totalSkillsRaw[$name] ?? 0) + ($s['level'] ?? 1);
                }
            }

            $savedDecos = DB::table('builds_equipments_decorations')->where('build_equipment_id', $eq->id)->get();
            $eq->attached_decos = [];
            foreach ($savedDecos as $d) {
                $decoInfo = collect($allDecorations)->firstWhere('id', $d->decoration_id);
                if ($decoInfo) {
                    $eq->attached_decos[] = ['id' => $decoInfo['id'], 'name' => $decoInfo['name'], 'is_empty' => false];
                    $dSkills = $decoInfo['skills'] ?? [];
                    foreach ($dSkills as $ds) {
                        $dn = trim($ds['skill']['name'] ?? $ds['name'] ?? '');
                        if ($dn) $totalSkillsRaw[$dn] = ($totalSkillsRaw[$dn] ?? 0) + ($ds['level'] ?? 1);
                    }
                }
            }
        }

        $totalSkills = collect($totalSkillsRaw)->map(function($lvl, $name) use ($skillMaxLevels, $skillsData) {
            $max = $skillMaxLevels[$name] ?? 5;
            $currentLvl = (int)min($lvl, $max);
            $skillInfo = collect($skillsData)->first(function($item) use ($name) { 
                return trim($item['name'] ?? '') === $name; 
            });
            $desc = $skillInfo['ranks'][$currentLvl - 1]['description'] ?? "No desc";
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