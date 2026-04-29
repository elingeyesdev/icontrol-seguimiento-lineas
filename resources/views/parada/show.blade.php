@extends('layouts.app')

@section('template_title')
    {{ $parada->name ?? __('Show') . " " . __('Parada') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Parada</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('paradas.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $parada->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Ubicacion:</strong>
                                    {{ $parada->ubicacion }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
