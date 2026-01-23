<?php

namespace App\Http\Controllers;

use App\Support\SlugHelper;
use App\Support\JsonLoader;
use App\Support\SearchHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CharmController extends Controller
{
    private array $roman = ['I','II','III','IV','V','VI','VII'];

    private function loadCharms(): Collection
    {
        return Cache::rememberForever('charms_processed', function () {

            $raw = JsonLoader::load('data/charms.json');

            return collect($raw)
                ->filter(fn($c) => isset($c['ranks'][0]['name']))
                ->map(function ($c) {

                    // Obtener nombre base sin número romano
                    $parts = explode(' ', $c['ranks'][0]['name']);
                    if (in_array(end($parts), $this->roman)) {
                        array_pop($parts);
                    }
                    $base = implode(' ', $parts);

                    // Generar slug por cada rank
                    foreach ($c['ranks'] as &$rank) {
                        $rank['slug'] = SlugHelper::make("$base-{$rank['level']}");
                    }

                    return $c;
                });
        });
    }

    private function getAllRanks(): Collection
    {
        return $this->loadCharms()->flatMap(function ($charm) {
            return collect($charm['ranks'])->map(function ($rank) use ($charm) {
                $rank['parent'] = $charm;
                return $rank;
            });
        });
    }

    private function getPaginatedRanks(int $perPage = 20): LengthAwarePaginator
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

    private function findRankBySlug(string $slug): ?array
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

    public function index()
    {
        return view('seccion.charms', [
            'paginatedCharm' => $this->getPaginatedRanks()
        ]);
    }

    public function show($slug)
    {
        $result = $this->findRankBySlug($slug);

        if (!$result) abort(404);

        return view('seccion.charmsShow', [
            'charm' => $result['charm'],
            'selectedRank' => $result['rank']
        ]);
    }
}
