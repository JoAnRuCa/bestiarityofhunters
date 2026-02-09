<?php

namespace App\View\Components;

use Illuminate\View\Component;

class GuideGrid extends Component
{
    public $guides;
    public $editable;

    /**
     * @param $guides
     * @param bool $editable (Por defecto false para que no salga el botón en la lista general)
     */
    public function __construct($guides, $editable = false)
    {
        $this->guides = $guides;
        $this->editable = $editable;
    }

    public function render()
    {
        return view('components.guide-grid');
    }
}