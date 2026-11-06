@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('asignacion-turno.index') }}" class="text-decoration-none">Asignación de turnos</a></li>
            <li class="breadcrumb-item"><a href="{{ route('asignacion-turno.show', $asignacion->id) }}" class="text-decoration-none">AT-{{ str_pad($asignacion->id, 3, '0', STR_PAD_LEFT) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
    </nav>

    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                Editar asignación de turno
            </h1>
            <p class="page-subtitle">Asignación del {{ \Carbon\Carbon::parse($asignacion->fecha)->format('d/m/Y') }}.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('asignacion-turno.update', $asignacion->id) }}">
                        @csrf
                        @method('PATCH')
                        @include('asignacion_turno.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
