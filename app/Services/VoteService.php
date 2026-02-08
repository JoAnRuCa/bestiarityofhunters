<?php

namespace App\Services;

class VoteService
{
    public function procesarVoto($voteModel, $foreignKey, $entityId, $userId, $tipo, $scoreCallback)
    {
        // Buscar voto existente
        $voto = $voteModel::where('user_id', $userId)
                          ->where($foreignKey, $entityId)
                          ->first();

        if ($voto) {

            // Si pulsa el mismo voto → eliminar
            if ($voto->tipo == $tipo) {
                $voto->delete();

                return [
                    'score' => $scoreCallback($entityId),
                    'voto'  => 0,
                    'estado' => 'removed'
                ];
            }

            // Cambiar voto
            $voto->update(['tipo' => $tipo]);

        } else {

            // Crear nuevo voto
            $voto = $voteModel::create([
                'user_id' => $userId,
                $foreignKey => $entityId,
                'tipo' => $tipo
            ]);
        }

        return [
            'score' => $scoreCallback($entityId),
            'voto'  => $voto->tipo,
            'estado' => 'updated'
        ];
    }
}
