@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('asignacion-turno.index') }}" class="text-decoration-none">Asignación de turnos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nueva asignación</li>
        </ol>
    </nav>

    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-calendar2-plus" aria-hidden="true"></i>
                Nueva asignación de turno
            </h1>
            <p class="page-subtitle">Asigna conductor, micro, ruta y turno para una fecha específica.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('asignacion-turno.store') }}">
                        @csrf
                        @include('asignacion_turno.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
