<?php

namespace App\Http\Controllers;

use App\Support\SlugHelper;
use App\Support\JsonLoader;
use App\Support\SearchHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SkillController extends Controller
{
    private function loadSkills(): Collection
    {
        return Cache::rememberForever('skills_processed', function () {

            $raw = JsonLoader::load('data/skills.json');

            return collect($raw)->map(function ($skill) {
                $skill['slug'] = SlugHelper::make($skill['name']);
                return $skill;
            });
        });
    }

    private function getPaginatedSkills(int $perPage = 20): LengthAwarePaginator
    {
        $skills = $this->loadSkills();

        if (request()->filled('q')) {
            $skills = SearchHelper::text($skills, 'name', request('q'));
        }

        $skills = $skills->values();
        $page = request('page', 1);

        return new LengthAwarePaginator(
            $skills->forPage($page, $perPage),
            $skills->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
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
