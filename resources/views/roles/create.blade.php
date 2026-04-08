<h1>Crear Rol</h1>

<form action="{{ route('roles.store') }}" method="POST">
    @csrf
    <label>Nombre del Rol:</label>
    <input type="text" name="name" required>

    <h3>Permisos para este rol:</h3>
    @foreach($permissions as $permission)
        <div>
            <label>
                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}">
                {{ $permission->name }}
            </label>
        </div>
    @endforeach

    <br>
    <button type="submit">Guardar Rol</button>
    <a href="{{ route('roles.index') }}">Cancelar</a>
</form>