<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Build;

class BuildPolicy extends BasePolicy
{
    /**
     * Determina si el usuario puede ver la build (previsualización del editor).
     */
    public function view(User $user, Build $build)
    {
        return $this->isOwner($user, $build);
    }

    /**
     * Determina si el usuario puede editar la build.
     */
    public function update(User $user, Build $build)
    {
        return $this->isOwner($user, $build);
    }

    /**
     * Determina si el usuario puede eliminar la build.
     */
    public function delete(User $user, Build $build)
    {
        return $this->isOwner($user, $build);
    }
}
