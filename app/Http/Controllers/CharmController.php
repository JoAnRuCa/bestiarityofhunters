<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class CharmController extends Controller
{
    public function index()
    {
        // 1. Cargar charms y generar slug correctamente
        $charms = collect(json_decode(Storage::get('data/charms.json'), true))
    ->filter(fn($item) => isset($item['ranks'][0]['name']))
    ->map(function ($item, $index) {

        $fullName = $item['ranks'][0]['name']; // Windproof Charm I

        // Quitar el último token si es un número romano
        $parts = explode(' ', $fullName);
        $last = end($parts);

        // Lista de números romanos típicos en charms
        $roman = ['I','II','III','IV','V','VI','VII'];

        if (in_array($last, $roman)) {
            array_pop($parts); // quitar el número romano
        }

        $baseName = implode(' ', $parts); // Windproof Charm

        $item['id'] = $index;
        $item['slug'] = Str::slug($baseName); // windproof-charm

        return $item;
    });


        // 2. Filtrar si hay búsqueda
        if (request()->has('q')) {
            $q = strtolower(request()->get('q'));

            $charms = $charms->filter(function ($charm) use ($q) {
                return str_contains(strtolower($charm['ranks'][0]['name']), $q);
            });
        }

        // 3. Paginar
        $page = request()->get('page', 1);
        $perPage = 20;

        $paginatedCharm = new LengthAwarePaginator(
            $charms->forPage($page, $perPage),
            $charms->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return view('seccion.charms', compact('paginatedCharm'));
    }

    public function show($slug, $rank)
{
    $charms = collect(json_decode(Storage::get('data/charms.json'), true))
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
            $item['slug'] = Str::slug($baseName);

            return $item;
        });

    // Buscar charm por slug base
    $charm = $charms->firstWhere('slug', $slug);

    // Buscar rank seleccionado
    $selectedRank = collect($charm['ranks'])->firstWhere('level', intval($rank));

    return view('seccion.charmsShow', compact('charm', 'selectedRank'));
}


}
