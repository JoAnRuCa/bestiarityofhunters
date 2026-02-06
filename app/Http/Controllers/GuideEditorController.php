<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuideEditorController extends Controller
{
    /**
     * Show the guide editor or guest message.
     */
    public function index()
    {
        // Si NO está logueado → mostrar vista guest
        if (!Auth::check()) {
            return view('layouts.partials.guest');
        }

        // Si está logueado → cargar editor
        return view('seccion.guideEditor', [
            'guide' => null,
            'tags'  => Tag::all(),
            'user'  => Auth::user(),
        ]);
    }

    /**
     * Store a new guide.
     */
    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'titulo'    => 'required|string|max:255',
            'contenido' => 'required|string',
            'tags'      => 'array',
        ]);

        // Crear guía
        $guide = Guide::create([
            'titulo'    => $request->titulo,
            'contenido' => $request->contenido,
            'user_id'   => Auth::id(),
        ]);

        // Sincronizar tags
        if ($request->has('tags')) {
            $guide->tags()->sync($request->tags);
        }

        return redirect()
            ->route('guide.editor')
            ->with('success', 'Guide created successfully.');
    }
}
