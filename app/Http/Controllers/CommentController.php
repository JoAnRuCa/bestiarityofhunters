<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validación base
        $request->validate([
            'item_id'    => 'required|integer',
            'comentario' => 'required|string|min:3|max:1000',
            'padre'      => 'nullable|integer',
            'type'       => 'required|in:guide,build' // El discriminador
        ]);

        // 2. Mapa de configuración (Tablas separadas)
        $config = [
            'guide' => [
                'model' => \App\Models\GuidesComment::class,
                'fk'    => 'guide_id'
            ],
            'build' => [
                'model' => \App\Models\BuildsComment::class, // Tu futura tabla
                'fk'    => 'build_id'
            ]
        ];

        $setup = $config[$request->type];

        // 3. Crear el comentario
        $setup['model']::create([
            'user_id'           => Auth::id(),
            $setup['fk']        => $request->item_id,
            'comentario'        => $request->comentario,
            'padre'             => $request->padre,
        ]);

        return back()->with('status', 'Comment posted!');
    }
}