<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Guide;

class VoteBlock extends Component
{
    public $guide;
    public $votoUsuario;

    public function __construct(Guide $guide)
    {
        $this->guide = $guide;

        $this->votoUsuario = auth()->check()
            ? ($guide->votoDe(auth()->id())->tipo ?? 0)
            : 0;
    }

    public function render()
    {
        return view('components.vote-block');
    }
}
