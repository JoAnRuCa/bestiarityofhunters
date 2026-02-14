<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuildComment;
use Illuminate\Http\Request;

class BuildCommentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $comments = BuildComment::with(['user', 'build'])
            ->when($search, function ($query, $search) {
                return $query->where('comentario', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('build', function ($q) use ($search) {
                        $q->where('titulo', 'LIKE', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(15);

        return view('admin.buildComments.index', compact('comments', 'search'));
    }

    public function edit($id)
    {
        $comment = BuildComment::findOrFail($id);
        return view('admin.buildComments.edit', compact('comment'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'comentario' => 'required|string|max:1000',
        ]);

        $comment = BuildComment::findOrFail($id);
        $comment->update([
            'comentario' => $request->comentario
        ]);

        return redirect()->route('admin.buildComments.index')
            ->with('success', 'The Hunter\'s scroll has been updated.');
    }

public function destroy($id)
{
    $comment = BuildComment::findOrFail($id);

    $comment->update([
        'comentario' => 'This text has been deleted'
    ]);
    
    $comment->votos()->delete();

    return redirect()->route('admin.buildComments.index')
        ->with('success', 'Comment content has been redacted and votes cleared.');
}

public function hardDelete($id)
{
    $comment = BuildComment::findOrFail($id);

    // 1. Borramos los votos del comentario principal
    $comment->votos()->delete();

    // 2. Buscamos y borramos todos los hijos (respuestas)
    // Nota: Si tus hijos pueden tener más hijos, esto borrará el primer nivel.
    // Si quieres borrar toda la cadena, lo ideal es un loop o cascade en DB.
    $respuestas = BuildComment::where('padre', $comment->id)->get();
    
    foreach ($respuestas as $respuesta) {
        $respuesta->votos()->delete();
        $respuesta->delete();
    }

    // 3. Borramos el comentario principal
    $comment->delete();

    return redirect()->route('admin.buildComments.index')
        ->with('success', 'Comment and all its echoes have been purged from the archives.');
}
}
