<?php

namespace App\View\Components;

use App\Models\Tag;
use Illuminate\View\Component;

class TagSelector extends Component
{
    public $allTags;
    public $selectedTags;

    public function __construct($selectedTags = [], $showAll = false)
    {
        $allTags = Tag::all();

        if ($showAll) {
            // Si queremos ver todo, no filtramos nada
            $this->allTags = $allTags;
        } else {
            // Aquí mantienes tu lógica de filtrado para la otra vista
            $weaponNames = ['Great Sword', 'Long Sword', 'Bow', 'Hammer', 'Lance', 'Gunlance', 'Switch Axe', 'Charge Blade', 'Insect Glaive', 'Light Bowgun', 'Heavy Bowgun', 'Sword and Shield', 'Dual Blades', 'Hunting Horn'];
            $this->allTags = $allTags->whereNotIn('name', $weaponNames);
        }

        $this->selectedTags = is_array($selectedTags) ? $selectedTags : $selectedTags->pluck('id')->toArray();
    }

    public function render()
    {
        return view('components.tag-selector');
    }
}