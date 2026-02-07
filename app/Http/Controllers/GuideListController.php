<?php

namespace App\Http\Controllers;

use App\Models\Guide;

class GuideListController extends Controller
{
    public function index()
    {
        $guides = Guide::with('tags', 'user')
            ->latest()
            ->paginate(10);

        return view('seccion.guidesList', [
            'guides' => $guides
        ]);
    }
}
