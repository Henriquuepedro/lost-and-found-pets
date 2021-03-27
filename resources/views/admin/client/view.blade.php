@extends('adminlte::page')

@section('title', 'Informações do Cliente')

@section('content_header')
    <h1 class="m-0 text-dark">Informações do Cliente</h1>
@stop

@section('css')
@stop

@section('js')
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Dados do Cliente</h3>
                </div>
                <div class="card-body">
                    <div class="row d-flex justify-content-between flex-wrap">
                        <div class="form-group col-md-3 col-xs-12">
                            <label>Data de Cadastro</label>
                            <input type="text" class="form-control" value="{{ $dataClient['created_at'] }}" readonly>
                        </div>
                        <div class="form-group col-md-3 col-xs-12">
                            <label>Data de Alteração</label>
                            <input type="text" class="form-control" value="{{ $dataClient['updated_at'] }}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-7">
                            <label>Nome</label>
                            <input type="text" class="form-control" value="{{ $dataClient['name'] }}" readonly>
                        </div>
                        <div class="form-group col-md-5">
                            <label>E-mail</label>
                            <input type="text" class="form-control" value="{{ $dataClient['email'] }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-default">
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ route('admin.clients') }}" class="btn btn-danger col-md-3"><i class="fas fa-arrow-left"></i> Voltar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
