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
        $tags = Tag::withCount(['guides', 'builds'])->latest()->get();
        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Guarda una nueva etiqueta.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:tags,name',
        ]);

        $tag = Tag::create([
            'name' => trim($request->name), 
        ]);

        return redirect()->route('admin.tags.index')
                         ->with('success', "The tag «{$tag->name}» has been registered.");
    }

    /**
     * Muestra el formulario para editar.
     */
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * Actualiza la etiqueta.
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
                         ->with('success', "The tag «{$tag->name}» has been updated.");
    }

    /**
     * Elimina la etiqueta.
     */
    public function destroy(Tag $tag)
    {
        $tagName = $tag->name; // Guardamos el nombre antes de borrar para el mensaje
        
        try {
            $tag->delete();
            return redirect()->route('admin.tags.index')
                             ->with('success', "The tag «{$tagName}» has been deleted from the files.");
        } catch (\Exception $e) {
            return redirect()->route('admin.tags.index')
                             ->with('error', "The tag «{$tagName}» could not be deleted: " . $e->getMessage());
        }
    }
}