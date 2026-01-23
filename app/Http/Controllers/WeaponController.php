<?php

namespace App\Http\Controllers;

use App\Support\SlugHelper;
use App\Support\JsonLoader;
use App\Support\SearchHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class WeaponController extends Controller
{
    private function loadWeapons(): Collection
    {
        return Cache::rememberForever('weapons_processed', function () {

            $raw = JsonLoader::load('data/weapons.json');

            return collect($raw)->map(function ($weapon) {
                $weapon['slug'] = SlugHelper::make($weapon['name']);
                return $weapon;
            });
        });
    }

    private function getPaginatedWeapons(int $perPage = 20): LengthAwarePaginator
    {
        $weapons = $this->loadWeapons();

        if (request()->filled('q')) {
            $weapons = SearchHelper::text($weapons, 'name', request('q'));
        }

        $weapons = $weapons->values();
        $page = request('page', 1);

        return new LengthAwarePaginator(
            $weapons->forPage($page, $perPage),
            $weapons->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
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
