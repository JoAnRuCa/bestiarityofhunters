<?php

namespace App\View\Components;

use Illuminate\View\Component;

class EditButton extends Component
{
    public $url;
    public $editable;

    /**
     * @param string $url
     * @param bool $editable
     */
    public function __construct($url, $editable = false)
    {
        $this->url = $url;
        $this->editable = $editable;
    }

    public function render()
    {
        return view('components.edit-button');
    }
}