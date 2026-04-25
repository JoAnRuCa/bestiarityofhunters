<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        return view('seccion.profile', ['user' => Auth::user()]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $type = $request->type;

        if ($type === 'name') {
            $user->name = $request->name;
        } 
        
        elseif ($type === 'email') {
            $user->email = $request->email;
        } 
        
        elseif ($type === 'password') {
            // Comprobamos si la contraseña actual coincide
            if (!Hash::check($request->current_password, $user->password)) {
                return back()
                    ->withErrors(['current_password' => 'The current password you entered is incorrect.'])
                    ->withInput(); // Importante para que el modal se mantenga abierto
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}