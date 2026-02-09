<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guide;
use Illuminate\Support\Facades\Auth;

class GuideListController extends Controller
{
    /**
     * Lista general de guías
     */
    public function index(Request $request)
    {
        // Llamamos a la lógica común sin forzar un usuario
        $query = $this->applyFiltersAndSorting($request);
        
        $guides = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('components.guide-grid', compact('guides'))->render();
        }

        return view('seccion.guidesList', compact('guides'));
    }

    /**
     * Mis Guías (Librería personal)
     */
    public function myGuides(Request $request)
    {
        // Llamamos a la misma lógica común, pero pasándole el ID del usuario
        $query = $this->applyFiltersAndSorting($request, Auth::id());

        $guides = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('components.guide-grid', compact('guides'))->render();
        }

        return view('seccion.myGuides', compact('guides'));
    }

    /**
     * MÉTODO PRIVADO: Aquí centralizamos toda la lógica de filtrado y ordenación
     */
    private function applyFiltersAndSorting(Request $request, $userId = null)
    {
        $query = Guide::with(['tags', 'user', 'votos'])
            ->withSum('votos as score_sum', 'tipo');

        // Si pasamos un userId, filtramos por dueño (para My Guides)
        if ($userId) {
            $query->where('guides.user_id', $userId);
        }

        // Filtro de búsqueda (Título o Contenido)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('guides.titulo', 'like', '%' . $request->search . '%')
                  ->orWhere('guides.contenido', 'like', '%' . $request->search . '%');
            });
        }

        // Filtro por Autor (solo si no estamos en My Guides)
        if (!$userId && $request->filled('autor')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->autor . '%');
            });
        }

        // Filtro por Tags (Lógica común)
        if ($request->filled('tag')) {
            $tags = (array) $request->tag;
            foreach ($tags as $tag) {
                $query->whereHas('tags', function ($q) use ($tag) {
                    $q->where('name', $tag);
                });
            }
        }

        // Lógica de Ordenación (Lógica común)
        if ($request->orden === 'votados') {
            $query->orderByDesc('score_sum');
        } else {
            $query->orderBy('guides.created_at', 'desc');
        }

        return $query;
    }

    public function show($slug)
    {
        $guide = Guide::where('slug', $slug)
            ->with(['tags', 'user', 'votos', 'comments.user', 'comments.respuestas.user'])
            ->firstOrFail();

        return view('seccion.guideShow', compact('guide'));
    }
}