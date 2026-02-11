<?php

namespace App\Http\Controllers;

use App\Support\SlugHelper;
use App\Support\JsonLoader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Support\SearchHelper;

class WeaponController extends Controller
{
    public function loadWeapons(): Collection
    {
        return Cache::rememberForever('weapons_processed', function () {
            $raw = JsonLoader::load('data/weapons.json');

            return collect($raw)->map(function ($weapon) {
                $weapon['slug'] = SlugHelper::make($weapon['name']);
                return $weapon;
            });
        });
    }

    private function getPaginatedWeapons(int $perPage = 18): LengthAwarePaginator
{
    $weapons = $this->loadWeapons();

    if (request()->filled('q')) {
    $weapons = SearchHelper::apply($weapons, request('q'));
}

    $weapons = $weapons->values();
    $page = request('page', 1);

    $paginator = new LengthAwarePaginator(
        $weapons->forPage($page, $perPage),
        $weapons->count(),
        $perPage,
        $page,
        ['path' => request()->url()]
    );

    $paginator->appends(request()->query());

    return $paginator;
}

    private function findBySlug(string $slug): ?array
    {
        return $this->loadWeapons()->firstWhere('slug', $slug);
    }

    public function index()
    {
        return view('seccion.weapons', [
            'paginatedWeapons' => $this->getPaginatedWeapons()
        ]);
    }

    public function show($slug)
    {
        $weapon = $this->findBySlug($slug);

        if (!$weapon) abort(404);

        return view('seccion.weaponsShow', compact('weapon'));
    }
}