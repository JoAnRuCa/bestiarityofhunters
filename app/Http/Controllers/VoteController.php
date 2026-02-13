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
        // 1. Validación de entrada (añadimos 'build')
        $request->validate([
            'id'    => 'required|integer',
            'tipo'  => 'required|in:1,-1',
            'model' => 'required|in:guide,comment,build' 
        ]);

        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = Auth::id();

            // 2. Configuración dinámica de modelos
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
                'comment' => [ // Comentarios de GUÍAS (GuidesComment)
                    'voteModel'   => \App\Models\GuidesCommentVote::class,
                    'entityModel' => \App\Models\GuidesComment::class,
                    'foreignKey'  => 'comment_id'
                ],
                'build_comment' => [ // Comentarios de BUILDS (BuildComment)
                    'voteModel'   => \App\Models\BuildCommentVote::class,
                    'entityModel' => \App\Models\BuildComment::class,
                    'foreignKey'  => 'comment_id' // En tu modelo BuildCommentVote pusiste comment_id
                ],
];

        $setup = $config[$request->model];
        $voteModel = $setup['voteModel'];
        $foreignKey = $setup['foreignKey'];

        // 3. Lógica de votación
        $voto = $voteModel::where('user_id', $userId)
                          ->where($foreignKey, $request->id)
                          ->first();

        if ($voto) {
            // Si pulsa el mismo voto → eliminar (toggle)
            if ($voto->tipo == $request->tipo) {
                $voto->delete();
                return response()->json([
                    'score'  => $setup['entityModel']::find($request->id)->score(),
                    'voto'   => 0,
                    'estado' => 'removed'
                ]);
            }
            // Si pulsa el contrario → actualizar
            $voto->update(['tipo' => $request->tipo]);
        } else {
            // Crear nuevo voto
            $voto = $voteModel::create([
                'user_id'   => $userId,
                $foreignKey => $request->id,
                'tipo'      => $request->tipo
            ]);
        }

        // 4. Respuesta unificada
        return response()->json([
            'score'  => $setup['entityModel']::find($request->id)->score(),
            'voto'   => $voto->tipo,
            'estado' => 'updated'
        ]);
    }
}