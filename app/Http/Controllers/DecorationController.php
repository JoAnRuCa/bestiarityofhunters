<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class DecorationController extends Controller
{
    private function loadDecorations()
    {
        return collect(json_decode(Storage::get('data/decorations.json'), true))
            ->map(function ($item, $index) {
                $item['id'] = $index;
                $item['slug'] = Str::slug($item['name']);
                return $item;
            });
    }

    public function index()
    {
        $decorations = $this->loadDecorations();

        if (request()->filled('q')) {
            $q = request()->get('q');
            $decorations = $decorations->filter(function ($decoration) use ($q) {
                return str_contains(
                    strtolower($decoration['name']),
                    strtolower($q)
                );
            });
        }

        $page = request()->get('page', 1);
        $perPage = 20;

        $paginatedDecorations = new LengthAwarePaginator(
            $decorations->forPage($page, $perPage),
            $decorations->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return view('seccion.decorations', compact('paginatedDecorations'));
    }

    public function show($slug)
    {
        $decorations = $this->loadDecorations();
        $decoration = $decorations->firstWhere('slug', $slug);

        if (!$decoration) {
            abort(404);
        }

        return view('seccion.decorationsShow', compact('decoration'));
    }
}
