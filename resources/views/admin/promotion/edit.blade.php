@extends('adminlte::page')

@section('title', 'Alterar Promoção')

@section('content_header')
    <h1 class="m-0 text-dark">Alterar Promoção</h1>
@stop

@section('css')
@stop

@section('js')
    <script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <script src="https://raw.githubusercontent.com/RobinHerbots/Inputmask/3.3.3/js/jquery.inputmask.js"></script>
    <script src="https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js"></script>
    <script>
        $(function () {
            $("input[data-bootstrap-switch]").each(function(){
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });
            $('input[name="value"]').mask('#.##0,00', {
                reverse: true
            });

        });
    </script>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="error-form alert alert-warning {{ count($errors) == 0 ? 'display-none' : '' }}">
                <h5>Existem erros no envio do formulário, veja abaixo para corrigi-los.</h5>
                <ol>
                    @foreach($errors->all() as $error)
                        <li><label id="name-error" class="error">{{ $error }}</label></li>
                    @endforeach
                </ol>
            </div>
            @if(session('success'))
                <div class="alert alert-success mt-2">{{session('success')}}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-danger mt-2">{{session('warning')}}</div>
            @endif

            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Alterar Promoção</h3>
                </div>
                <form action="{{ route('admin.promotion.update') }}" enctype="multipart/form-data" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">Tipo da Promoção</label>
                                <select name="type" class="form-control" required>
                                    <option value="">Selecione um tipo</option>
                                    <option {{ old() ? (old('type') == 1 ? 'selected' : '') : ($promotion['type'] == 1 ? 'selected' : '') }} value="1">Frete Grátis - Frete menor que X reais</option>
                                    <option {{ old() ? (old('type') == 2 ? 'selected' : '') : ($promotion['type'] == 2 ? 'selected' : '') }} value="2">Frete Grátis - Pedido maior que X reais (PAC)</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="value">Valor</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text font-weight-bold">
                                            R$
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="value" name="value" value="{{ old() ? old('value') : $promotion['value'] }}" placeholder="Digite o valor" required>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="value">Situação</label>
                                <div class="form-group col-md-12">
                                    <input type="checkbox" data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="ATIVO"  data-off-text="INATIVO" name="active" {{ old() ? (old('active') == "on" ? 'checked' : '') : ($promotion['active'] == 1 ? 'checked' : '') }}>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('admin.promotions') }}" class="btn btn-danger col-md-3"><i class="fas fa-arrow-left"></i> Voltar</a>
                                <button type="submit" class="btn btn-success col-md-3"><i class="fa fa-save"></i> Salvar</button>
                            </div>
                        </div>
                    </div>
                    {!! csrf_field() !!}
                    <input type="hidden" name="promotion_id" value="{{$promotion['id']}}">
                </form>
            </div>
        </div>
    </div>
@stop
