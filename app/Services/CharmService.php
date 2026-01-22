<?php

namespace App\Services;

use App\Support\SlugHelper;
use App\Support\JsonLoader;
use App\Support\SearchHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CharmService
{
    private array $roman = ['I','II','III','IV','V','VI','VII'];

    public function loadCharms(): Collection
    {
        return Cache::rememberForever('charms_processed', function () {

            $raw = JsonLoader::load('data/charms.json');

            return collect($raw)
                ->filter(fn($c) => isset($c['ranks'][0]['name']))
                ->map(function ($c) {

                    $parts = explode(' ', $c['ranks'][0]['name']);
                    if (in_array(end($parts), $this->roman)) {
                        array_pop($parts);
                    }
                    $base = implode(' ', $parts);

                    foreach ($c['ranks'] as &$rank) {
                        $rank['slug'] = SlugHelper::make("$base-{$rank['level']}");
                    }

                    return $c;
                });
        });
    }

    public function getAllRanks(): Collection
    {
        return $this->loadCharms()->flatMap(function ($charm) {
            return collect($charm['ranks'])->map(function ($rank) use ($charm) {
                $rank['parent'] = $charm;
                return $rank;
            });
        });
    }

    public function getPaginatedRanks(int $perPage = 20): LengthAwarePaginator
    {
        $ranks = $this->getAllRanks();

        if (request()->filled('q')) {
            $ranks = SearchHelper::text($ranks, 'name', request('q'));
        }

        $ranks = $ranks->values();
        $page = request('page', 1);

        return new LengthAwarePaginator(
            $ranks->forPage($page, $perPage),
            $ranks->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
    }

    public function findRankBySlug(string $slug): ?array
    {
        $result = $this->getAllRanks()
            ->first(fn($r) => $r['slug'] === $slug);

        if (!$result) {
            return null;
        }

        return [
            'charm' => $result['parent'],
            'rank'  => $result
        ];
    }
}
