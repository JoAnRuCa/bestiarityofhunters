<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class WeaponController extends Controller
{
     public function index()
    {
        // 1. Cargar JSON y asignar IDs fijos
        $weapons = collect(json_decode(Storage::get('data/weapons.json'), true))->map(function ($item, $index) {
            $item['id'] = $index;
            $item['slug'] = Str::slug($item['name']);
            return $item;
        });

        // 2. Filtrar si hay búsqueda
        if (request()->has('q')) {
            $q = request()->get('q');
            $weapons = $weapons->filter(function ($weapon) use ($q) {
                return str_contains(strtolower($weapon['name']), strtolower($q));
            });
        }
        // 3. Paginar 20 por página
        $page = request()->get('page', 1);
        $perPage = 20;
        $paginatedWeapons = new LengthAwarePaginator(
            $weapons->forPage($page, $perPage),
            $weapons->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return view('seccion.weapons', compact('paginatedWeapons'));
    }

    public function show($slug)
    {
        $weapons = collect(json_decode(Storage::get('data/weapons.json'), true))->map(function ($item, $index) {
            $item['id'] = $index;
            $item['slug'] = Str::slug($item['name']);
            return $item;
        });
        $weapon = $weapons->firstWhere('slug', $slug);
        return view('seccion.weaponsShow', compact('weapon'));
    }
}
