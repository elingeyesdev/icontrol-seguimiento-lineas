@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('rutas-paradas.index') }}" class="text-decoration-none">Rutas y Paradas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar asociación</li>
                </ol>
            </nav>

            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="bi bi-diagram-3" aria-hidden="true"></i>Editar Asociación</h1>
                    <p class="page-subtitle">Modifique los datos de la asociación ruta-parada.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('rutas-paradas.update', $rutaParada->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('rutas_paradas.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
