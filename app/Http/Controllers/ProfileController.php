<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // 'seccion' es la carpeta, 'profile' es el archivo .blade.php
        return view('seccion.profile', compact('user'));
    }
    public function update(Request $request)
    {
    $user = Auth::user();
    $type = $request->type;

    if ($type === 'name') {
        $user->update(['name' => $request->name]);
    } 
    elseif ($type === 'email') {
        $user->update(['email' => $request->email]);
    } 
    elseif ($type === 'password') {
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no coincide']);
        }
        $user->update(['password' => Hash::make($request->new_password)]);
    }

    return back()->with('success', 'Profile updated successfully!');
    }
}
