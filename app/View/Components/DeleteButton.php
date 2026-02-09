<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DeleteButton extends Component
{
    public $action;
    public $id;

    public function __construct($action, $id)
    {
        $this->action = $action;
        $this->id = $id;
    }

public function render() {
    return view('components.delete-button');
}
}