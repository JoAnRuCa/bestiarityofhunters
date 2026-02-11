<?php

namespace App\View\Components;

use App\Models\Tag;
use Illuminate\View\Component;

class TagSelector extends Component
{
    public $allTags;
    public $selectedTags;
    public $showAll; // <--- DEBE SER PÚBLICA

    public function __construct($selectedTags = [], $showAll = false)
    {
        $this->showAll = $showAll; // <--- ASIGNAR EL VALOR
        
        $allTags = Tag::all();

        // Lista de armas
        $weaponNames = ['Great Sword', 'Long Sword', 'Bow', 'Hammer', 'Lance', 'Gunlance', 'Switch Axe', 'Charge Blade', 'Insect Glaive', 'Light Bowgun', 'Heavy Bowgun', 'Sword and Shield', 'Dual Blades', 'Hunting Horn'];

        if ($this->showAll) {
            $this->allTags = $allTags;
        } else {
            // Filtramos para la vista, pero las mantenemos en la colección si el JS las necesita
            // (Opcional: puedes cargar todas y filtrar solo en el Blade con CSS como sugerí antes)
            $this->allTags = $allTags; 
        }

        $this->selectedTags = is_array($selectedTags) ? $selectedTags : $selectedTags->pluck('id')->toArray();
    }

    public function render()
    {
        return view('components.tag-selector');
    }
}