<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Guide;

class GuidePolicy extends BasePolicy
{
    /**
     * Determina si el usuario puede editar la guía.
     */
    public function update(User $user, Guide $guide)
    {
        return $this->isOwner($user, $guide);
    }

    /**
     * Determina si el usuario puede eliminar la guía.
     */
    public function delete(User $user, Guide $guide)
    {
        return $this->isOwner($user, $guide);
    }
}
