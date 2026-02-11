<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Build;
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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'playstyle' => 'nullable|string',
            'build_data' => 'required',
            'decorations_data' => 'required',
            'tags' => 'nullable|array'
        ]);

        try {
            $buildData = json_decode($request->input('build_data'), true);
            $decoData = json_decode($request->input('decorations_data'), true);

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

                    $tipoNumerico = isset($categoryMap[$slot]) ? $categoryMap[$slot] : 0;

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
                    'slug'    => $build->slug
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
        // IMPORTANTE: .with('tags') carga los nombres de los tags
        $build = Build::with('tags')->where('slug', $slug)->firstOrFail();
        
        $equipments = DB::table('builds_equipments')->where('build_id', $build->id)->get();

        $weapons = json_decode(Storage::get('data/weapons.json'), true) ?: [];
        $armors = json_decode(Storage::get('data/armors.json'), true) ?: [];
        $charms = $this->getNormalizedCharms();
        $allDecorations = json_decode(Storage::get('data/decorations.json'), true) ?: [];
        
        $skillsData = json_decode(Storage::get('data/skills.json'), true) ?: [];
        $skillMaxLevels = [];
        foreach ($skillsData as $s) {
            if (isset($s['name']) && isset($s['ranks'])) {
                $skillMaxLevels[$s['name']] = count($s['ranks']);
            }
        }

        $totalSkills = [];

        foreach ($equipments as $eq) {
            $source = [];
            switch ((int)$eq->tipo) {
                case 1: $source = $weapons; break;
                case 2: $source = $armors; break;
                case 3: $source = $charms; break;
            }

            $itemData = collect($source)->firstWhere('id', $eq->equipment_id);
            
            if ($itemData) {
                $eq->real_name = isset($itemData['name']) ? $itemData['name'] : 
                                (isset($itemData['weaponName']) ? $itemData['weaponName'] : 
                                (isset($itemData['charmName']) ? $itemData['charmName'] : 'Unknown Item'));

                if (isset($itemData['skill']['name'])) {
                    $name = $itemData['skill']['name'];
                    $lvl = isset($itemData['level']) ? $itemData['level'] : 1;
                    $totalSkills[$name] = (isset($totalSkills[$name]) ? $totalSkills[$name] : 0) + $lvl;
                } elseif (isset($itemData['skills']) && is_array($itemData['skills'])) {
                    foreach ($itemData['skills'] as $s) {
                        $name = isset($s['skill']['name']) ? $s['skill']['name'] : (isset($s['name']) ? $s['name'] : null);
                        if ($name) {
                            $lvl = isset($s['level']) ? $s['level'] : 1;
                            $totalSkills[$name] = (isset($totalSkills[$name]) ? $totalSkills[$name] : 0) + $lvl;
                        }
                    }
                }
            }

            $decos = DB::table('builds_equipments_decorations')->where('build_equipment_id', $eq->id)->get();
            $eq->attached_decos = [];
            foreach ($decos as $d) {
                $decoInfo = collect($allDecorations)->firstWhere('id', $d->decoration_id);
                if ($decoInfo) {
                    $eq->attached_decos[] = [
                        'name' => isset($decoInfo['name']) ? $decoInfo['name'] : 'Jewel',
                        'level' => isset($decoInfo['slot']) ? $decoInfo['slot'] : 1
                    ];

                    if (isset($decoInfo['skills']) && is_array($decoInfo['skills'])) {
                        foreach ($decoInfo['skills'] as $ds) {
                            $dName = isset($ds['skill']['name']) ? $ds['skill']['name'] : (isset($ds['name']) ? $ds['name'] : null);
                            if ($dName) {
                                $dLvl = isset($ds['level']) ? $ds['level'] : 1;
                                $totalSkills[$dName] = (isset($totalSkills[$dName]) ? $totalSkills[$dName] : 0) + $dLvl;
                            }
                        }
                    }
                }
            }
        }

        arsort($totalSkills);
        return view('seccion.buildEditorShow', compact('build', 'equipments', 'totalSkills', 'skillMaxLevels'));
    }

    private function getNormalizedCharms() {
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