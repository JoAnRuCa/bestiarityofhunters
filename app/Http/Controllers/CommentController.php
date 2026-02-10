<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\View\Components\CommentItem; // Importamos la clase del componente

class CommentController extends Controller
{
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
                'model' => \App\Models\BuildsComment::class,
                'fk'    => 'build_id',
                'parent_model' => \App\Models\Build::class // Asumiendo que tienes un modelo Build
            ]
        ];

        $setup = $config[$request->type];

        // 1. Crear el comentario en la base de datos
        $comment = $setup['model']::create([
            'user_id'    => Auth::id(),
            $setup['fk'] => $request->item_id,
            'comentario' => $request->comentario,
            'padre'      => $request->padre,
        ]);

        // 2. Respuesta para AJAX
        if ($request->ajax()) {
            // Buscamos el objeto padre (la Guía o la Build) para el componente
            $item = $setup['parent_model']::find($request->item_id);
            $level = intval($request->input('level', 0));

            // Instanciamos el componente y lo renderizamos a HTML
            // Pasamos: comentario, objeto padre, string del tipo y nivel
            $component = new CommentItem($comment, $item, $request->type, $level);

            return response()->json([
                'success' => true,
                'comment_html' => $component->render()->with($component->data())->render()
            ]);
        }

        return back()->with('status', 'Comment posted!');
    }
}