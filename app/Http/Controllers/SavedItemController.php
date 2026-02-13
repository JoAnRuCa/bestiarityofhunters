<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SavedGuide;
use App\Models\SavedBuild; 
use App\Models\Tag;
use App\Models\Guide;
use App\Models\Build;

class SavedItemController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Muestra la lista de guías guardadas por el usuario.
     */
    public function indexGuides(Request $request)
    {
        $userId = Auth::id();
        $allTags = Tag::all();
        $activeTags = $request->input('tag', []);
        $orden = $request->input('orden', 'recientes');
        $search = $request->input('search');
        $autor = $request->input('autor');

        $query = SavedGuide::where('saved_guides.user_id', $userId);

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

        if (!empty($activeTags)) {
            foreach ($activeTags as $tagName) {
                $query->whereHas('guide.tags', function($q) use ($tagName) {
                    $q->where('name', $tagName);
                });
            }
        }

        if ($orden === 'votados') {
            $query->join('guides', 'saved_guides.guide_id', '=', 'guides.id')
                ->leftJoin('guides_votes', 'guides.id', '=', 'guides_votes.guide_id')
                ->select('saved_guides.*')
                ->addSelect(DB::raw('IFNULL(SUM(guides_votes.tipo), 0) as total_score'))
                ->groupBy('saved_guides.id', 'saved_guides.user_id', 'saved_guides.guide_id', 'saved_guides.created_at', 'saved_guides.updated_at')
                ->orderBy('total_score', 'desc');
        } else {
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

    /**
     * Muestra la lista de builds guardadas por el usuario.
     */
    public function indexBuilds(Request $request)
    {
        $userId = Auth::id();
        $allTags = Tag::all();
        $activeTags = $request->input('tag', []);
        $orden = $request->input('orden', 'recientes');
        $search = $request->input('search');
        $autor = $request->input('autor');

        // 1. Consulta base sobre la tabla pivot de guardados
        $query = SavedBuild::where('saved_builds.user_id', $userId);

        // 2. Filtros (Búsqueda por título y Autor)
        if ($search || $autor) {
            $query->whereHas('build', function($q) use ($search, $autor) {
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
                $query->whereHas('build.tags', function($q) use ($tagName) {
                    $q->where('name', $tagName);
                });
            }
        }

        // 4. Lógica de Ordenación (Votados vs Recientes)
        if ($orden === 'votados') {
            $query->join('builds', 'saved_builds.build_id', '=', 'builds.id')
                ->leftJoin('builds_votes', 'builds.id', '=', 'builds_votes.build_id')
                ->select('saved_builds.*')
                ->addSelect(DB::raw('IFNULL(SUM(builds_votes.tipo), 0) as total_score'))
                ->groupBy('saved_builds.id', 'saved_builds.user_id', 'saved_builds.build_id', 'saved_builds.created_at', 'saved_builds.updated_at')
                ->orderBy('total_score', 'desc');
        } else {
            $query->join('builds', 'saved_builds.build_id', '=', 'builds.id')
                  ->select('saved_builds.*')
                  ->orderBy('builds.created_at', 'desc');
        }

        $savedData = $query->with(['build.user', 'build.tags', 'build.votos'])->paginate(10);

        $isTagActive = function ($tagName) use ($activeTags) {
            return in_array($tagName, $activeTags);
        };

        if ($request->ajax()) {
            return view('seccion.savedBuilds', compact('savedData', 'allTags', 'activeTags', 'isTagActive'))
                ->with('only_content', true);
        }

        return view('seccion.savedBuilds', compact('savedData', 'allTags', 'activeTags', 'isTagActive'));
    }

    /**
     * Alterna (Toggle) el estado de guardado para Guías y Builds.
     */
    public function toggle(Request $request, $type, $id)
    {
        $userId = Auth::id();

        // --- CASO GUÍAS ---
        if ($type === 'guide') {
            $saved = SavedGuide::where('user_id', $userId)
                               ->where('guide_id', $id)
                               ->first();

            if ($saved) {
                $saved->delete();
                return response()->json(['status' => 'removed']);
            }

            SavedGuide::create([
                'user_id' => $userId,
                'guide_id' => $id
            ]);

            return response()->json(['status' => 'added']);
        }

        // --- CASO BUILDS ---
        if ($type === 'build') {
            $saved = SavedBuild::where('user_id', $userId)
                               ->where('build_id', $id)
                               ->first();

            if ($saved) {
                $saved->delete();
                return response()->json(['status' => 'removed']);
            }

            SavedBuild::create([
                'user_id' => $userId,
                'build_id' => $id
            ]);

            return response()->json(['status' => 'added']);
        }

        return response()->json([
            'status' => 'error', 
            'message' => 'Invalid item type: ' . $type
        ], 400);
    }
}