<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuideEditorController extends Controller
{
    public function index()
    {
        $tags = Tag::all();

        return view('seccion.guideEditor', [
            'guide' => null,
            'tags'  => $tags,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'    => 'required|string|max:255',
            'contenido' => 'required|string',
            'tags'      => 'array',
        ]);

        $guide = Guide::create([
            'titulo'    => $request->titulo,
            'contenido' => $request->contenido,
            'user_id'   => Auth::id(),
        ]);

        if ($request->has('tags')) {
            $guide->tags()->sync($request->tags);
        }

        return redirect()->route('guide.editor')
            ->with('success', 'Guide created successfully.');
    }
}
