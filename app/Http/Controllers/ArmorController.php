<?php

namespace App\Http\Controllers;

use App\Support\SlugHelper;
use App\Support\JsonLoader;
use App\Support\SearchHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ArmorController extends Controller
{
    private function loadArmor(): Collection
    {
        return Cache::rememberForever('armor_processed', function () {

            $raw = JsonLoader::load('data/armors.json');

            return collect($raw)->map(function ($armor) {
                $armor['slug'] = SlugHelper::make($armor['name']);
                return $armor;
            });
        });
    }

    private function getPaginatedArmor(int $perPage = 20): LengthAwarePaginator
    {
        $armor = $this->loadArmor();

        if (request()->filled('q')) {
            $armor = $armor->filter(function ($item) {
                $q = strtolower(request('q'));

                $nameMatch = str_contains(strtolower($item['name']), $q);
                $kindMatch = isset($item['kind'])
                    ? str_contains(strtolower($item['kind']), $q)
                    : false;

                return $nameMatch || $kindMatch;
            });
        }

        $armor = $armor->values();
        $page = request('page', 1);

        return new LengthAwarePaginator(
            $armor->forPage($page, $perPage),
            $armor->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
    }

    private function findBySlug(string $slug): ?array
    {
        return $this->loadArmor()->firstWhere('slug', $slug);
    }

    public function index()
    {
        return view('seccion.armors', [
            'paginatedArmor' => $this->getPaginatedArmor()
        ]);
    }

    public function show($slug)
    {
        $armor = $this->findBySlug($slug);

        if (!$armor) abort(404);

        return view('seccion.armorsShow', compact('armor'));
    }
}
