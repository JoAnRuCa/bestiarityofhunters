<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class BuildEditorController extends Controller
{
    public function index()
    {
        return view('seccion.buildEditor', [
            'weapons'      => json_decode(Storage::get('data/weapons.json')),
            'armors'       => json_decode(Storage::get('data/armors.json')),
            'charms'       => json_decode(Storage::get('data/charms.json')),
            'decorations'  => json_decode(Storage::get('data/decorations.json')),
            'skills'       => json_decode(Storage::get('data/skills.json')),
        ]);
    }
}
