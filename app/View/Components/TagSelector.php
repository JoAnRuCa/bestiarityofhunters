<?php

namespace App\View\Components;

use App\Models\Tag;
use Illuminate\View\Component;

class TagSelector extends Component
{
    public $allTags;
    public $selectedTags;
    public $showAll;
    public $weaponNames = [
        'Great Sword', 'Long Sword', 'Bow', 'Hammer', 'Lance', 
        'Gunlance', 'Switch Axe', 'Charge Blade', 'Insect Glaive', 
        'Light Bowgun', 'Heavy Bowgun', 'Sword and Shield', 
        'Dual Blades', 'Hunting Horn'
    ];

    public function __construct($selectedTags = [], $showAll = false)
    {
        $this->showAll = $showAll;
        
        // Obtenemos todos los tags y les inyectamos una propiedad lógica
        $this->allTags = Tag::all()->map(function($tag) {
            $tag->is_weapon = in_array($tag->name, $this->weaponNames);
            return $tag;
        });

        $this->selectedTags = is_array($selectedTags) 
            ? $selectedTags 
            : $selectedTags->pluck('id')->toArray();
    }

    public function render()
    {
        return view('components.tag-selector');
    }
}