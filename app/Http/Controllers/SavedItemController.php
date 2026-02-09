<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SavedGuide;
use App\Models\Tag;
use App\Models\Guide;

class SavedItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Listado de guías guardadas con filtros y ordenación funcional.
     */
    public function indexGuides(Request $request)
    {
        $userId = Auth::id();
        $allTags = Tag::all();
        $activeTags = $request->input('tag', []);
        $orden = $request->input('orden', 'recientes');
        $search = $request->input('search');
        $autor = $request->input('autor');

        // Iniciamos la consulta base sobre la tabla de guardados
        $query = SavedGuide::where('saved_guides.user_id', $userId);

        // --- FILTROS (Búsqueda por Título y Autor) ---
        if ($search || $autor) {
            $query->whereHas('guide', function($q) use ($search, $autor) {
                if ($search) {
                    $q->where('titulo', 'LIKE', "%{$search}%");
                }
                if ($autor) {
                    $q->whereHas('user', function($u) use ($autor) {
                        $u->where('name', 'LIKE', "%{$autor}%");
                    });
                }
            });
        }

        // --- FILTRO: Tags ---
        if (!empty($activeTags)) {
            foreach ($activeTags as $tagName) {
                $query->whereHas('guide.tags', function($q) use ($tagName) {
                    $q->where('name', $tagName);
                });
            }
        }

        // --- LÓGICA DE ORDENACIÓN ---
        if ($orden === 'votados') {
            $query->join('guides', 'saved_guides.guide_id', '=', 'guides.id')
                ->leftJoin('guides_votes', 'guides.id', '=', 'guides_votes.guide_id')
                ->select('saved_guides.*') 
                ->addSelect(DB::raw('IFNULL(SUM(guides_votes.tipo), 0) as total_score'))
                ->groupBy(
                    'saved_guides.id', 
                    'saved_guides.user_id', 
                    'saved_guides.guide_id', 
                    'saved_guides.created_at', 
                    'saved_guides.updated_at'
                )
                ->orderBy('total_score', 'desc');
        } else {
            // "Most Recent": Ordenamos por la fecha en que el USUARIO guardó la guía
            $query->select('saved_guides.*')->orderBy('saved_guides.created_at', 'desc');
        }

        // Cargamos relaciones después de filtrar para mejorar rendimiento
        $savedData = $query->with(['guide.user', 'guide.tags', 'guide.votos'])->paginate(10);

        $isTagActive = function ($tagName) use ($activeTags) {
            return in_array($tagName, $activeTags);
        };

        // Soporte para carga AJAX (evita duplicar el layout master)
        if ($request->ajax()) {
            return view('seccion.savedGuides', compact('savedData', 'allTags', 'activeTags', 'isTagActive'))
                ->with('only_content', true);
        }

        return view('seccion.savedGuides', compact('savedData', 'allTags', 'activeTags', 'isTagActive'));
    }

    /**
     * Acción Toggle: Guarda o elimina una guía de los archivos del usuario.
     * Soluciona el error: "Method toggle does not exist".
     */
    public function toggle(Request $request, $type, $id)
    {
        $userId = Auth::id();

        if ($type === 'guide') {
            // Buscamos si ya existe el registro para borrarlo (toggle off)
            $saved = SavedGuide::where('user_id', $userId)
                               ->where('guide_id', $id)
                               ->first();

            if ($saved) {
                $saved->delete();
                return response()->json(['status' => 'removed']);
            }

            // Si no existe, lo creamos (toggle on)
            // IMPORTANTE: Asegúrate de añadir user_id y guide_id al $fillable de tu modelo SavedGuide
            SavedGuide::create([
                'user_id' => $userId,
                'guide_id' => $id
            ]);

            return response()->json(['status' => 'added']);
        }

        return response()->json(['status' => 'error', 'message' => 'Type not supported'], 400);
    }
}