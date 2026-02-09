<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SavedGuide;
use App\Models\Tag;
use App\Models\Guide; // Asegúrate de importar el modelo Guide

class SavedItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Listado de guías guardadas con filtros y AJAX
     */
    public function indexGuides(Request $request)
    {
        $userId = Auth::id();
        $allTags = Tag::all();
        $activeTags = $request->input('tag', []);
        $orden = $request->input('orden', 'recientes');
        $search = $request->input('search');
        $autor = $request->input('autor'); // Capturamos el nuevo campo

        $query = SavedGuide::where('user_id', $userId)
            ->with(['guide.user', 'guide.tags', 'guide.votos']);

        // Filtro por Tags
        if (!empty($activeTags)) {
            foreach ($activeTags as $tagName) {
                $query->whereHas('guide.tags', function($q) use ($tagName) {
                    $q->where('name', $tagName);
                });
            }
        }

        // Filtro por Búsqueda (Título)
        if ($request->filled('search')) {
            $query->whereHas('guide', function($q) use ($search) {
                $q->where('titulo', 'LIKE', '%' . $search . '%');
            });
        }

        // --- NUEVO: Filtro por Autor ---
        if ($request->filled('autor')) {
            $query->whereHas('guide.user', function($q) use ($autor) {
                $q->where('name', 'LIKE', '%' . $autor . '%');
            });
        }

        // Ordenación
        if ($orden === 'votados') {
            $query->whereHas('guide', function($q) {
                $q->withCount(['votos as total_score' => function($sq) {
                    $sq->select(DB::raw('sum(tipo)'));
                }])->orderBy('total_score', 'desc');
            });
        } else {
            $query->latest();
        }

        $savedData = $query->paginate(10);

        $isTagActive = function ($tagName) use ($activeTags) {
            return in_array($tagName, $activeTags);
        };

        // Respuesta para AJAX y carga normal (Compatible Laravel 8)
        if ($request->ajax()) {
            return view('seccion.savedGuides', compact('savedData', 'allTags', 'activeTags', 'isTagActive'))
                ->with('only_content', true);
        }

        return view('seccion.savedGuides', compact('savedData', 'allTags', 'activeTags', 'isTagActive'));
    }

    /**
     * MÉTODO TOGGLE: Para guardar/eliminar guías
     * Soluciona el error: Method toggle does not exist
     */
    public function toggle(Request $request, $type, $id)
    {
        $userId = Auth::id();

        if ($type === 'guide') {
            // Buscamos si ya está guardada
            $saved = SavedGuide::where('user_id', $userId)
                               ->where('guide_id', $id)
                               ->first();

            if ($saved) {
                $saved->delete();
                return response()->json(['status' => 'removed']);
            }

            // Si no existe, la creamos
            SavedGuide::create([
                'user_id' => $userId,
                'guide_id' => $id
            ]);

            return response()->json(['status' => 'added']);
        }

        // Para futuros SavedBuilds añadirías el 'else if' aquí
        return response()->json(['status' => 'error', 'message' => 'Type not supported'], 400);
    }
}