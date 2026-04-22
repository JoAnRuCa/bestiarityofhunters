<?php

namespace App\Http\Controllers;

use App\Models\Build;
use App\Http\Requests\StoreBuildRequest;
use App\Services\BuildService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BuildEditorController extends Controller
{
    /** @var BuildService */
    protected $buildService;

    public function __construct(BuildService $buildService)
    {
        $this->buildService = $buildService;
    }

    public function index()
    {
        return view('seccion.buildEditor');
    }

    public function store(StoreBuildRequest $request)
    {
        try {
            $build = $this->buildService->storeBuild($request->all(), Auth::id());

            return response()->json([
                'success' => true,
                'message' => '¡Build forjada correctamente!',
                'redirect_url' => url('/build-editor/' . $build->slug)
            ]);
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
        $build = Build::with('tags')->where('slug', $slug)->firstOrFail();

        $this->authorize('view', $build);

        $details = $this->buildService->getBuildDetails($build);

        return view('seccion.buildEditorShow', [
            'build' => $build,
            'equipments' => $details['equipments'],
            'totalSkills' => $details['totalSkills']
        ]);
    }
}