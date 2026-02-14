<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Models\Tag; // Importante para las etiquetas
use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Cargamos relaciones para evitar múltiples consultas a la BD
        $guides = Guide::with(['user', 'tags'])
            ->when($search, function ($query, $search) {
                return $query->where('titulo', 'LIKE', "%{$search}%")
                             ->orWhere('contenido', 'LIKE', "%{$search}%");
            })
            ->latest() // Las más nuevas primero
            ->get();

        return view('admin.guides.index', compact('guides', 'search'));
    }

    public function create()
    {
        $tags = Tag::all(); // Necesitamos las etiquetas para el formulario
        return view('admin.guides.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'    => 'required|string|max:255|unique:guides,titulo',
            'contenido' => 'required|string',
            'tags'      => 'nullable|array', // Validación de etiquetas
        ]);

        $guide = Guide::create([
            'titulo'    => $request->titulo,
            'contenido' => $request->contenido,
            'user_id'   => auth()->id(),
            // El slug se genera solo gracias a tu modelo
        ]);

        if ($request->has('tags')) {
            $guide->tags()->sync($request->tags);
        }

        return redirect()->route('admin.guides.index')
                         ->with('success', 'Guía publicada en los archivos del gremio.');
    }

    public function edit(Guide $guide)
    {
        $tags = Tag::all();
        return view('admin.guides.edit', compact('guide', 'tags'));
    }

    public function update(Request $request, Guide $guide)
    {
        $request->validate([
            'titulo'    => 'required|string|max:255|unique:guides,titulo,' . $guide->id,
            'contenido' => 'required|string',
            'tags'      => 'nullable|array',
        ]);

        $guide->update([
            'titulo'    => $request->titulo,
            'contenido' => $request->contenido,
        ]);

        // Sincronizar etiquetas (borra las viejas y pone las nuevas)
        $guide->tags()->sync($request->tags ?? []);

        return redirect()->route('admin.guides.index')
                         ->with('success', 'Los archivos han sido actualizados.');
    }

    public function destroy(Guide $guide)
    {
        $guide->delete();
        return redirect()->route('admin.guides.index')
                         ->with('success', 'Guía eliminada permanentemente.');
    }
}