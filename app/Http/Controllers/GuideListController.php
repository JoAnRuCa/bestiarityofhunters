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
        $query = Guide::with(['tags', 'user', 'votos'])
            ->withSum('votos as score_sum', 'tipo');

        // Filtro por Título o Contenido
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->search . '%')
                  ->orWhere('contenido', 'like', '%' . $request->search . '%');
            });
        }

        // Filtro por Autor
        if ($request->filled('autor')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->autor . '%');
            });
        }

        // Filtro por Tags
        if ($request->filled('tag')) {
            $tags = (array) $request->tag;
            foreach ($tags as $tag) {
                $query->whereHas('tags', function ($q) use ($tag) {
                    $q->where('name', $tag);
                });
            }
        }

        // Ordenación
        if ($request->orden === 'votados') {
            $query->orderByDesc('score_sum');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $guides = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('components.guide-grid', compact('guides'))->render();
        }

        return view('seccion.guidesList', compact('guides'));
    }

    /**
     * Detalle de una guía específica
     */
    public function show($slug)
    {
        $guide = Guide::where('slug', $slug)
            ->with([
                'tags', 
                'user', 
                'votos', 
                'comments.user', 
                'comments.respuestas.user'
            ])
            ->firstOrFail();

        return view('seccion.guideShow', compact('guide'));
    }

    /**
     * Mis Guías (Librería personal del usuario logeado)
     */
    public function myGuides(Request $request)
    {
        $userId = Auth::id();
        $orden = $request->input('orden', 'recientes');

        // Usamos withSum para evitar los errores de Join y Group By
        $query = Guide::where('guides.user_id', $userId)
            ->with(['user', 'tags', 'votos'])
            ->withSum('votos as score_sum', 'tipo');

        // Filtro de Búsqueda (solo en mis guías)
        if ($request->filled('search')) {
            $query->where('guides.titulo', 'LIKE', "%{$request->search}%");
        }

        // Filtros de Tags
        if ($request->filled('tag')) {
            $tags = (array) $request->tag;
            foreach ($tags as $tagName) {
                $query->whereHas('tags', function($q) use ($tagName) {
                    $q->where('name', $tagName);
                });
            }
        }

        // Ordenación corregida sin ambigüedad
        if ($orden === 'votados') {
            $query->orderByDesc('score_sum');
        } else {
            $query->orderBy('guides.created_at', 'desc');
        }

        $guides = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('components.guide-grid', compact('guides'))->render();
        }

        return view('seccion.myGuides', compact('guides'));
    }
}