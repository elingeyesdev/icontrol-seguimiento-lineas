@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center"><div class="col-lg-10">
        <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('micro.index') }}" class="text-decoration-none">Micros</a></li>
            <li class="breadcrumb-item active">Editar: {{ $micro->placa }}</li>
        </ol></nav>
        <form method="POST" action="{{ route('micro.update', $micro->id) }}">@csrf @method('PATCH') @include('micro.form')</form>
    </div></div>
</div>
@endsection
