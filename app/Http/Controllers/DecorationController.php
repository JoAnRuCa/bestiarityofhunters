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

        // ⭐ Búsqueda por nombre, habilidad, kind y slot
        if (request()->filled('q')) {
            $q = strtolower(request('q'));

            $decorations = $decorations->filter(function ($decoration) use ($q) {

                // 1. Coincidencia por nombre de la decoración
                $nameMatch = str_contains(strtolower($decoration['name']), $q);

                // 2. Coincidencia por nombre de habilidad
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

                // 3. Coincidencia por kind
                $kindMatch = isset($decoration['kind'])
                    ? str_contains(strtolower($decoration['kind']), $q)
                    : false;

                // 4. Coincidencia por slot (convertimos a string para buscar)
                $slotMatch = isset($decoration['slot'])
                    ? str_contains((string)$decoration['slot'], $q)
                    : false;

                return $nameMatch || $skillMatch || $kindMatch || $slotMatch;
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
