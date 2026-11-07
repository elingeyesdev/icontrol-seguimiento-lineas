@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('parada.index') }}" class="text-decoration-none">Paradas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar: {{ $parada->nombre }}</li>
                </ol>
            </nav>

            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="bi bi-geo-alt" aria-hidden="true"></i>Editar Parada</h1>
                    <p class="page-subtitle">Actualice la información de la parada.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('parada.update', $parada->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('parada.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
