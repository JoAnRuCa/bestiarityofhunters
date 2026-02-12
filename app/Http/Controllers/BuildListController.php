<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Build;
use Illuminate\Support\Facades\Auth;

class BuildListController extends Controller
{
    /**
     * Listado público de Builds
     */
    public function index(Request $request)
    {
        $query = $this->applyFiltersAndSorting($request);
        $builds = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            // Reutilizamos el componente grid pero para builds
            return view('components.build-grid', [
                'builds' => $builds,
                'editable' => true 
            ])->render();
        }

        return view('seccion.buildList', compact('builds'));
    }

    /**
     * Mis Builds (Perfil personal)
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
     * Muestra una Build específica
     */
    public function show($slug)
    {
        $build = Build::where('slug', $slug)
            ->with(['tags', 'user', 'votos', 'equipments.decorations', 'comments.user'])
            ->firstOrFail();

        return view('seccion.buildShow', compact('build'));
    }

    /**
     * Lógica de filtrado (Igual que en Guides)
     */
    private function applyFiltersAndSorting(Request $request, $userId = null)
    {
        $query = Build::with(['tags', 'user', 'votos'])
            ->withSum('votos as score_sum', 'tipo');

        if ($userId) {
            $query->where('builds.user_id', $userId);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('builds.titulo', 'like', '%' . $request->search . '%')
                  ->orWhere('builds.playstyle', 'like', '%' . $request->search . '%');
            });
        }

        if (!$userId && $request->filled('autor')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->autor . '%');
            });
        }

        if ($request->filled('tag')) {
            $tags = (array) $request->tag;
            foreach ($tags as $tag) {
                $query->whereHas('tags', function ($q) use ($tag) {
                    $q->where('name', $tag);
                });
            }
        }

        if ($request->orden === 'votados') {
            $query->orderByDesc('score_sum');
        } else {
            $query->orderBy('builds.created_at', 'desc');
        }

        return $query;
    }
}