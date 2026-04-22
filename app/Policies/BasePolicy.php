<?php

namespace App\Policies;

use App\Models\User;

class BasePolicy
{
    /**
     * El Administrador siempre tiene permiso para todo.
     * Si este método devuelve true, se autoriza la acción inmediatamente.
     */
    public function before(User $user, $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    /**
     * Comprueba si el usuario es el creador del modelo.
     * El modelo debe tener un campo 'user_id'.
     */
    protected function isOwner(User $user, $model)
    {
        return $user->id === $model->user_id;
    }
}
