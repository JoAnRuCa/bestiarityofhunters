<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Build;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BuildListController extends Controller
{
    /**
     * Lista pública de builds con filtros.
     */
    public function index(Request $request)
    {
        $query = $this->applyFiltersAndSorting($request);
        $builds = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('components.build-grid', [
                'builds' => $builds,
                'editable' => true 
            ])->render();
        }

        return view('seccion.buildList', compact('builds'));
    }

    /**
     * Lista de builds del usuario autenticado.
     */
    public function myBuilds(Request $request)
    {
        $query = $this->applyFiltersAndSorting($request, Auth::id());
        $builds = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('components.build-grid', [
                'builds' => $builds,
                'editable' => true
            ])->render();
        }

        return view('seccion.myBuilds', compact('builds'));
    }

    /**
     * Muestra el formulario de edición (Slug-based).
     */
    public function edit($slug)
    {
        $build = Build::where('slug', $slug)->firstOrFail();

        // Permiso: Dueño o Admin
        if ($build->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Acceso no autorizado.');
        }

        $processedData = $this->getProcessedBuildData($build);
        
        // Guardamos la URL de procedencia para el redireccionamiento posterior
        $previous_url = old('previous_url', url()->previous());
        
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
                                ->filter(fn($d) => !$d['is_empty'])
                                ->map(fn($d) => ['id' => $d['id'] ?? null, 'name' => $d['name']]) 
                                ->values()
                                ->toArray()
                ];
            }
        }

        return view('seccion.editBuild', [
            'build' => $build,
            'equipments' => $processedData['equipments'],
            'totalSkills' => collect($processedData['totalSkills']),
            'jsPreload' => $jsPreload,
            'previous_url' => $previous_url
        ]);
    }

    /**
     * Procesa la actualización de la build.
     */
    public function update(Request $request, $slug)
    {
        try {
            $build = Build::where('slug', $slug)->firstOrFail();

            if ($build->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $buildData = json_decode($request->input('build_data'), true);
            $decoData = json_decode($request->input('decorations_data'), true);

            $categoryMap = [
                'weapon1' => 1, 'weapon2' => 1,
                'head'    => 2, 'chest'   => 2, 'arms' => 2, 'waist' => 2, 'legs' => 2,
                'charm'   => 3
            ];

            return DB::transaction(function () use ($request, $build, $buildData, $decoData, $categoryMap) {
                // Actualizamos datos y regeneramos slug para mantener unicidad
                $build->update([
                    'titulo'    => $request->name,
                    'slug'      => Str::slug($request->name) . '-' . $build->id,
                    'playstyle' => $request->playstyle,
                ]);

                // Limpieza de equipos y decoraciones previas
                $currentEquipIds = DB::table('builds_equipments')->where('build_id', $build->id)->pluck('id');
                DB::table('builds_equipments_decorations')->whereIn('build_equipment_id', $currentEquipIds)->delete();
                DB::table('builds_equipments')->where('build_id', $build->id)->delete();

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

                // Determinamos a dónde enviar al usuario tras guardar
                $redirectUrl = route('my.builds');
                if (Auth::user()->role === 'admin') {
                    // Si es admin, intentamos volver a la lista de administración
                    $redirectUrl = $request->input('previous_url') ?: route('admin.builds.index');
                }

                return response()->json([
                    'success' => true,
                    'message' => '¡Build actualizada correctamente!',
                    'redirect_url' => $redirectUrl
                ]);
            });

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Elimina la build y sus relaciones.
     */
    public function destroy($slug) 
    {
        $build = Build::where('slug', $slug)->firstOrFail();

        if ($build->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();
            $internalId = $build->id;
            $equipmentIds = DB::table('builds_equipments')->where('build_id', $internalId)->pluck('id');
            
            DB::table('builds_equipments_decorations')->whereIn('build_equipment_id', $equipmentIds)->delete();
            DB::table('builds_equipments')->where('build_id', $internalId)->delete();
            DB::table('saved_builds')->where('build_id', $internalId)->delete();
            
            if (method_exists($build, 'votos')) { $build->votos()->delete(); }
            if (method_exists($build, 'tags')) { $build->tags()->detach(); }
            
            $build->delete();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Muestra el detalle de una build.
     */
    public function show($slug)
    {
        $build = Build::where('slug', $slug)->with(['tags', 'user', 'votos', 'comments.user'])->firstOrFail();
        $processedData = $this->getProcessedBuildData($build);
        return view('seccion.buildShow', [
            'build' => $build,
            'equipments' => $processedData['equipments'],
            'totalSkills' => $processedData['totalSkills']
        ]);
    }

    // --- MÉTODOS PRIVADOS DE APOYO ---

    private function getProcessedBuildData($build)
    {
        $equipments = DB::table('builds_equipments')->where('build_id', $build->id)->orderBy('id')->get();
        $weapons = json_decode(Storage::get('data/weapons.json'), true) ?: [];
        $armors = json_decode(Storage::get('data/armors.json'), true) ?: [];
        $charms = $this->getNormalizedCharms();
        $allDecorations = json_decode(Storage::get('data/decorations.json'), true) ?: [];
        $skillsData = json_decode(Storage::get('data/skills.json'), true) ?: [];

        $skillMaxLevels = [];
        foreach ($skillsData as $s) {
            if (isset($s['name']) && isset($s['ranks'])) { $skillMaxLevels[trim($s['name'])] = count($s['ranks']); }
        }

        $totalSkillsRaw = [];
        $weaponSkills = []; 
        $tipoLabels = [1 => 'Weapon', 2 => 'Armor Piece', 3 => 'Charm'];

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

            $savedDecos = DB::table('builds_equipments_decorations')->where('build_equipment_id', $eq->id)->get();
            $eq->attached_decos = [];
            foreach ($savedDecos as $d) {
                $decoInfo = collect($allDecorations)->firstWhere('id', $d->decoration_id);
                if ($decoInfo) {
                    $eq->attached_decos[] = [
                        'id' => $decoInfo['id'],
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
            if (isset($eq->total_slots) && is_array($eq->total_slots)) {
                $numEquipadas = count($eq->attached_decos);
                if ($numEquipadas < count($eq->total_slots)) {
                    for ($i = $numEquipadas; $i < count($eq->total_slots); $i++) {
                        $eq->attached_decos[] = ['name' => null, 'level' => $eq->total_slots[$i], 'is_empty' => true];
                    }
                }
            }
        }

        $totalSkills = collect($totalSkillsRaw)->map(function($lvl, $name) use ($skillMaxLevels, $weaponSkills, $skillsData) {
            $nameClean = trim($name);
            $max = $skillMaxLevels[$nameClean] ?? 5;
            $currentLvl = (int)min($lvl, $max);
            $skillInfo = collect($skillsData)->first(fn($item) => trim($item['name'] ?? '') === $nameClean);
            $desc = "Description not found.";
            if ($skillInfo && isset($skillInfo['ranks'][$currentLvl - 1])) {
                $rank = $skillInfo['ranks'][$currentLvl - 1];
                $desc = $rank['description'] ?? $rank['desc'] ?? $desc;
            }
            return [
                'name' => $nameClean, 'lvl' => $currentLvl, 'max' => $max,
                'percent' => ($max > 0) ? ($currentLvl / $max) * 100 : 0,
                'desc' => $desc, 'is_weapon' => isset($weaponSkills[$nameClean]) ? 1 : 0
            ];
        })->sort(fn($a, $b) => $b['lvl'] <=> $a['lvl'])->values()->toArray();

        return ['equipments' => $equipments, 'totalSkills' => $totalSkills];
    }

    private function getNormalizedCharms() 
    {
        $charmsRaw = json_decode(Storage::get('data/charms.json'), true) ?: [];
        $normalized = [];
        foreach ($charmsRaw as $charm) {
            if (isset($charm['ranks'])) { foreach ($charm['ranks'] as $rank) { $normalized[] = $rank; } }
        }
        return $normalized;
    }

    private function applyFiltersAndSorting(Request $request, $userId = null)
    {
        $query = Build::with(['tags', 'user', 'votos'])->withSum('votos as score_sum', 'tipo');
        if ($userId) $query->where('builds.user_id', $userId);
        if ($request->filled('search')) $query->where('builds.titulo', 'like', '%' . $request->search . '%');
        if (!$userId && $request->filled('autor')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->autor . '%'));
        }
        if ($request->filled('tag')) {
            foreach ((array)$request->tag as $tag) { $query->whereHas('tags', fn($q) => $q->where('name', $tag)); }
        }
        $request->orden === 'votados' ? $query->orderByRaw('COALESCE(score_sum, 0) DESC') : $query->orderBy('builds.created_at', 'desc');
        return $query;
    }

    private function getArmorSlotName($id) {
        $armors = json_decode(Storage::get('data/armors.json'), true) ?: [];
        foreach($armors as $a) { if($a['id'] == $id) return $a['kind']; }
        return null;
    }
}