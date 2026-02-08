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
        // Convertimos a array por si viene de old() o de una relación
        $this->selectedTags = collect($selectedTags)->toArray();
    }

    public function render()
    {
        return view('components.tag-selector');
    }
}