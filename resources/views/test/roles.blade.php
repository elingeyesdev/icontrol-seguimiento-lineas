<x-app-layout>
    <div class="container">
        <h1>Prueba de Roles - Spatie</h1>

        @role('admin')
            <p style="color: green; font-weight: bold;">
                Soy ADMIN
            </p>
        @endrole

        @role('conductor')
            <p style="color: blue; font-weight: bold;">
                Soy Conductor
            </p>
        @endrole

        @hasanyrole('admin|conductor')
            <p>Tengo al menos un rol</p>
        @endhasanyrole

        @unlessrole('admin')
            <p>No soy administrador</p>
        @endunlessrole

    </div>
</x-app-layout>