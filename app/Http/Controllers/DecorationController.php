<?php

namespace App\Http\Controllers;

use App\Services\DecorationService;

class DecorationController extends Controller
{
    public function index(DecorationService $service)
    {
        return view('seccion.decorations', [
            'paginatedDecorations' => $service->getPaginatedDecorations()
        ]);
    }

    public function show($slug, DecorationService $service)
    {
        $decoration = $service->findBySlug($slug);

        if (!$decoration) abort(404);

        return view('seccion.decorationsShow', compact('decoration'));
    }
}
