<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Guide;

class VoteBlock extends Component
{
    public $item;
    public $type;
    public $votoUsuario;

    public function __construct($item, $type = 'guide')
    {
        $this->item = $item;
        $this->type = $type;

        $this->votoUsuario = auth()->check()
            ? ($item->votos()->where('user_id', auth()->id())->first()->tipo ?? 0)
            : 0;
    }

    public function render() { return view('components.vote-block'); }
}
