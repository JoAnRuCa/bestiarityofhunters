<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guide;

class GuideListController extends Controller
{
    public function index(Request $request)
    {
        // Cargar votos, tags y usuario SIEMPRE
        $query = Guide::with(['tags', 'user', 'votos'])
            ->withSum('votos as score_sum', 'tipo'); // suma de votos

        // Buscador general (título + contenido)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->search . '%')
                  ->orWhere('contenido', 'like', '%' . $request->search . '%');
            });
        }

        // Buscar por autor
        if ($request->filled('autor')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->autor . '%');
            });
        }

        // Buscar por múltiples tags (AND)
        if ($request->filled('tag')) {
            $tags = (array) $request->tag;

            foreach ($tags as $tag) {
                $query->whereHas('tags', function ($q) use ($tag) {
                    $q->where('name', $tag);
                });
            }
        }

        // Ordenar
        if ($request->orden === 'votados') {
            $query->orderByDesc('score_sum');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $guides = $query->paginate(10)->withQueryString();

        return view('seccion.guidesList', compact('guides'));
    }

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
}
