<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Build;
use App\Services\BuildService; // Inyectamos el servicio
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreBuildRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BuildController extends Controller
{
    /** @var BuildService */
    protected $buildService;

    public function __construct(BuildService $buildService)
    {
        $this->buildService = $buildService;
    }

    /**
     * Lista todas las builds en el panel de admin.
     */
    public function index(Request $request)
    {
        // Reutilizamos la lógica de filtrado del servicio
        $builds = $this->buildService->getFilteredBuilds($request)
            ->latest()
            ->paginate(15);

        $search = $request->input('search');

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

            return DB::transaction(function () use ($request, $buildData, $decoData) {
                // Creamos la instancia base
                $build = Build::create([
                    'titulo'    => $request->titulo,
                    'slug'      => Str::slug($request->titulo) . '-' . Str::random(5),
                    'playstyle' => $request->playstyle,
                    'user_id'   => Auth::id(),
                ]);

                // Ajustamos slug con ID real
                $build->update(['slug' => Str::slug($request->titulo) . '-' . $build->id]);

                // USAMOS EL SERVICIO PARA GUARDAR EQUIPOS
                $this->buildService->saveBuildEquipment($build->id, $buildData, $decoData);

                if ($request->has('tags')) {
                    $build->tags()->sync($request->tags);
                }

                session()->flash('success', 'Build "' . $build->titulo . '" forged successfully by Admin!');

                return response()->json([
                    'success' => true,
                    'redirect_url' => route('admin.builds.index')
                ]);
            });
        } catch (\Exception $e) {
            Log::error("Error guardando build admin: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Edita la build usando el servicio para procesar datos.
     */
    public function edit(Build $build) 
    {
        // USAMOS EL SERVICIO
        $processedData = $this->buildService->getBuildDetails($build);
        
        $previous_url = old('previous_url', url()->previous());
        
        $jsPreload = [];
        $weaponCount = 0;

        foreach ($processedData['equipments'] as $eq) {
            $slotKey = null;
            $tipo = (int)$eq->tipo;

            if ($tipo === 1) {
                $weaponCount++;
                $slotKey = 'weapon' . $weaponCount;
            } elseif ($tipo === 2) {
                $slotKey = $this->buildService->getArmorKind($eq->equipment_id);
            } elseif ($tipo === 3) {
                $slotKey = 'charm';
            }
            
            if ($slotKey) {
                $jsPreload[$slotKey] = [
                    'id' => $eq->equipment_id,
                    'decos' => collect($eq->attached_decos)
                        ->filter(function($d) { return !$d['is_empty']; })
                        ->map(function($d) { 
                            return [
                                'id' => isset($d['id']) ? $d['id'] : null,
                                'name' => $d['name']
                            ]; 
                        }) 
                        ->values()
                        ->toArray()
                ];
            }
        }

        return view('admin.builds.edit', [
            'build' => $build,
            'equipments' => $processedData['equipments'],
            'totalSkills' => collect($processedData['totalSkills']),
            'jsPreload' => $jsPreload,
            'previous_url' => $previous_url
        ]);
    }

    /**
     * Actualiza la build (Admin).
     */
    public function update(StoreBuildRequest $request, Build $build)
    {
        try {
            $buildData = json_decode($request->input('build_data'), true);
            $decoData = json_decode($request->input('decorations_data'), true);

            return DB::transaction(function () use ($request, $build, $buildData, $decoData) {
                $build->update([
                    'titulo'    => $request->input('titulo'),
                    'slug'      => Str::slug($request->input('titulo')) . '-' . $build->id,
                    'playstyle' => $request->input('playstyle'),
                ]);

                // Limpieza
                $currentEquipIds = DB::table('builds_equipments')->where('build_id', $build->id)->pluck('id');
                DB::table('builds_equipments_decorations')->whereIn('build_equipment_id', $currentEquipIds)->delete();
                DB::table('builds_equipments')->where('build_id', $build->id)->delete();

                // USAMOS EL SERVICIO
                $this->buildService->saveBuildEquipment($build->id, $buildData, $decoData);

                if ($request->has('tags')) {
                    $build->tags()->sync($request->input('tags'));
                }

                session()->flash('success', 'Build updated successfully.');
                
                return response()->json([
                    'success' => true, 
                    'redirect_url' => $request->input('previous_url') ?: route('admin.builds.index')
                ]);
            });
        } catch (\Exception $e) {
            Log::error("Error updating build admin: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Elimina la build.
     */
    public function destroy(Build $build)
    {
        try {
            DB::transaction(function () use ($build) {
                $equipmentIds = DB::table('builds_equipments')->where('build_id', $build->id)->pluck('id');
                
                DB::table('builds_equipments_decorations')->whereIn('build_equipment_id', $equipmentIds)->delete();
                DB::table('builds_equipments')->where('build_id', $build->id)->delete();
                DB::table('saved_builds')->where('build_id', $build->id)->delete();
                
                if (method_exists($build, 'votos')) $build->votos()->delete();
                if (method_exists($build, 'comments')) $build->comments()->delete();
                
                $build->tags()->detach();
                $build->delete();
            });

            return redirect()->route('admin.builds.index')->with('success', 'Build deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting: ' . $e->getMessage());
        }
    }
}