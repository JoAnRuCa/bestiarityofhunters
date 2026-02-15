<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Models\Tag;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $guides = Guide::with(['user', 'tags'])
            ->when($search, function ($query, $search) {
                return $query->where('titulo', 'LIKE', "%{$search}%")
                             ->orWhere('contenido', 'LIKE', "%{$search}%");
            })
            ->latest()
            ->paginate(15);

        // Pasamos la búsqueda para que no se pierda al paginar
        return view('admin.guides.index', compact('guides', 'search'));
    }

 public function edit(Guide $guide) // Laravel hará el Route Model Binding por slug si está configurado
{
    $guide->load('tags');
    $previous_url = old('previous_url', url()->previous());
    
    return view('admin.guides.edit', compact('guide', 'previous_url'));
}

public function update(Request $request, $slug) // Recibimos el slug
{
    // Buscamos la guía por slug manualmente para asegurar
    $guide = Guide::where('slug', $slug)->firstOrFail();

    $request->validate([
        // Validamos el título único ignorando el ID de esta guía
        'titulo'    => 'required|string|max:255|unique:guides,titulo,' . $guide->id,
        'contenido' => 'required|string',
        'tags'      => 'nullable|array',
    ]);

    $guide->update([
        'titulo'    => $request->titulo,
        'contenido' => $request->contenido,
    ]);

    $guide->tags()->sync($request->tags ?? []);

    $redirectUrl = $request->input('previous_url', url('/admin/guides'));

    return redirect($redirectUrl)->with('success', 'Guide updated successfully.');
}

    public function store(Request $request)
    {
        $request->validate([
            'titulo'    => 'required|string|max:255|unique:guides,titulo',
            'contenido' => 'required|string',
        ]);

        $guide = Guide::create([
            'titulo'    => $request->titulo,
            'contenido' => $request->contenido,
            'user_id'   => auth()->id(),
        ]);

        if ($request->has('tags')) {
            $guide->tags()->sync($request->tags);
        }

        // Forzamos salida a la lista
        return redirect('/admin/guides')->with('success', 'New guide created successfully.');
    }

    public function destroy(Guide $guide)
    {
        $guide->delete();
        return redirect('/admin/guides')->with('success', 'Guide deleted successfully.');
    }
}