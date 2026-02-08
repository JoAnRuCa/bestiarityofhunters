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
            'guide_id' => 'required|exists:guides,id',
            'tipo' => 'required|in:1,-1'
        ]);

        $user = auth()->user();

        // Llamada compatible con PHP 7.x
        $resultado = $voteService->procesarVoto(
            GuidesVote::class,
            'guide_id',
            $request->guide_id,
            $user->id,
            $request->tipo,
            function($id) {
                return Guide::find($id)->score();
            }
        );

        return response()->json($resultado);
    }
}
