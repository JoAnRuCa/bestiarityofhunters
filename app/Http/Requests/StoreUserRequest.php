<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'name'     => ['required', 'string', 'max:255', 'unique:users,name,' . $userId],
            'email'    => ['required', 'email', 'unique:users,email,' . $userId],
            'password' => [$userId ? 'nullable' : 'required', 'min:8', 'confirmed'],
            'role'     => ['sometimes', 'required', 'in:user,admin'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Every hunter needs a name to be recognized in the guild.',
            'name.unique'       => 'That name is already taken by another hunter.',
            'email.required'    => 'We need an owl address (email) to send guild reports.',
            'email.unique'      => 'This owl address is already registered in our files.',
            'password.required' => 'A secure key is mandatory for new hunters.',
            'password.min'      => 'The secret key must be at least 8 characters long.',
            'password.confirmed'=> 'The keys do not match. Try again, hunter.',
            'role.in'           => 'Invalid rank. Choose between Hunter or Admin.',
        ];
    }
}
