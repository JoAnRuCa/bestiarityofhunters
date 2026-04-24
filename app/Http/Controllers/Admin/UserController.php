<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 

class UserController extends Controller
{
    /**
     * Lista de todos los cazadores del gremio.
     */
        public function index(Request $request)
        {
            $search = $request->input('search');

            $users = User::when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
            })->get();

            return view('admin.users.index', compact('users', 'search'));
        }
    /**
     * Formulario para registrar un nuevo cazador.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Guardar un nuevo cazador (Nombre e Email ÚNICOS).
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'New hunter registered successfully.');
    }

    /**
     * Formulario para editar un cazador.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Actualizar los datos (Validando unicidad excepto para el usuario actual).
     */
    public function update(StoreUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')
                         ->with('success', "The files of {$user->name} have been updated.");
    }

    /**
     * Expulsar a un cazador del gremio.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot expel yourself.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
                         ->with('success', 'Record deleted from the files.');
    }
}