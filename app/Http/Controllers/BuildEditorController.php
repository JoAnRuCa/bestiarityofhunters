<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Build;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BuildEditorController extends Controller
{
    public function index()
    {
        return view('seccion.buildEditor');
    }

    public function store(Request $request)
    {
        // 1. Validar campos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'playstyle' => 'nullable|string',
            'build_data' => 'required', // JSON string de las piezas
            'decorations_data' => 'required', // JSON string de las decos
            'tags' => 'nullable|array'
        ]);

        try {
            // Decodificar los JSON que enviamos desde el JS
            $buildData = json_decode($request->input('build_data'), true);
            $decoData = json_decode($request->input('decorations_data'), true);

            /** * MAPEO DE CATEGORÍAS 
             * Convertimos el nombre del slot en un ID numérico para la DB
             * 1 = Weapon, 2 = Armor, 3 = Charm
             */
            $categoryMap = [
                'weapon1' => 1, 'weapon2' => 1,
                'head'    => 2, 'chest'   => 2, 'arms' => 2, 'waist' => 2, 'legs' => 2,
                'charm'   => 3
            ];

            return DB::transaction(function () use ($request, $buildData, $decoData, $categoryMap) {
                
                // 2. Crear la Build
                $build = Build::create([
                    'titulo'    => $request->name,
                    'playstyle' => $request->playstyle,
                    'user_id'   => Auth::id() ?? 1, // Fallback para pruebas
                ]);

                // 3. Guardar Equipos y sus Decoraciones
                foreach ($buildData as $slot => $item) {
                    if (!$item || !isset($item['id'])) continue;

                    // Traducir el nombre del slot (ej: 'head') a su número (ej: 2)
                    $tipoNumerico = $categoryMap[$slot] ?? 0;

                    // Insertar equipo con el tipo corregido (Integer)
                    $buildEquipmentId = DB::table('builds_equipments')->insertGetId([
                        'build_id'     => $build->id,
                        'equipment_id' => $item['id'],
                        'tipo'         => $tipoNumerico, 
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);

                    // Insertar decoraciones si existen para este slot
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

                // 4. Sincronizar Tags
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
    $build = Build::where('slug', $slug)->firstOrFail();
    $equipments = DB::table('builds_equipments')->where('build_id', $build->id)->get();

    // Cargar datos base
    $weapons = json_decode(\Storage::get('data/weapons.json'), true) ?: [];
    $armors = json_decode(\Storage::get('data/armors.json'), true) ?: [];
    $charms = $this->getNormalizedCharms();
    $allDecorations = json_decode(\Storage::get('data/decorations.json'), true) ?: [];
    
    // CARGAR NIVELES MÁXIMOS (Sincronizado con skills.json)
    $skillsData = json_decode(\Storage::get('data/skills.json'), true) ?: [];
    $skillMaxLevels = [];
    foreach ($skillsData as $s) {
        if (isset($s['name']) && isset($s['ranks'])) {
            $skillMaxLevels[$s['name']] = count($s['ranks']);
        }
    }

    $totalSkills = [];

    foreach ($equipments as $eq) {
        $source = [];
        // Reemplazamos match por switch para PHP 7.4
        switch ((int)$eq->tipo) {
            case 1: $source = $weapons; break;
            case 2: $source = $armors; break;
            case 3: $source = $charms; break;
        }

        $itemData = collect($source)->firstWhere('id', $eq->equipment_id);
        
        if ($itemData) {
            $eq->real_name = isset($itemData['name']) ? $itemData['name'] : 
                            (isset($itemData['weaponName']) ? $itemData['weaponName'] : 
                            (isset($itemData['charmName']) ? $itemData['charmName'] : null));

            // Lógica de habilidades del equipo (Casos A y B de tu JS)
            if (isset($itemData['skill']['name'])) {
                $name = $itemData['skill']['name'];
                $lvl = isset($itemData['level']) ? $itemData['level'] : 1;
                $totalSkills[$name] = ($totalSkills[$name] ?? 0) + $lvl;
            } elseif (isset($itemData['skills']) && is_array($itemData['skills'])) {
                foreach ($itemData['skills'] as $s) {
                    $name = isset($s['skill']['name']) ? $s['skill']['name'] : (isset($s['name']) ? $s['name'] : null);
                    if ($name) {
                        $lvl = isset($s['level']) ? $s['level'] : 1;
                        $totalSkills[$name] = ($totalSkills[$name] ?? 0) + $lvl;
                    }
                }
            }
        }

        // Decoraciones
        $decos = DB::table('builds_equipments_decorations')->where('build_equipment_id', $eq->id)->get();
        $eq->attached_decos = [];
        foreach ($decos as $d) {
            $decoInfo = collect($allDecorations)->firstWhere('id', $d->decoration_id);
            if ($decoInfo) {
                $eq->attached_decos[] = isset($decoInfo['name']) ? $decoInfo['name'] : 'Jewel';
                if (isset($decoInfo['skills']) && is_array($decoInfo['skills'])) {
                    foreach ($decoInfo['skills'] as $ds) {
                        $dName = isset($ds['skill']['name']) ? $ds['skill']['name'] : (isset($ds['name']) ? $ds['name'] : null);
                        if ($dName) {
                            $dLvl = isset($ds['level']) ? $ds['level'] : 1;
                            $totalSkills[$dName] = ($totalSkills[$dName] ?? 0) + $dLvl;
                        }
                    }
                }
            }
        }
    }

    arsort($totalSkills);
    return view('seccion.buildEditorShow', compact('build', 'equipments', 'totalSkills', 'skillMaxLevels'));
}
/**
 * Función auxiliar para normalizar talismanes igual que en el ApiController
 */
private function getNormalizedCharms() {
    $charmsRaw = json_decode(\Storage::get('data/charms.json'), true);
    $normalized = [];
    foreach ($charmsRaw as $charm) {
        if (isset($charm['ranks'])) {
            foreach ($charm['ranks'] as $rank) {
                $normalized[] = $rank;
            }
        }
    }
    return $normalized;
}

}