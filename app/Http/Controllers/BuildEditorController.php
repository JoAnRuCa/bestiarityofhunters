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

            return DB::transaction(function () use ($request, $buildData, $decoData) {
                
                // 2. Crear la Build (El slug se genera en el modelo)
                $build = Build::create([
                    'titulo'    => $request->name,
                    'playstyle' => $request->playstyle,
                    'user_id'   => Auth::id() ?? 1, // Fallback para pruebas
                ]);

                // 3. Guardar Equipos y sus Decoraciones
                foreach ($buildData as $slot => $item) {
                    if (!$item || !isset($item['id'])) continue;

                    // Insertar equipo
                    $buildEquipmentId = DB::table('builds_equipments')->insertGetId([
                        'build_id'     => $build->id,
                        'equipment_id' => $item['id'],
                        'tipo'         => $slot,
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

                // 4. Sincronizar Tags (esto maneja la tabla build_tags automáticamente)
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
}