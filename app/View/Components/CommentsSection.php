<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CommentsSection extends Component
{
    public $item;
    public $type;

    public function __construct($item, $type)
    {
        $this->item = $item;
        $this->type = $type;
    }

    public function render()
    {
        return view('components.comments-section');
    }
}