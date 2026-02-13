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
     * Muestra el formulario de edición
     */
    public function edit($id)
    {
        $build = Build::findOrFail($id);

        // Seguridad: Solo el dueño edita
        if ($build->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar esta build.');
        }

        // Procesamos los datos actuales para que el editor los cargue
        $processedData = $this->getProcessedBuildData($build);
        
        return view('seccion.editBuild', [
            'build' => $build,
            'equipments' => $processedData['equipments'],
            'totalSkills' => $processedData['totalSkills']
        ]);
    }

    /**
     * Elimina la build y todas sus dependencias técnicas
     */
    public function destroy($id)
    {
        $build = Build::findOrFail($id);

        if ($build->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            // 1. Obtener IDs de los equipos vinculados (Plural corregido: builds_equipments)
            $equipmentIds = DB::table('builds_equipments')->where('build_id', $id)->pluck('id');

            // 2. Borrar decoraciones (Plural corregido: builds_equipments_decorations)
            DB::table('builds_equipments_decorations')->whereIn('build_equipment_id', $equipmentIds)->delete();

            // 3. Borrar registros de equipos
            DB::table('builds_equipments')->where('build_id', $id)->delete();

            // 4. Limpiar favoritos/guardados
            DB::table('saved_builds')->where('build_id', $id)->delete();

            // 5. Borrar Votos y Tags
            $build->votos()->delete(); 
            if (method_exists($build, 'tags')) {
                $build->tags()->detach();
            }

            // 6. Borrado final del modelo
            $build->delete();

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'error' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($slug)
    {
        $build = Build::where('slug', $slug)
            ->with(['tags', 'user', 'votos', 'comments.user'])
            ->firstOrFail();

        $processedData = $this->getProcessedBuildData($build);

        return view('seccion.buildShow', [
            'build' => $build,
            'equipments' => $processedData['equipments'],
            'totalSkills' => $processedData['totalSkills']
        ]);
    }

    /**
     * Lógica de procesamiento (Plurales de tablas corregidos)
     */
    private function getProcessedBuildData($build)
    {
        // Corregido a plural: builds_equipments
        $equipments = DB::table('builds_equipments')->where('build_id', $build->id)->get();
        
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

            // Corregido a plural: builds_equipments_decorations
            $savedDecos = DB::table('builds_equipments_decorations')->where('build_equipment_id', $eq->id)->get();
            $eq->attached_decos = [];
            foreach ($savedDecos as $d) {
                $decoInfo = collect($allDecorations)->firstWhere('id', $d->decoration_id);
                if ($decoInfo) {
                    $eq->attached_decos[] = ['name' => $decoInfo['name'], 'level' => $decoInfo['slot'] ?? 1, 'is_empty' => false];
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
            if (isset($charm['ranks'])) {
                foreach ($charm['ranks'] as $rank) { $normalized[] = $rank; }
            }
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
            foreach ((array)$request->tag as $tag) {
                $query->whereHas('tags', fn($q) => $q->where('name', $tag));
            }
        }
        $request->orden === 'votados' ? $query->orderByRaw('COALESCE(score_sum, 0) DESC') : $query->orderBy('builds.created_at', 'desc');
        return $query;
    }
}