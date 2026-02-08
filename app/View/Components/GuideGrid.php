<?php

namespace App\View\Components;

use Illuminate\View\Component;

class GuideGrid extends Component
{
    public $guides;

    public function __construct($guides)
    {
        // Recibimos las guías desde la vista principal
        $this->guides = $guides;
    }

    public function render()
    {
        return view('components.guide-grid');
    }
}