<?php

namespace App\View\Components;

use App\Models\Tag;
use Illuminate\View\Component;

class TagSelector extends Component
{
    public $allTags;
    public $selectedTags;

    public function __construct($selectedTags = [])
    {
        $this->allTags = Tag::all();
        // Convertimos a array para manejar tanto old() como colecciones de Eloquent
        $this->selectedTags = is_array($selectedTags) ? $selectedTags : $selectedTags->pluck('id')->toArray();
    }

    public function render()
    {
        return view('components.tag-selector');
    }
}