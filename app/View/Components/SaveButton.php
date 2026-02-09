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
        // Apunta a la nueva ruta universal: /saved/toggle/{type}/{id}
        $this->url = route('saved.toggle', ['type' => $type, 'id' => $id]);
        $this->shouldShow = Auth::check();

        if ($this->shouldShow) {
            // Dinamismo según el tipo (guide o build)
            $table = ($type === 'guide') ? 'saved_guides' : 'saved_builds';
            $foreignKey = ($type === 'guide') ? 'guide_id' : 'build_id';

            $this->isSaved = DB::table($table)
                ->where('user_id', Auth::id())
                ->where($foreignKey, $id)
                ->exists();
        }
    }

    public function render()
    {
        return view('components.save-button');
    }
}