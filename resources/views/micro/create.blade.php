@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-dark">Crear Nuevo Micro</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('micro.store') }}" role="form" enctype="multipart/form-data">
                        @csrf
                        @include('micro.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
