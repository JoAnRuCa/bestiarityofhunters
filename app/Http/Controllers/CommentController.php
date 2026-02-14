<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\View\Components\CommentItem; 

class CommentController extends Controller
{
    /**
     * Almacena un nuevo comentario o respuesta.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id'    => 'required|integer',
            'comentario' => 'required|string|min:1|max:1000',
            'padre'      => 'nullable|integer',
            'type'       => 'required|in:guide,build'
        ]);

        $config = [
            'guide' => [
                'model' => \App\Models\GuidesComment::class,
                'fk'    => 'guide_id',
                'parent_model' => \App\Models\Guide::class
            ],
            'build' => [
                'model' => \App\Models\BuildComment::class,
                'fk'    => 'build_id',
                'parent_model' => \App\Models\Build::class 
            ]
        ];

        $setup = $config[$request->type];

        // 1. Crear el comentario
        $comment = $setup['model']::create([
            'user_id'    => Auth::id(),
            $setup['fk'] => $request->item_id,
            'comentario' => $request->comentario,
            'padre'      => $request->padre,
        ]);

        // 2. Respuesta para AJAX (renderiza el componente al vuelo)
        if ($request->ajax()) {
            $item = $setup['parent_model']::find($request->item_id);
            $level = intval($request->input('level', 0));

            // Instanciamos el componente x-comment-item
            $component = new CommentItem($comment, $item, $request->type, $level);

            return response()->json([
                'success' => true,
                'comment_html' => $component->render()->with($component->data())->render()
            ]);
        }

        return back()->with('status', 'Comment posted!');
    }

    /**
     * Actualiza un comentario existente (Dueño o Admin).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'comentario' => 'required|string',
            'type'       => 'required|in:guide,build'
        ]);
        
        $modelClass = $request->type === 'build' ? \App\Models\BuildComment::class : \App\Models\GuidesComment::class;
        $comment = $modelClass::findOrFail($id);

        // PERMISOS: Solo el dueño del comentario o un administrador pueden editar
        if (auth()->id() !== $comment->user_id && auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $comment->update(['comentario' => $request->comentario]);

        return response()->json([
            'success' => true,
            'new_text' => $comment->comentario
        ]);
    }

    /**
     * Realiza un borrado lógico (Soft Delete) del contenido (Dueño o Admin).
     */
    public function softDelete(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:guide,build'
        ]);

        $modelClass = $request->type === 'build' ? \App\Models\BuildComment::class : \App\Models\GuidesComment::class;
        $comment = $modelClass::findOrFail($id);

        // PERMISOS: Solo el dueño del comentario o un administrador pueden borrar
        if (auth()->id() !== $comment->user_id && auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Sustituimos el texto por el aviso de borrado
        $comment->update(['comentario' => 'This text has been deleted']);
        
        // Opcional: Eliminar los votos asociados para limpiar el ranking
        if (method_exists($comment, 'votos')) {
            $comment->votos()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }
}