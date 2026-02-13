<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    /**
     * Método universal para procesar votos de guías, comentarios y builds.
     */
public function votar(Request $request)
{
    // 1. ACTUALIZAR VALIDACIÓN
    $request->validate([
        'id'    => 'required|integer',
        'tipo'  => 'required|in:1,-1',
        'model' => 'required|in:guide,build,comment,build_comment' // <--- AGREGAR build_comment AQUÍ
    ]);

    if (!auth()->check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // 2. CONFIGURACIÓN DINÁMICA
    $config = [
        'guide' => [
            'voteModel'   => \App\Models\GuidesVote::class,
            'entityModel' => \App\Models\Guide::class,
            'foreignKey'  => 'guide_id'
        ],
        'build' => [
            'voteModel'   => \App\Models\BuildVote::class,
            'entityModel' => \App\Models\Build::class,
            'foreignKey'  => 'build_id'
        ],
        'comment' => [
            'voteModel'   => \App\Models\GuidesCommentVote::class,
            'entityModel' => \App\Models\GuidesComment::class,
            'foreignKey'  => 'comment_id'
        ],
        'build_comment' => [ 
            'voteModel'   => \App\Models\BuildCommentVote::class,
            'entityModel' => \App\Models\BuildComment::class,
            'foreignKey'  => 'comment_id'
        ],
    ];

    $setup = $config[$request->model];
    $voteModel = $setup['voteModel'];
    $foreignKey = $setup['foreignKey'];

    // Lógica de guardado...
    $voto = $voteModel::where('user_id', auth()->id())
                      ->where($foreignKey, $request->id)
                      ->first();

    if ($voto) {
        if ($voto->tipo == $request->tipo) {
            $voto->delete();
            $voto = null;
        } else {
            $voto->update(['tipo' => $request->tipo]);
        }
    } else {
        $voto = $voteModel::create([
            'user_id'   => auth()->id(),
            $foreignKey => $request->id,
            'tipo'      => $request->tipo
        ]);
    }

    $entity = $setup['entityModel']::find($request->id);

    return response()->json([
        'score' => $entity ? $entity->score() : 0,
        'voto'  => $voto ? (int)$voto->tipo : 0
    ]);
}
}