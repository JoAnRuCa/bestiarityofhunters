<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Lista todas las etiquetas con el conteo de sus relaciones.
     */
    public function index()
    {
        // Usamos withCount para obtener el total de guías y builds asociadas eficientemente
        $tags = Tag::withCount(['guides', 'builds'])->latest()->get();
        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Guarda una nueva etiqueta respetando el formato de texto del usuario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:tags,name',
        ]);

        Tag::create([
            // trim() elimina espacios al inicio/final para evitar duplicados como " Fire" y "Fire"
            'name' => trim($request->name), 
        ]);

        return redirect()->route('admin.tags.index')
                         ->with('success', 'Nueva categoría de cacería registrada.');
    }

    /**
     * Muestra el formulario para editar.
     */
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * Actualiza la etiqueta sin forzar mayúsculas.
     */
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:tags,name,' . $tag->id,
        ]);

        $tag->update([
            'name' => trim($request->name),
        ]);

        return redirect()->route('admin.tags.index')
                         ->with('success', 'Tag updated successfully.');
    }

    /**
     * Elimina la etiqueta de los archivos.
     */
    public function destroy(Tag $tag)
    {
        // Laravel se encarga de la tabla pivote si definiste 'onDelete(cascade)' en la migración
        $tag->delete();

        return redirect()->route('admin.tags.index')
                         ->with('success', 'Tag deleted successfully.');
    }
}