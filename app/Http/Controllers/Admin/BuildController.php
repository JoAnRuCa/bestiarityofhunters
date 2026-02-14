<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Build;
use App\Models\Tag;
use Illuminate\Http\Request;

class BuildController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $builds = Build::with(['user', 'tags'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('titulo', 'LIKE', "%{$search}%")
                      ->orWhere('playstyle', 'LIKE', "%{$search}%")
                      ->orWhereHas('user', function ($u) use ($search) {
                          $u->where('name', 'LIKE', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->get(); // Cambiado a get() para seguir el estilo de tus Guías

        return view('admin.builds.index', compact('builds', 'search'));
    }

    public function create()
    {
        return view('admin.builds.create');
    }

    public function edit(Build $build)
    {
        $build->load(['tags', 'user']);
        return view('admin.builds.edit', compact('build'));
    }

    public function destroy(Build $build)
    {
        $build->delete();
        return redirect()->route('admin.builds.index')
                         ->with('success', 'Build eliminada de los registros.');
    }
}