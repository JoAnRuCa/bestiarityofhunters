<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

        // Iniciamos la consulta sobre los registros guardados
        $query = SavedGuide::where('user_id', $userId)
            ->with(['guide.user', 'guide.tags']);

        // Filtro: Búsqueda por texto en el título de la guía
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('guide', function($q) use ($search) {
                $q->where('titulo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro: Tags (la guía debe tener los tags seleccionados)
        if (!empty($activeTags)) {
            foreach ($activeTags as $tagName) {
                $query->whereHas('guide.tags', function($q) use ($tagName) {
                    $q->where('name', $tagName);
                });
            }
        }

        $savedData = $query->latest()->paginate(10);

        // Helper para que el componente filter-panel sepa qué botones iluminar
        $isTagActive = function ($tagName) use ($activeTags) {
            return in_array($tagName, $activeTags);
        };

        return view('seccion.savedGuides', compact('savedData', 'allTags', 'activeTags', 'isTagActive'));
    }

    public function toggle($type, $id)
    {
        try {
            $userId = Auth::id();
            $table = ($type === 'guide') ? 'saved_guides' : 'saved_builds';
            $foreignKey = ($type === 'guide') ? 'guide_id' : 'build_id';

            $query = DB::table($table)->where('user_id', $userId)->where($foreignKey, $id);

            if ($query->exists()) {
                $query->delete();
                return response()->json(['status' => 'removed']);
            }

            DB::table($table)->insert([
                'user_id' => $userId,
                $foreignKey => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['status' => 'added']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}