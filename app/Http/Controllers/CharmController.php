<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class CharmController extends Controller
{
    private function loadCharms()
{
    return collect(json_decode(Storage::get('data/charms.json'), true))
        ->filter(fn($item) => isset($item['ranks'][0]['name']))
       ->map(function ($item, $index) {

    $fullName = $item['ranks'][0]['name'];

    $parts = explode(' ', $fullName);
    $last = end($parts);

    $roman = ['I','II','III','IV','V','VI','VII'];

    if (in_array($last, $roman)) {
        array_pop($parts);
    }

    $baseName = implode(' ', $parts);

    $item['id'] = $index;

    // Generar slug por cada rank
    foreach ($item['ranks'] as $i => $rank) {
        $item['ranks'][$i]['slug'] = Str::slug($baseName . '-' . $rank['level']);
    }

    return $item;
});

}


    public function index()
{
    $charms = $this->loadCharms();

    // Convertir charms en una lista plana de ranks
    $ranks = collect();

    foreach ($charms as $charm) {
        foreach ($charm['ranks'] as $rank) {
            $rank['parent'] = $charm; // guardar charm original
            $ranks->push($rank);
        }
    }

    // Filtro de búsqueda
    if (request()->filled('q')) {
        $q = strtolower(request()->get('q'));

        $ranks = $ranks->filter(function ($rank) use ($q) {
            return str_contains(strtolower($rank['name']), $q);
        });
    }

    // Reindexar para que funcione la paginación
    $ranks = $ranks->values();

    // Paginación
    $page = request()->get('page', 1);
    $perPage = 20;

    $paginatedCharm = new LengthAwarePaginator(
        $ranks->forPage($page, $perPage),
        $ranks->count(),
        $perPage,
        $page,
        ['path' => request()->url()]
    );

    return view('seccion.charms', compact('paginatedCharm'));
}



    public function show($slug)
    {
        $charms = $this->loadCharms();

        // Buscar el rank correcto para no devolver siempre 1
        foreach ($charms as $charm) {
            foreach ($charm['ranks'] as $rank) {
                if ($rank['slug'] === $slug) {
                    return view('seccion.charmsShow', [
                        'charm' => $charm,
                        'selectedRank' => $rank
                    ]);
                }
            }
        }

        abort(404);
    }



}

