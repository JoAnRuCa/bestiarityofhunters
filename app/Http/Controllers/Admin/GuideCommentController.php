<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuidesComment;
use Illuminate\Http\Request;

class GuideCommentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $comments = GuidesComment::with(['user', 'guide'])
            ->when($search, function ($query, $search) {
                return $query->where('comentario', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('guide', function ($q) use ($search) {
                        $q->where('titulo', 'LIKE', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(15);

        return view('admin.guideComments.index', compact('comments', 'search'));
    }

    public function edit($id)
    {
        $comment = GuidesComment::findOrFail($id);
        return view('admin.guideComments.edit', compact('comment'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'comentario' => 'required|string|max:1000',
        ]);

        $comment = GuidesComment::findOrFail($id);
        $comment->update([
            'comentario' => $request->comentario
        ]);

        return redirect()->route('admin.guideComments.index')
            ->with('success', 'The Hunter\'s scroll has been updated.');
    }

public function destroy($id)
{
    $comment = GuidesComment::findOrFail($id);

    $comment->update([
        'comentario' => 'This text has been deleted'
    ]);
    
    $comment->votos()->delete();

    return redirect()->route('admin.guideComments.index')
        ->with('success', 'Comment content has been redacted and votes cleared.');
}

public function hardDelete($id)
{
    $comment = GuidesComment::findOrFail($id);

    // 1. Borramos los votos del comentario principal
    $comment->votos()->delete();

    // 2. Buscamos y borramos todos los hijos (respuestas)
    // Nota: Si tus hijos pueden tener más hijos, esto borrará el primer nivel.
    // Si quieres borrar toda la cadena, lo ideal es un loop o cascade en DB.
    $respuestas = GuidesComment::where('padre', $comment->id)->get();
    
    foreach ($respuestas as $respuesta) {
        $respuesta->votos()->delete();
        $respuesta->delete();
    }

    // 3. Borramos el comentario principal
    $comment->delete();

    return redirect()->route('admin.guideComments.index')
        ->with('success', 'Comment and all its echoes have been purged from the archives.');
}
}