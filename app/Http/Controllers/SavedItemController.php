<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SavedGuide;
use App\Models\Tag;

class SavedItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexGuides(Request $request)
{
    $userId = Auth::id();
    $allTags = Tag::all();
    $activeTags = $request->input('tag', []);
    $orden = $request->input('orden', 'recientes');
    $search = $request->input('search');
    $autor = $request->input('autor'); // <--- Nuevo filtro

    $query = SavedGuide::where('user_id', $userId)
        ->with(['guide.user', 'guide.tags', 'guide.votos']);

    // Filtro por Tags (igual que antes)
    if (!empty($activeTags)) {
        foreach ($activeTags as $tagName) {
            $query->whereHas('guide.tags', function($q) use ($tagName) {
                $q->where('name', $tagName);
            });
        }
    }

    // Filtro por Búsqueda de Título
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

    // Ordenación y Paginación
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
    $isTagActive = function ($tagName) use ($activeTags) { return in_array($tagName, $activeTags); };

    if ($request->ajax()) {
        return view('seccion.savedGuides', compact('savedData', 'allTags', 'activeTags', 'isTagActive'))
               ->with('only_content', true);
    }

    return view('seccion.savedGuides', compact('savedData', 'allTags', 'activeTags', 'isTagActive'));
}
}
