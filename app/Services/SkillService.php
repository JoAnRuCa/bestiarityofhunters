<?php

namespace App\Services;

use App\Support\SlugHelper;
use App\Support\JsonLoader;
use App\Support\SearchHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SkillService
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

    public function getPaginatedSkills(int $perPage = 20): LengthAwarePaginator
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

    public function findBySlug(string $slug): ?array
    {
        return $this->loadSkills()->firstWhere('slug', $slug);
    }
}
