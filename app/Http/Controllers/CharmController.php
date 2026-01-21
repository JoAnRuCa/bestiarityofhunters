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
        // 1. Cargar JSON y asignar IDs fijos
        $charm = collect(json_decode(Storage::get('data/charms.json'), true))->map(function ($item, $index) {
            $item['id'] = $index;
            $item['slug'] = Str::slug($item['name']);
            return $item;
        });

        // 2. Filtrar si hay búsqueda
        if (request()->has('q')) {
            $q = request()->get('q');
            $charm = $charm->filter(function ($charm) use ($q) {
                return str_contains(strtolower($charm['name']), strtolower($q));
            });
        }

        // 3. Paginar 20 por página
        $page = request()->get('page', 1);
        $perPage = 20;
        $paginatedCharm = new LengthAwarePaginator(
            $charm->forPage($page, $perPage),
            $charm->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return view('seccion.charms', compact('paginatedCharm'));
    }

    public function show($slug)
    {
        $charm = collect(json_decode(Storage::get('data/charms.json'), true))->map(function ($item, $index) {
            $item['id'] = $index;
            $item['slug'] = Str::slug($item['name']);
            return $item;
        });
        $charm = $charm->firstWhere('slug', $slug);
        return view('seccion.charmsShow', compact('charm'));
    }
}