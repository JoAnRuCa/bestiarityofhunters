<?php

namespace App\View\Components;

use App\Models\Tag;
use Illuminate\View\Component;

class FilterPanel extends Component
{
    public $action;
    public $activeTags;
    public $allTags;

    public function __construct($action, $activeTags = [])
    {
        $this->action = $action;
        // Nos aseguramos de que sea un array
        $this->activeTags = (array) $activeTags;
        // Obtenemos los tags aquí para no hacerlo en la vista
        $this->allTags = Tag::all();
    }

    /**
     * Comprueba si un tag específico está en la lista de activos.
     */
    public function isTagActive($tagName): bool
    {
        return in_array($tagName, $this->activeTags);
    }

    public function render()
    {
        return view('components.filter-panel');
    }
}