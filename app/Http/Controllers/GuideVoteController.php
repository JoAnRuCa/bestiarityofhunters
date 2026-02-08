<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guide;
use App\Models\GuidesVote;
use App\Services\VoteService;

class GuideVoteController extends Controller
{
    public function votar(Request $request, VoteService $voteService)
{
    $request->validate([
        'id'    => 'required|integer',
        'tipo'  => 'required|in:1,-1',
        'model' => 'required|in:guide,comment' // Añade aquí más tipos si hace falta
    ]);

    $user = auth()->user();

    // Mapeo de modelos y llaves
    $config = [
        'guide' => [
            'voteModel' => \App\Models\GuidesVote::class,
            'entityModel' => \App\Models\Guide::class,
            'foreignKey' => 'guide_id'
        ],
        'comment' => [
            'voteModel' => \App\Models\GuidesCommentVote::class,
            'entityModel' => \App\Models\GuidesComment::class,
            'foreignKey' => 'comment_id'
        ],
    ];

    $setup = $config[$request->model];

    $resultado = $voteService->procesarVoto(
        $setup['voteModel'],
        $setup['foreignKey'],
        $request->id,
        $user->id,
        $request->tipo,
        function($id) use ($setup) {
            return $setup['entityModel']::find($id)->score();
        }
    );

    return response()->json($resultado);
}
}
