<?php

namespace App\Http\Controllers;

use App\Services\CharmService;

class CharmController extends Controller
{
    public function index(CharmService $service)
    {
        return view('seccion.charms', [
            'paginatedCharm' => $service->getPaginatedRanks()
        ]);
    }

    public function show($slug, CharmService $service)
    {
        $result = $service->findRankBySlug($slug);

        if (!$result) abort(404);

        return view('seccion.charmsShow', [
            'charm' => $result['charm'],
            'selectedRank' => $result['rank']
        ]);
    }
}
