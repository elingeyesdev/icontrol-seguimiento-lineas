<?php

namespace App\Http\Controllers;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    // Usamos 'buscar' porque así lo nombraste en el input de la vista
    $buscar = $request->input('buscar');

        $roles = Role::where('name', 'LIKE', '%' . $buscar . '%')->paginate(10);

        // Pasamos 'roles' y 'buscar' a la vista
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
        $request->validate(['name'=>'required|unique:roles,name','permissions'=>'array']);
        $role = Role::create(['name'=>$request->name]);
        $role->syncPermissions($request->permissions);
        
        return redirect()->route('roles.index')->with('info','Rol creado con exito');
    }
   
    
    public function show(string $id)
    {
      
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
            'name' => "required|unique:roles,name,$id", // Permite el mismo nombre si es el mismo registro
            'permissions' => 'array'
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('info', 'Rol actualizado con éxito');
   
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
