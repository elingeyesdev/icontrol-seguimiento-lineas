@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('ruta.index') }}" class="text-decoration-none">Rutas</a></li>
                    <li class="breadcrumb-item active">Editar: {{ $ruta->nombre }}</li>
                </ol>
            </nav>

            <form method="POST" action="{{ route('ruta.update', $ruta->id) }}">
                @csrf
                @method('PATCH')
                @include('ruta.form')
            </form>
        </div>
    </div>
</div>
@endsection
