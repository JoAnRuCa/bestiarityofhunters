<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
    public function store(Request $request)
    {
        $request->validate([
            // Añadimos unique:users,name para evitar duplicados
            'name'     => 'required|string|max:255|unique:users,name',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:user,admin',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Nuevo cazador registrado con éxito.');
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
    public function update(Request $request, User $user)
    {
        $request->validate([
            // El tercer parámetro ,'.$user->id indica que ignore el nombre de este usuario específico
            'name'     => 'required|string|max:255|unique:users,name,' . $user->id,
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')
                         ->with('success', "Los archivos de {$user->name} han sido actualizados.");
    }

    /**
     * Expulsar a un cazador del gremio.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'No puedes auto-expulsarte.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
                         ->with('success', 'Registro eliminado de los archivos.');
    }
}