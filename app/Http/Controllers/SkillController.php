<?php

namespace App\Http\Controllers;

use App\Services\SkillService;

class SkillController extends Controller
{
    public function index(SkillService $service)
    {
        return view('seccion.skills', [
            'paginatedSkills' => $service->getPaginatedSkills()
        ]);
    }

    public function show($slug, SkillService $service)
    {
        $skill = $service->findBySlug($slug);

        if (!$skill) abort(404);

        return view('seccion.skillsShow', compact('skill'));
    }
}
