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

    $weapons = json_decode(\Storage::get('data/weapons.json'), true);
    $armors = json_decode(\Storage::get('data/armors.json'), true);
    $charms = $this->getNormalizedCharms();
    $allDecorations = json_decode(\Storage::get('data/decorations.json'), true);

    $totalSkills = [];

    foreach ($equipments as $eq) {
        // 1. Determinar fuente de datos
        $source = [];
        if ($eq->tipo == 1) $source = $weapons;
        elseif ($eq->tipo == 2) $source = $armors;
        elseif ($eq->tipo == 3) $source = $charms;

        $itemData = collect($source)->firstWhere('id', $eq->equipment_id);
        
        // Asignar nombre (si no existe el item, queda como null)
        $eq->real_name = $itemData['name'] ?? $itemData['weaponName'] ?? $itemData['charmName'] ?? null;

        // 2. Sumar habilidades de la pieza (Seguro)
        if (isset($itemData['skills']) && is_array($itemData['skills'])) {
            foreach ($itemData['skills'] as $s) {
                // Buscamos el nombre en cualquiera de las posibles llaves
                $sName = $s['name'] ?? $s['skillName'] ?? null;
                if ($sName) {
                    $level = $s['level'] ?? $s['modifier'] ?? 1;
                    $totalSkills[$sName] = ($totalSkills[$sName] ?? 0) + $level;
                }
            }
        }

        // 3. Sumar habilidades de las decoraciones
        $decos = DB::table('builds_equipments_decorations')
            ->where('build_equipment_id', $eq->id)
            ->get();
        
        $eq->attached_decos = [];
        foreach ($decos as $d) {
            $decoInfo = collect($allDecorations)->firstWhere('id', $d->decoration_id);
            if ($decoInfo) {
                $eq->attached_decos[] = $decoInfo['name'] ?? 'Deco';
                if (isset($decoInfo['skills']) && is_array($decoInfo['skills'])) {
                    foreach ($decoInfo['skills'] as $ds) {
                        $dsName = $ds['name'] ?? $ds['skillName'] ?? null;
                        if ($dsName) {
                            $totalSkills[$dsName] = ($totalSkills[$dsName] ?? 0) + ($ds['level'] ?? 1);
                        }
                    }
                }
            }
        }
    }

    $header = $build->titulo;
    return view('seccion.buildEditorShow', compact('build', 'equipments', 'totalSkills', 'header'));
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