<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SeccionController extends Controller
{
    public function index()
    {
        return view('seccion.index');
    }
    public function create()
    {
        return view('seccion.create');
    }
    public function show($seccion)
    {
        return view('seccion.show', compact('seccion'));
    }

}
