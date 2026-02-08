<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaveButton extends Component
{
    public $id;
    public $type;
    public $url;
    public $isSaved;
    public $shouldShow;

    public function __construct($id, $type)
    {
        $this->id = $id;
        $this->type = $type;
        $this->url = route('item.save', ['type' => $type, 'id' => $id]);
        $this->shouldShow = Auth::check();

        if ($this->shouldShow) {
            // Comprobamos si el registro existe en la tabla correspondiente
            $this->isSaved = DB::table('saved_guides')
                ->where('user_id', Auth::id())
                ->where('guide_id', $id)
                ->exists();
        }
    }

    public function render()
    {
        return view('components.save-button');
    }
}