<?php

namespace App\Http\Controllers;

use App\Support\SlugHelper;
use App\Support\JsonLoader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Support\SearchHelper; 

class SkillController extends Controller
{
    public function loadSkills(): Collection
    {
        return Cache::rememberForever('skills_processed', function () {
            $raw = JsonLoader::load('data/skills.json');

            return collect($raw)->map(function ($skill) {
                $skill['slug'] = SlugHelper::make($skill['name']);
                return $skill;
            });
        });
    }

    private function getPaginatedSkills(int $perPage = 18): LengthAwarePaginator
    {
        $skills = $this->loadSkills();

        // ⭐ Aplicamos la búsqueda inteligente (Nombre y normalización)
        if (request()->filled('q')) {
            $skills = SearchHelper::apply($skills, request('q'));
        }

        $skills = $skills->values();
        $page = request('page', 1);

        $paginator = new LengthAwarePaginator(
            $skills->forPage($page, $perPage),
            $skills->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        $paginator->appends(request()->query());

        return $paginator;
    }

    private function findBySlug(string $slug): ?array
    {
        return $this->loadSkills()->firstWhere('slug', $slug);
    }

    public function index()
    {
        return view('seccion.skills', [
            'paginatedSkills' => $this->getPaginatedSkills()
        ]);
    }

    public function show($slug)
    {
        $skill = $this->findBySlug($slug);

        if (!$skill) abort(404);

        return view('seccion.skillsShow', compact('skill'));
    }
}