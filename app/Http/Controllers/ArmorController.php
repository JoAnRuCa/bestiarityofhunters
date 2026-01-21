<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class ArmorController extends Controller
{
    private function loadArmor()
    {
        return collect(json_decode(Storage::get('data/armors.json'), true))
            ->map(function ($item, $index) {
                $item['id'] = $index;
                $item['slug'] = Str::slug($item['name']);
                return $item;
            });
    }

    public function index()
    {
        $armor = $this->loadArmor();

        if (request()->filled('q')) {
            $q = strtolower(request()->get('q'));

            $armor = $armor->filter(function ($item) use ($q) {
                $nameMatch = str_contains(strtolower($item['name']), $q);

                $kindMatch = isset($item['kind'])
                    ? str_contains(strtolower($item['kind']), $q)
                    : false;

                return $nameMatch || $kindMatch;
            });
        }

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
        $armor = $this->loadArmor()->firstWhere('slug', $slug);

        if (!$armor) {
            abort(404);
        }

        return view('seccion.armorsShow', compact('armor'));
    }
}
