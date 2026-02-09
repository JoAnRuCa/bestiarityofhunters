<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // 'seccion' es la carpeta, 'profile' es el archivo .blade.php
        return view('seccion.profile', compact('user'));
        }
}
