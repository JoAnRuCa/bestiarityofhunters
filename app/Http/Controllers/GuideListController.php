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
    public function edit($id)
    {
        $guide = Guide::findOrFail($id);

        // Comprobación de seguridad: Solo el dueño edita
        if ($guide->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar este contenido.');
        }
        
        return view('seccion.editGuide', compact('guide'));
    }

    /**
     * Procesa la actualización de la guía
     */
    public function update(Request $request, $id)
    {
        $guide = Guide::findOrFail($id);

        // Comprobación de seguridad
        if ($guide->user_id !== Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }
        
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'tags' => 'nullable|array',
        ]);

        // 1. Datos básicos y slug
        $guide->titulo = $validated['titulo'];
        $guide->contenido = $validated['contenido'];
        $guide->slug = Str::slug($validated['titulo']);
        $guide->save();

        // 2. Sincronización de Tags (Relación Many-to-Many)
        if ($request->has('tags')) {
            $guide->tags()->sync($request->tags);
        } else {
            $guide->tags()->detach();
        }

        return redirect()->route('my.guides')->with('success', 'Scroll updated successfully.');
    }

    /**
     * Elimina la guía y sus relaciones
     */
   /**
 * Elimina la guía y sus relaciones
 */
public function destroy($id)
{
    $guide = Guide::findOrFail($id);

    if ($guide->user_id !== Auth::id()) {
        return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
    }

    try {
        // --- LIMPIEZA DE DEPENDENCIAS ---

        // 1. Borrar de la tabla de "Guardados" (Favoritos)
        \DB::table('saved_guides')->where('guide_id', $id)->delete();

        // 2. Borrar Votos
        $guide->votos()->delete(); 

        // 3. Borrar Comentarios y sus Respuestas
        foreach ($guide->comments as $comment) {
            if (method_exists($comment, 'respuestas')) {
                $comment->respuestas()->delete();
            }
            $comment->delete();
        }

        // 4. Limpieza de Tags
        if (method_exists($guide, 'tags')) {
            $guide->tags()->detach();
        }

        // --- BORRADO FINAL ---
        $guide->delete();

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'error' => 'Error de integridad: ' . $e->getMessage()
        ], 500);
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