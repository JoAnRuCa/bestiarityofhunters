<?php

namespace App\Http\Controllers;

use App\Support\SlugHelper;
use App\Support\JsonLoader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Support\SearchHelper;

class DecorationController extends Controller
{
    public function loadDecorations(): Collection
    {
        return Cache::rememberForever('decorations_processed', function () {
            $raw = JsonLoader::load('data/decorations.json');

            return collect($raw)->map(function ($decoration) {
                $decoration['slug'] = SlugHelper::make($decoration['name']);
                return $decoration;
            });
        });
    }

   private function getPaginatedDecorations(int $perPage = 18): LengthAwarePaginator
{
    $decorations = $this->loadDecorations();

    if (request()->filled('q')) {
        // El helper ahora se encarga de nombres, habilidades, kind y slots (números)
        $decorations = SearchHelper::apply($decorations, request('q'));
    }

    $decorations = $decorations->values();
    $page = request('page', 1);

    $paginator = new LengthAwarePaginator(
        $decorations->forPage($page, $perPage),
        $decorations->count(),
        $perPage,
        $page,
        ['path' => request()->url()]
    );

    $paginator->appends(request()->query());

    return $paginator;
}

    private function findBySlug(string $slug): ?array
    {
        return $this->loadDecorations()->firstWhere('slug', $slug);
    }

    public function index()
    {
        return view('seccion.decorations', [
            'paginatedDecorations' => $this->getPaginatedDecorations()
        ]);
    }

    public function show($slug)
    {
        $decoration = $this->findBySlug($slug);

        if (!$decoration) abort(404);

        return view('seccion.decorationsShow', compact('decoration'));
    }
}