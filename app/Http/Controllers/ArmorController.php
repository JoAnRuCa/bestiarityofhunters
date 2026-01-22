<?php

namespace App\Http\Controllers;

use App\Services\ArmorService;

class ArmorController extends Controller
{
    public function index(ArmorService $service)
    {
        return view('seccion.armors', [
            'paginatedArmor' => $service->getPaginatedArmor()
        ]);
    }

    public function show($slug, ArmorService $service)
    {
        $armor = $service->findBySlug($slug);

        if (!$armor) abort(404);

        return view('seccion.armorsShow', compact('armor'));
    }
}

