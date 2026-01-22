<?php

namespace App\Services;

use App\Support\SlugHelper;
use App\Support\JsonLoader;
use App\Support\SearchHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ArmorService
{
    public function loadArmor(): Collection
    {
        return Cache::rememberForever('armor_processed', function () {

            $raw = JsonLoader::load('data/armors.json');

            return collect($raw)->map(function ($armor) {
                $armor['slug'] = SlugHelper::make($armor['name']);
                return $armor;
            });
        });
    }

    public function getPaginatedArmor(int $perPage = 20): LengthAwarePaginator
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

    public function findBySlug(string $slug): ?array
    {
        return $this->loadArmor()->firstWhere('slug', $slug);
    }
}
