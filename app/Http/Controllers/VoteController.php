<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guide;
use App\Models\GuidesVote;

class VoteController extends Controller
{
    public function votar(Request $request)
    {
        $request->validate([
            'guide_id' => 'required|exists:guides,id',
            'tipo' => 'required|in:1,-1'
        ]);

        $user = auth()->user();

        // Buscar si el usuario ya votó esta guía
        $voto = GuidesVote::where('user_id', $user->id)
                          ->where('guide_id', $request->guide_id)
                          ->first();

        if ($voto) {

            // Si pulsa el mismo voto → toggle (se elimina)
            if ($voto->tipo == $request->tipo) {

                $voto->delete();

                return response()->json([
                    'score' => Guide::find($request->guide_id)->score(),
                    'voto'  => 0, // ← SIN VOTO
                    'estado' => 'removed'
                ]);
            }

            // Si pulsa el contrario → actualizar
            $voto->update(['tipo' => $request->tipo]);

        } else {

            // Crear nuevo voto
            $voto = GuidesVote::create([
                'user_id' => $user->id,
                'guide_id' => $request->guide_id,
                'tipo' => $request->tipo
            ]);
        }

        return response()->json([
            'score' => Guide::find($request->guide_id)->score(),
            'voto'  => $voto->tipo, // ← DEVOLVEMOS EL VOTO ACTUAL
            'estado' => 'updated'
        ]);
    }
}
