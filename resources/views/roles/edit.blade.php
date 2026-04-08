<!-- resources/views/roles/edit.blade.php -->
<h1>Editar Rol: {{ $role->name }}</h1>

<form action="{{ route('roles.update', $role->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Nombre del Rol:</label>
    <input type="text" name="name" value="{{ $role->name }}" required>

    <h3>Permisos:</h3>
    @foreach($permissions as $permission)
        <div>
            <label>
                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                    {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                {{ $permission->name }}
            </label>
        </div>
    @endforeach

    <br>
    <button type="submit">Actualizar Rol</button>
    <a href="{{ route('roles.index') }}">Cancelar</a>
</form>