<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guide;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GuideListController extends Controller
{
    /**
     * Lista general de guías (Pública)
     */
    public function index(Request $request)
    {
        $query = $this->applyFiltersAndSorting($request);
        $guides = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            // Activamos 'editable' para que el componente evalúe la autoría en cada card
            return view('components.guide-grid', [
                'guides' => $guides,
                'editable' => true 
            ])->render();
        }

        return view('seccion.guidesList', compact('guides'));
    }

    /**
     * Mis Guías (Librería personal del cazador)
     */
    public function myGuides(Request $request)
    {
        $query = $this->applyFiltersAndSorting($request, Auth::id());
        $guides = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('components.guide-grid', [
                'guides' => $guides,
                'editable' => true
            ])->render();
        }

        return view('seccion.myGuides', compact('guides'));
    }

    /**
     * Muestra el formulario de edición
     */
 // Cambia $id por $slug en los parámetros de estas funciones:

public function edit($slug)
{
    $guide = Guide::where('slug', $slug)->firstOrFail();

    if ($guide->user_id !== Auth::id()) {
        abort(403);
    }
    
    return view('seccion.editGuide', compact('guide'));
}

public function update(Request $request, Guide $guide) // <--- Laravel ya sabe buscar por slug gracias a getRouteKeyName()
{
    if ($guide->user_id !== Auth::id()) {
        abort(403);
    }

    $validated = $request->validate([
        'titulo' => 'required|string|max:255',
        'contenido' => 'required|string',
        'tags' => 'nullable|array',
    ]);

    $guide->update([
        'titulo' => $validated['titulo'],
        'contenido' => $validated['contenido'],
    ]);

    $guide->tags()->sync($request->tags ?? []);

    return redirect()->route('my.guides')->with('success', 'Scroll updated successfully.');
}

public function destroy($slug)
{
    $guide = Guide::where('slug', $slug)->firstOrFail();

    if ($guide->user_id !== Auth::id()) {
        return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
    }

    try {
        // Usamos el ID interno para las consultas de DB manuales
        $internalId = $guide->id;

        \DB::transaction(function () use ($guide, $internalId) {
            \DB::table('saved_guides')->where('guide_id', $internalId)->delete();
            $guide->votos()->delete(); 
            
            foreach ($guide->comments as $comment) {
                if (method_exists($comment, 'respuestas')) {
                    $comment->respuestas()->delete();
                }
                $comment->delete();
            }

            $guide->tags()->detach();
            $guide->delete();
        });

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
    /**
     * Muestra una guía específica mediante su slug
     */
    public function show($slug)
    {
        $guide = Guide::where('slug', $slug)
            ->with(['tags', 'user', 'votos', 'comments.user', 'comments.respuestas.user'])
            ->firstOrFail();

        return view('seccion.guideShow', compact('guide'));
    }

    /**
     * Lógica de filtrado y ordenación
     */
    private function applyFiltersAndSorting(Request $request, $userId = null)
    {
        $query = Guide::with(['tags', 'user', 'votos'])
            ->withSum('votos as score_sum', 'tipo');

        if ($userId) {
            $query->where('guides.user_id', $userId);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('guides.titulo', 'like', '%' . $request->search . '%')
                  ->orWhere('guides.contenido', 'like', '%' . $request->search . '%');
            });
        }

        if (!$userId && $request->filled('autor')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->autor . '%');
            });
        }

        if ($request->filled('tag')) {
            $tags = (array) $request->tag;
            foreach ($tags as $tag) {
                $query->whereHas('tags', function ($q) use ($tag) {
                    $q->where('name', $tag);
                });
            }
        }

        if ($request->orden === 'votados') {
            $query->orderByDesc('score_sum');
        } else {
            $query->orderBy('guides.created_at', 'desc');
        }

        return $query;
    }
}