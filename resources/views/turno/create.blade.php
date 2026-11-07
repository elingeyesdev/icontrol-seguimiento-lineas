@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-7">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('turno.index') }}" class="text-decoration-none">Turnos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Registrar turno</li>
                </ol>
            </nav>

            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="bi bi-clock" aria-hidden="true"></i>Nuevo Turno</h1>
                    <p class="page-subtitle">Registre un nuevo horario en el catálogo.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('turno.store') }}">
                        @csrf
                        @include('turno.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
