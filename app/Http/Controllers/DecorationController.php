<?php

namespace App\Http\Controllers;

use App\Support\SlugHelper;
use App\Support\JsonLoader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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

    private function getPaginatedDecorations(int $perPage = 20): LengthAwarePaginator
    {
        $decorations = $this->loadDecorations();

        // ⭐ Búsqueda por nombre
        if (request()->filled('q')) {
            $q = strtolower(request('q'));

            $decorations = $decorations->filter(function ($decoration) use ($q) {
                return str_contains(strtolower($decoration['name']), $q);
            });
        }

        $decorations = $decorations->values();
        $page = request('page', 1);

        // ⭐ Paginación con parámetros preservados
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

    private function index()
    {
        return view('seccion.decorations', [
            'paginatedDecorations' => $this->getPaginatedDecorations()
        ]);
    }

    private function show($slug)
    {
        $decoration = $this->findBySlug($slug);

        if (!$decoration) abort(404);

        return view('seccion.decorationsShow', compact('decoration'));
    }
}
