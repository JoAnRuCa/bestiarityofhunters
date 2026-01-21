<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class WeaponController extends Controller
{
    private function loadWeapons()
    {
        return collect(json_decode(Storage::get('data/weapons.json'), true))
            ->map(function ($item, $index) {
                $item['id'] = $index;
                $item['slug'] = Str::slug($item['name']);
                return $item;
            });
    }

    public function index()
    {
        $weapons = $this->loadWeapons();

        if (request()->filled('q')) {
            $q = strtolower(request()->get('q'));

            $weapons = $weapons->filter(function ($weapon) use ($q) {
                $nameMatch = str_contains(strtolower($weapon['name']), $q);

                $kindMatch = isset($weapon['kind'])
                    ? str_contains(strtolower($weapon['kind']), $q)
                    : false;

                return $nameMatch || $kindMatch;
            });
        }

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
        $weapon = $this->loadWeapons()->firstWhere('slug', $slug);

        if (!$weapon) {
            abort(404);
        }

        return view('seccion.weaponsShow', compact('weapon'));
    }
}
