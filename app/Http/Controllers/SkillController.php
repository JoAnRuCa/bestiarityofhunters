<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class SkillController extends Controller
{
    public function index()
    {
        // 1. Cargar JSON y asignar IDs fijos
        $skills = collect(json_decode(Storage::get('data/skills.json'), true))->map(function ($item, $index) {
            $item['id'] = $index;
            $item['slug'] = Str::slug($item['name']);
            return $item;
        });

        // 2. Filtrar si hay búsqueda
        if (request()->has('q')) {
            $q = request()->get('q');
            $skills = $skills->filter(function ($skill) use ($q) {
                return str_contains(strtolower($skill['name']), strtolower($q));
            });
        }
        // 3. Paginar 20 por página
        $page = request()->get('page', 1);
        $perPage = 20;
        $paginatedSkills = new LengthAwarePaginator(
            $skills->forPage($page, $perPage),
            $skills->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        return view('seccion.skills', compact('paginatedSkills'));
    }

    public function show($slug)
    {
        $skills = collect(json_decode(Storage::get('data/skills.json'), true))->map(function ($item, $index) {
            $item['id'] = $index;
            $item['slug'] = Str::slug($item['name']);
            return $item;
        });
        $skill = $skills->firstWhere('slug', $slug);
        return view('seccion.skillsShow', compact('skill'));
    }
}
