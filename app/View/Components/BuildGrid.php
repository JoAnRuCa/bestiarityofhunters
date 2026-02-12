<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BuildGrid extends Component
{
    public $builds;
    public $editable;

    public function __construct($builds, $editable = false)
    {
        $this->builds = $builds;
        $this->editable = $editable;
    }

    public function render()
    {
        return view('components.build-grid');
    }
}