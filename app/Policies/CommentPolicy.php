<?php

namespace App\Policies;

use App\Models\User;

class CommentPolicy extends BasePolicy
{
    /**
     * Determina si el usuario puede editar o borrar el comentario.
     * Funciona para cualquier modelo de comentario que tenga 'user_id'.
     */
    public function update(User $user, $comment)
    {
        return $this->isOwner($user, $comment);
    }

    public function delete(User $user, $comment)
    {
        return $this->isOwner($user, $comment);
    }
}
