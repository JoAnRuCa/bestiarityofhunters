<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Models\Tag;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    /**
     * Lista de guías con búsqueda y relaciones.
     */
public function index(Request $request)
{
    $search = $request->input('search');

    $guides = Guide::with(['user', 'tags'])
        ->when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('titulo', 'LIKE', "%{$search}%")
                  ->orWhere('contenido', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        })
        ->latest()
        ->get();

    return view('admin.guides.index', compact('guides', 'search'));
}

    /**
     * Formulario de creación.
     */
    public function create()
    {
        // No pasamos tags aquí si vas a usar el componente <x-tag-selector /> 
        // ya que el componente mismo carga los Tags de la base de datos.
        return view('admin.guides.create');
    }

    /**
     * Guardar nueva guía.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo'    => 'required|string|max:255|unique:guides,titulo',
            'contenido' => 'required|string',
            'tags'      => 'nullable|array',
        ]);

        $guide = Guide::create([
            'titulo'    => $request->titulo,
            'contenido' => $request->contenido,
            'user_id'   => auth()->id(),
        ]);

        // Sincronizamos los tags enviados por el componente
        if ($request->has('tags')) {
            $guide->tags()->sync($request->tags);
        }

        return redirect()->route('admin.guides.index')
                         ->with('success', 'Guía publicada en los archivos del gremio.');
    }

    /**
     * Formulario de edición.
     */
    public function edit(Guide $guide)
    {
        // Cargamos la relación para que el componente sepa qué tags están marcados
        $guide->load('tags');
        
        return view('admin.guides.edit', compact('guide'));
    }

    /**
     * Actualizar guía existente.
     */
    public function update(Request $request, Guide $guide)
    {
        $request->validate([
            'titulo'    => 'required|string|max:255|unique:guides,titulo,' . $guide->id,
            'contenido' => 'required|string',
            'tags'      => 'nullable|array',
        ]);

        // Al usar update(), si el 'titulo' cambia, el boot() de tu modelo 
        // generará el nuevo slug automáticamente.
        $guide->update([
            'titulo'    => $request->titulo,
            'contenido' => $request->contenido,
        ]);

        // Sincronizar etiquetas (si viene vacío, se limpian las etiquetas de la guía)
        $guide->tags()->sync($request->tags ?? []);

        return redirect()->route('admin.guides.index')
                         ->with('success', 'Los archivos han sido actualizados.');
    }

    /**
     * Eliminar guía.
     */
    public function destroy(Guide $guide)
    {
        $guide->delete();
        
        return redirect()->route('admin.guides.index')
                         ->with('success', 'Guía eliminada permanentemente.');
    }
}