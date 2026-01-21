<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class ArmorController extends Controller
{
     public function index()
    {
        // 1. Cargar JSON y asignar IDs fijos
        $armor = collect(json_decode(Storage::get('data/armors.json'), true))->map(function ($item, $index) {
            $item['id'] = $index;
            $item['slug'] = Str::slug($item['name']);
            return $item;
        });

        // 2. Filtrar si hay búsqueda
        if (request()->has('q')) {
            $q = strtolower(request()->get('q'));

            $armor = $armor->filter(function ($armor) use ($q) {

            $nameMatch = str_contains(strtolower($armor['name']), $q);

            $kindMatch = isset($armor['kind']) 
                ? str_contains(strtolower($armor['kind']), $q)
                : false;

            return $nameMatch || $kindMatch;
        });
    }

        // 3. Paginar 20 por página
        $page = request()->get('page', 1);
        $perPage = 20;
        $paginatedArmor = new LengthAwarePaginator(
            $armor->forPage($page, $perPage),
            $armor->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return view('seccion.armors', compact('paginatedArmor'));
    }

    public function show($slug)
    {
        $armor = collect(json_decode(Storage::get('data/armors.json'), true))->map(function ($item, $index) {
            $item['id'] = $index;
            $item['slug'] = Str::slug($item['name']);
            return $item;
        });
        $armor = $armor->firstWhere('slug', $slug);
        return view('seccion.armorsShow', compact('armor'));
    }
}
