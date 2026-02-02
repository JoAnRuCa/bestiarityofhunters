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

        // ⭐ Búsqueda por nombre de decoración o por nombre de habilidad
        if (request()->filled('q')) {
            $q = strtolower(request('q'));

            $decorations = $decorations->filter(function ($decoration) use ($q) {

                // Coincidencia por nombre de la decoración
                $nameMatch = str_contains(strtolower($decoration['name']), $q);

                // Coincidencia por nombre de habilidad
                $skillMatch = false;

                if (isset($decoration['skills']) && is_array($decoration['skills'])) {
                    foreach ($decoration['skills'] as $skillEntry) {
                        if (isset($skillEntry['skill']['name'])) {
                            if (str_contains(strtolower($skillEntry['skill']['name']), $q)) {
                                $skillMatch = true;
                                break;
                            }
                        }
                    }
                }

                return $nameMatch || $skillMatch;
            });
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
