<!-- resources/views/roles/index.blade.php -->
<h1>Lista de Roles</h1>
<a href="{{ route('roles.create') }}">Crear Nuevo Rol</a>

@if(session('info'))
    <div style="color: green;">{{ session('info') }}</div>
@endif
<div class="mb-3">
    <form action="{{ route('roles.index') }}" method="GET" class="d-flex">
        <input type="text" name="buscar" class="form-control me-2" 
               placeholder="Buscar rol por nombre..." value="{{ $buscar }}">
        <button type="submit" class="btn btn-outline-primary">Buscar</button>
        
        @if($buscar)
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary ms-2">Limpiar</a>
        @endif
    </form>
</div>

<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>{{ $role->name }}</td>
                <td>
                    <a href="{{ route('roles.edit', $role->id) }}">Editar</a>
                    
                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('¿Eliminar rol?')">Eliminar</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>