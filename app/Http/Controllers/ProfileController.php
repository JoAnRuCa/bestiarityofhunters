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
            // La validación de formato ya pasó, ahora comprobamos la veracidad
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The current password you entered is incorrect.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}
