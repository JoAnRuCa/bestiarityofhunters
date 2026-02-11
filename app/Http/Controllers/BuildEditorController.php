<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Build;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Corregido: Importación necesaria para evitar el ErrorClass

class BuildEditorController extends Controller
{
    /**
     * Muestra la vista del editor de builds.
     */
    public function index()
    {
        return view('seccion.buildEditor');
    }

    /**
     * Guarda la build y sus decoraciones en la base de datos.
     */
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
            // Decodificar los JSON enviados desde el JS
            $buildData = json_decode($request->input('build_data'), true);
            $decoData = json_decode($request->input('decorations_data'), true);

            /** * MAPEO DE CATEGORÍAS 
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
                    'user_id'   => Auth::id() ?? 1,
                ]);

                // 3. Guardar Equipos y sus Decoraciones
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
    
    /**
     * Muestra la build finalizada procesando los JSON de datos.
     */
    public function show($slug)
    {
        // 1. Obtener la build o lanzar 404
        $build = Build::where('slug', $slug)->firstOrFail();
        
        // 2. Obtener el equipamiento de la base de datos
        $equipments = DB::table('builds_equipments')->where('build_id', $build->id)->get();

        // 3. Cargar datos JSON
        $weapons = json_decode(Storage::get('data/weapons.json'), true) ?: [];
        $armors = json_decode(Storage::get('data/armors.json'), true) ?: [];
        $charms = $this->getNormalizedCharms();
        $allDecorations = json_decode(Storage::get('data/decorations.json'), true) ?: [];
        
        // 4. Diccionario de Niveles Máximos
        $skillsData = json_decode(Storage::get('data/skills.json'), true) ?: [];
        $skillMaxLevels = [];
        foreach ($skillsData as $s) {
            if (isset($s['name']) && isset($s['ranks'])) {
                $skillMaxLevels[$s['name']] = count($s['ranks']);
            }
        }

        $totalSkills = [];

        // 5. Mapeo de equipo para procesar nombres y habilidades
        foreach ($equipments as $eq) {
            $source = [];
            switch ((int)$eq->tipo) {
                case 1: $source = $weapons; break;
                case 2: $source = $armors; break;
                case 3: $source = $charms; break;
            }

            $itemData = collect($source)->firstWhere('id', $eq->equipment_id);
            
            if ($itemData) {
                // Normalización de nombres (PHP 7.4)
                $eq->real_name = isset($itemData['name']) ? $itemData['name'] : 
                                (isset($itemData['weaponName']) ? $itemData['weaponName'] : 
                                (isset($itemData['charmName']) ? $itemData['charmName'] : 'Unknown Item'));

                // Sumar habilidades del equipo
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

            // 6. Decoraciones (Extraer nombre y nivel del slot para el círculo verde)
            $decos = DB::table('builds_equipments_decorations')
                ->where('build_equipment_id', $eq->id)
                ->get();
            
            $eq->attached_decos = [];
            foreach ($decos as $d) {
                $decoInfo = collect($allDecorations)->firstWhere('id', $d->decoration_id);
                if ($decoInfo) {
                    $eq->attached_decos[] = [
                        'name'  => isset($decoInfo['name']) ? $decoInfo['name'] : 'Jewel',
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

    /**
     * Aplana los rangos de los charms para una búsqueda eficiente.
     */
    private function getNormalizedCharms() {
        $charmsRaw = json_decode(Storage::get('data/charms.json'), true) ?: [];
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