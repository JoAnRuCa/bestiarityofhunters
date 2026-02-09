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
            return view('components.guide-grid', compact('guides'))->render();
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
            // Pasamos editable true para que el grid active los botones de gestión
            return view('components.guide-grid', [
                'guides' => $guides,
                'editable' => true
            ])->render();
        }

        return view('seccion.myGuides', compact('guides'));
    }

    /**
     * Muestra el formulario de edición (Ubicado en seccion/editGuide.blade.php)
     */
    public function edit($id)
    {
        $guide = Guide::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        return view('seccion.editGuide', compact('guide'));
    }

    /**
     * Procesa la actualización de la guía
     */
public function update(Request $request, $id)
{
    $guide = Guide::where('id', $id)
        ->where('user_id', Auth::id())
        ->firstOrFail();
    
    $validated = $request->validate([
        'titulo' => 'required|string|max:255',
        'contenido' => 'required|string',
        'tags' => 'nullable|array', // Validamos que los tags sean un array
    ]);

    // 1. Actualizamos los datos básicos
    $guide->titulo = $validated['titulo'];
    $guide->contenido = $validated['contenido'];
    $guide->slug = Str::slug($validated['titulo']);
    $guide->save();

    // 2. Sincronizamos los tags (ESTO ES LO QUE FALTA)
    // sync() borra los tags viejos y añade los nuevos en la tabla intermedia automáticamente
    if ($request->has('tags')) {
        $guide->tags()->sync($request->tags);
    } else {
        // Si el usuario desmarca todos los tags, vaciamos la relación
        $guide->tags()->detach();
    }

    return redirect()->route('my.guides')->with('success', 'Scroll updated successfully.');
}

    /**
     * Elimina la guía y sus relaciones (AJAX compatible)
     */
    public function destroy($id)
    {
        $guide = Guide::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Limpiamos relaciones para evitar errores de Foreign Key (SQLState 23000)
        $guide->votos()->delete(); 
        
        // Si usas etiquetas con tabla pivote, las desvinculamos
        if (method_exists($guide, 'tags')) {
            $guide->tags()->detach();
        }

        $guide->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Scroll discarded.');
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
     * MÉTODO PRIVADO: Centraliza la lógica de filtrado y ordenación
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