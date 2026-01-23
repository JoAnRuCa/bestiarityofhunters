<?php

namespace App\Http\Controllers;

use App\Support\SlugHelper;
use App\Support\JsonLoader;
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

        // ⭐ Búsqueda por nombre y por tipo (kind)
        if (request()->filled('q')) {
            $q = strtolower(request('q'));

            $weapons = $weapons->filter(function ($weapon) use ($q) {

                $nameMatch = str_contains(strtolower($weapon['name']), $q);

                $kindMatch = isset($weapon['kind'])
                    ? str_contains(strtolower($weapon['kind']), $q)
                    : false;

                return $nameMatch || $kindMatch;
            });
        }

        $weapons = $weapons->values();
        $page = request('page', 1);

        // ⭐ Paginación con parámetros preservados
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
