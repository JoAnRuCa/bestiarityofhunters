<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guide;
use App\Http\Requests\StoreGuideRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class GuideListController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->applyFiltersAndSorting($request);
        $guides = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('components.guide-grid', [
                'guides' => $guides,
                'editable' => true 
            ])->render();
        }

        return view('seccion.guidesList', compact('guides'));
    }

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

    public function edit($slug)
    {
        $guide = Guide::where('slug', $slug)->firstOrFail();

        $this->authorize('update', $guide);
        
        // Intentamos guardar la URL de la lista
        $previousUrl = old('previous_url', url()->previous());

        return view('seccion.editGuide', [
            'guide' => $guide,
            'previous_url' => $previousUrl
        ]);
    }

    public function update(StoreGuideRequest $request, $slug)
    {
        $guide = Guide::where('slug', $slug)->firstOrFail();

        $this->authorize('update', $guide);

        $validated = $request->validated();

        $guide->update([
            'titulo' => $validated['titulo'],
            'contenido' => $validated['contenido'],
        ]);

        $guide->tags()->sync($request->tags ?? []);

        // --- LÓGICA DE REDIRECCIÓN FORZADA ---
        
        // 1. Si eres ADMIN, te mandamos DIRECTAMENTE a la lista de guías de admin.
        if (Auth::user()->role === 'admin') {
            // Usamos la ruta por nombre. Asegúrate que en web.php se llame 'admin.guides.index'
            return redirect()->route('admin.guides.index')->with('success', 'Guide updated.');
        }

        // 2. Si no eres admin, pero el formulario tiene una URL previa válida, la usamos
        if ($request->filled('previous_url') && !str_contains($request->previous_url, '/edit')) {
            return redirect($request->previous_url)->with('success', 'Guide updated.');
        }

        // 3. Fallback: tu lista personal
        return redirect()->route('my.guides')->with('success', 'Guide updated.');
    }

    public function destroy($slug)
    {
        $guide = Guide::where('slug', $slug)->firstOrFail();

        $this->authorize('delete', $guide);

        try {
            $internalId = $guide->id;
            DB::transaction(function () use ($guide, $internalId) {
                DB::table('saved_guides')->where('guide_id', $internalId)->delete();
                $guide->votos()->delete(); 
                foreach ($guide->comments as $comment) {
                    if (method_exists($comment, 'respuestas')) { $comment->respuestas()->delete(); }
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

    public function show($slug)
    {
        $guide = Guide::where('slug', $slug)
            ->with(['tags', 'user', 'votos', 'comments.user', 'comments.respuestas.user'])
            ->firstOrFail();

        return view('seccion.guideShow', compact('guide'));
    }

    private function applyFiltersAndSorting(Request $request, $userId = null)
    {
        $query = Guide::with(['tags', 'user', 'votos'])->withSum('votos as score_sum', 'tipo');
        if ($userId) { $query->where('guides.user_id', $userId); }
        
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
                $query->whereHas('tags', function ($q) use ($tag) { $q->where('name', $tag); });
            }
        }
        
        $request->orden === 'votados' ? $query->orderByDesc('score_sum') : $query->orderBy('guides.created_at', 'desc');
        return $query;
    }
}