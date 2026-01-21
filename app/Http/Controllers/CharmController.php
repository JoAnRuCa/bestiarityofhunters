<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class CharmController extends Controller
{
    private function loadCharms()
    {
        return collect(json_decode(Storage::get('data/charms.json'), true))
            ->filter(fn($item) => isset($item['ranks'][0]['name']))
            ->map(function ($item, $index) {

                $fullName = $item['ranks'][0]['name'];

                $parts = explode(' ', $fullName);
                $last = end($parts);

                $roman = ['I','II','III','IV','V','VI','VII'];

                if (in_array($last, $roman)) {
                    array_pop($parts);
                }

                $baseName = implode(' ', $parts);

                $item['id'] = $index;
                $item['slug'] = Str::slug($baseName);

                return $item;
            });
    }

    public function index()
    {
        $charms = $this->loadCharms();

        if (request()->filled('q')) {
            $q = strtolower(request()->get('q'));

            $charms = $charms->filter(function ($charm) use ($q) {
                return str_contains(strtolower($charm['ranks'][0]['name']), $q);
            });
        }

        $page = request()->get('page', 1);
        $perPage = 20;

        $paginatedCharm = new LengthAwarePaginator(
            $charms->forPage($page, $perPage),
            $charms->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return view('seccion.charms', compact('paginatedCharm'));
    }

    public function show($slug, $rank)
    {
        $charms = $this->loadCharms();

        $charm = $charms->firstWhere('slug', $slug);

        if (!$charm) {
            abort(404);
        }

        $selectedRank = collect($charm['ranks'])->firstWhere('level', intval($rank));

        return view('seccion.charmsShow', compact('charm', 'selectedRank'));
    }
}

