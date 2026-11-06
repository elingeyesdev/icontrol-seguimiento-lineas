@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center"><div class="col-lg-8">
        <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('interno.index') }}" class="text-decoration-none">Internos</a></li>
            <li class="breadcrumb-item active">Editar: {{ $interno->numero_interno }}</li>
        </ol></nav>
        <form method="POST" action="{{ route('interno.update', $interno->id) }}">@csrf @method('PATCH') @include('interno.form')</form>
    </div></div>
</div>
@endsection
