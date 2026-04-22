<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Build;
use App\Services\BuildService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BuildListController extends Controller
{
    /** @var BuildService */
    protected $buildService;

    /**
     * Inyección del servicio para centralizar toda la lógica de negocio.
     */
    public function __construct(BuildService $buildService)
    {
        $this->buildService = $buildService;
    }

    /**
     * Lista pública de builds con filtros delegados al servicio.
     */
    public function index(Request $request)
    {
        $builds = $this->buildService->getFilteredBuilds($request)
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('components.build-grid', [
                'builds' => $builds,
                'editable' => false 
            ])->render();
        }

        return view('seccion.buildList', compact('builds'));
    }

    /**
     * Lista de builds del usuario autenticado.
     */
    public function myBuilds(Request $request)
    {
        $builds = $this->buildService->getFilteredBuilds($request, Auth::id())
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('components.build-grid', [
                'builds' => $builds,
                'editable' => true
            ])->render();
        }

        return view('seccion.myBuilds', compact('builds'));
    }

    /**
     * Muestra el detalle de una build.
     */
    public function show($slug)
    {
        $build = Build::where('slug', $slug)
            ->with(['tags', 'user', 'votos', 'comments.user'])
            ->firstOrFail();
        
        $processedData = $this->buildService->getBuildDetails($build);

        return view('seccion.buildShow', [
            'build' => $build,
            'equipments' => $processedData['equipments'],
            'totalSkills' => $processedData['totalSkills']
        ]);
    }

    /**
     * Prepara los datos para el editor de builds.
     */
    public function edit($slug)
    {
        $build = Build::where('slug', $slug)->firstOrFail();

        $this->authorize('update', $build);

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
                // El servicio ahora nos dice qué pieza es (head, chest...)
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

        return view('seccion.editBuild', [
            'build' => $build,
            'equipments' => $processedData['equipments'],
            'totalSkills' => collect($processedData['totalSkills']),
            'jsPreload' => $jsPreload,
            'previous_url' => $previous_url
        ]);
    }

    /**
     * Actualiza la build delegando el guardado al servicio.
     */
    public function update(Request $request, $slug)
    {
        try {
            $build = Build::where('slug', $slug)->firstOrFail();

            $this->authorize('update', $build);

            $buildData = json_decode($request->input('build_data'), true);
            $decoData = json_decode($request->input('decorations_data'), true);

            return DB::transaction(function () use ($request, $build, $buildData, $decoData) {
                $build->update([
                    'titulo'    => $request->titulo,
                    'slug'      => Str::slug($request->titulo) . '-' . $build->id,
                    'playstyle' => $request->playstyle,
                ]);

                // Limpieza rápida
                $currentEquipIds = DB::table('builds_equipments')->where('build_id', $build->id)->pluck('id');
                DB::table('builds_equipments_decorations')->whereIn('build_equipment_id', $currentEquipIds)->delete();
                DB::table('builds_equipments')->where('build_id', $build->id)->delete();

                // Delegamos el guardado de equipo al Service
                $this->buildService->saveBuildEquipment($build->id, $buildData, $decoData);

                if ($request->has('tags')) {
                    $build->tags()->sync($request->tags);
                }

                $redirectUrl = (Auth::user()->role === 'admin') 
                    ? ($request->input('previous_url') ?: route('admin.builds.index')) 
                    : route('my.builds');

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
     * Elimina la build y limpia sus relaciones.
     */
    public function destroy($slug) 
    {
        $build = Build::where('slug', $slug)->firstOrFail();

        $this->authorize('delete', $build);

        try {
            DB::beginTransaction();
            
            $equipmentIds = DB::table('builds_equipments')->where('build_id', $build->id)->pluck('id');
            DB::table('builds_equipments_decorations')->whereIn('build_equipment_id', $equipmentIds)->delete();
            DB::table('builds_equipments')->where('build_id', $build->id)->delete();
            DB::table('saved_builds')->where('build_id', $build->id)->delete();
            
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
}