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
    public function __construct() {
        $this->middleware('auth');
    }

    public function indexGuides(Request $request) {
        $userId = Auth::id();
        $allTags = Tag::all();
        $activeTags = $request->input('tag', []);
        $orden = $request->input('orden', 'recientes');
        $search = $request->input('search');
        $autor = $request->input('autor');

        $query = SavedGuide::where('user_id', $userId)
            ->with(['guide.user', 'guide.tags', 'guide.votos']);

        if (!empty($activeTags)) {
            foreach ($activeTags as $tagName) {
                $query->whereHas('guide.tags', function($q) use ($tagName) {
                    $q->where('name', $tagName);
                });
            }
        }

        if ($request->filled('search')) {
            $query->whereHas('guide', function($q) use ($search) {
                $q->where('titulo', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->filled('autor')) {
            $query->whereHas('guide.user', function($q) use ($autor) {
                $q->where('name', 'LIKE', '%' . $autor . '%');
            });
        }

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

    public function toggle(Request $request, $type, $id) {
        $userId = Auth::id();
        if ($type === 'guide') {
            $saved = SavedGuide::where('user_id', $userId)->where('guide_id', $id)->first();
            if ($saved) {
                $saved->delete();
                return response()->json(['status' => 'removed']);
            }
            SavedGuide::create(['user_id' => $userId, 'guide_id' => $id]);
            return response()->json(['status' => 'added']);
        }
        return response()->json(['status' => 'error'], 400);
    }
}
