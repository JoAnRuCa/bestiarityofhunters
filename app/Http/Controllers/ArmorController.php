<?php

namespace App\Http\Controllers;

use App\Support\SlugHelper;
use App\Support\JsonLoader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Support\SearchHelper;

class ArmorController extends Controller
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

    private function getPaginatedArmor(int $perPage = 18): LengthAwarePaginator
    {
        $armor = $this->loadArmor();

        if (request()->filled('q')) {

    $armor = SearchHelper::apply($armor, request('q'));
}

        $armor = $armor->values();
        $page = request('page', 1);

        $paginator = new LengthAwarePaginator(
            $armor->forPage($page, $perPage),
            $armor->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        $paginator->appends(request()->query());

        return $paginator;
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

