<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permitir a cualquier usuario logeado
    }

    public function rules()
    {
        $user = Auth::user();
        $type = $this->input('type');

        if ($type === 'name') {
            return [
                'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            ];
        }

        if ($type === 'email') {
            return [
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            ];
        }

        if ($type === 'password') {
            return [
                'current_password' => ['required'],
                'new_password' => ['required', 'string', 'min:8'],
            ];
        }

        return [];
    }

    public function messages()
    {
        return [
            'name.unique' => 'This nickname is already taken by another hunter.',
            'email.unique' => 'This email address is already registered.',
            'new_password.min' => 'The new password must be at least 8 characters.',
            'current_password.required' => 'You must provide your current password.',
        ];
    }
}
