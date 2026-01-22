<?php

namespace App\Http\Controllers;

use App\Services\WeaponService;

class WeaponController extends Controller
{
    public function index(WeaponService $service)
    {
        return view('seccion.weapons', [
            'paginatedWeapons' => $service->getPaginatedWeapons()
        ]);
    }

    public function show($slug, WeaponService $service)
    {
        $weapon = $service->findBySlug($slug);

        if (!$weapon) abort(404);

        return view('seccion.weaponsShow', compact('weapon'));
    }
}
