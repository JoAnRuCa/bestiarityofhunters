<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CommentItem extends Component
{
    public $comment;
    public $item;
    public $type;
    public $level;

    public function __construct($comment, $item, $type, $level = 0)
    {
        $this->comment = $comment;
        $this->item = $item;
        $this->type = $type;
        $this->level = $level;
    }

    public function render()
    {
        return view('components.comment-item');
    }
}