<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $roles = Role::where('estado', '!=', 'inactivo')
            ->when($buscar, function ($query, $buscar) {
                return $query->where('name', 'LIKE', '%'.$buscar.'%');
            })
            ->paginate(10);

        return view('roles.index', compact('roles', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();

        return view('roles.create', compact('permissions'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
        ]);

        Role::create([
            'name' => $request->name,
            'descripcion' => $request->descripcion,
            'estado' => $request->estado,
            'guard_name' => 'web',
        ]);

        return redirect()->route('roles.index')->with('success', 'Rol creado con éxito');
    }

    public function show(string $id)
    {
        // Este método no se usa actualmente. Se puede habilitar para mostrar detalles del rol.
    }

    public function assignUsers(Request $request)
    {
        $buscar = $request->input('buscar');

        $usersQuery = User::with('roles');
        if ($buscar) {
            $usersQuery->where(function ($query) use ($buscar) {
                $query->where('name', 'LIKE', "%{$buscar}%")
                    ->orWhere('email', 'LIKE', "%{$buscar}%");
            });
        }
        $users = $usersQuery
            ->orderBy('name')
            ->paginate(12)
            ->appends(['buscar' => $buscar]);

        return view('roles.assign', compact('users', 'buscar'));
    }

    public function createUserRoleAssignment()
    {
        $allUsers = User::orderBy('name')->get();
        $roles = Role::where('estado', '!=', 'inactivo')->orderBy('name')->get();

        return view('roles.assign-form', compact('allUsers', 'roles'));
    }

    public function editUserRoleAssignment(string $id)
    {
        $edit_user = User::with('roles')->findOrFail($id);
        $allUsers = User::orderBy('name')->get();
        $roles = Role::where('estado', '!=', 'inactivo')->orderBy('name')->get();

        return view('roles.assign-form', compact('edit_user', 'allUsers', 'roles'));
    }

    public function storeUserRoles(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->syncRoles($request->input('roles', []));

        return redirect()->route('roles.assign')->with('success', 'Roles asignados correctamente al usuario.');
    }

    public function destroyUserRoles($id)
    {
        $user = User::findOrFail($id);
        $user->syncRoles([]);

        return redirect()->route('roles.assign')->with('info', 'Roles eliminados del usuario.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findById($id); // Corregido: Necesitamos buscar el rol antes de enviarlo a la vista
        $permissions = Permission::all();

        return view('roles.edit', compact('role', 'permissions'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findById($id);

        $request->validate([
            'name' => "required|unique:roles,name,$id",
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $role->update([
            'name' => $request->name,
            'descripcion' => $request->descripcion,
            'estado' => $request->estado,
        ]);

        return redirect()->route('roles.index')->with('success', 'Rol actualizado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findById($id);
        $role->delete();

        return redirect()->route('roles.index')->with('info', 'Rol eliminado con éxito');

    }
}
