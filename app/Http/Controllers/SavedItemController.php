<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SavedGuide;
use App\Models\Tag;

class SavedItemController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function indexGuides(Request $request)
    {
        $userId = Auth::id();
        $allTags = Tag::all();
        $activeTags = $request->input('tag', []);
        $orden = $request->input('orden', 'recientes');
        $search = $request->input('search');
        $autor = $request->input('autor');

        // 1. Consulta base sobre saved_guides
        $query = SavedGuide::where('saved_guides.user_id', $userId);

        // 2. Filtros (Búsqueda y Autor)
        if ($search || $autor) {
            $query->whereHas('guide', function($q) use ($search, $autor) {
                if ($search) $q->where('titulo', 'LIKE', "%{$search}%");
                if ($autor) {
                    $q->whereHas('user', function($u) use ($autor) {
                        $u->where('name', 'LIKE', "%{$autor}%");
                    });
                }
            });
        }

        // 3. Filtro por Tags
        if (!empty($activeTags)) {
            foreach ($activeTags as $tagName) {
                $query->whereHas('guide.tags', function($q) use ($tagName) {
                    $q->where('name', $tagName);
                });
            }
        }

        // 4. Lógica de Ordenación Dual (Votados o Recientes de la Guía)
        if ($orden === 'votados') {
            $query->join('guides', 'saved_guides.guide_id', '=', 'guides.id')
                ->leftJoin('guides_votes', 'guides.id', '=', 'guides_votes.guide_id')
                ->select('saved_guides.*')
                ->addSelect(DB::raw('IFNULL(SUM(guides_votes.tipo), 0) as total_score'))
                ->groupBy('saved_guides.id', 'saved_guides.user_id', 'saved_guides.guide_id', 'saved_guides.created_at', 'saved_guides.updated_at')
                ->orderBy('total_score', 'desc');
        } else {
            // "Most Recent": Ordenamos por la fecha de creación de la GUÍA original
            // Hacemos el join para poder acceder a guides.created_at
            $query->join('guides', 'saved_guides.guide_id', '=', 'guides.id')
                  ->select('saved_guides.*')
                  ->orderBy('guides.created_at', 'desc');
        }

        $savedData = $query->with(['guide.user', 'guide.tags', 'guide.votos'])->paginate(10);

        $isTagActive = function ($tagName) use ($activeTags) {
            return in_array($tagName, $activeTags);
        };

        if ($request->ajax()) {
            return view('seccion.savedGuides', compact('savedData', 'allTags', 'activeTags', 'isTagActive'))
                ->with('only_content', true);
        }

        return view('seccion.savedGuides', compact('savedData', 'allTags', 'activeTags', 'isTagActive'));
    }

    // El método toggle se mantiene igual...
    public function toggle(Request $request, $type, $id) { /* ... código anterior ... */ }
}