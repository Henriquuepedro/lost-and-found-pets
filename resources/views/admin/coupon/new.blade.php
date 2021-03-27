@extends('adminlte::page')

@section('title', 'Cria Cupom')

@section('content_header')
    <h1 class="m-0 text-dark">Cria Cupom</h1>
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
            $('input[name="percentage"]').mask('#.##0,00', {
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

            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Alterar Cupom</h3>
                </div>
                <form action="{{ route('admin.coupon.insert') }}" enctype="multipart/form-data" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">Nome do Cupom</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Digite o nome do cupom" required>
                                <small>O nome do cupom ficará sempre em maiúsculo</small>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="value">Data de Expiração (<i>dd/mm/yyyy</i>)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text font-weight-bold">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="date" class="form-control" id="date_exp" name="date_exp" value="{{ old('date_exp') }}" placeholder="Digite a data de expiração" required>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="value">Percentual</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="percentage" name="percentage" value="{{ old('percentage') }}" placeholder="Digite o percentual" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold">
                                          %
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="form-group col-md-12 mt-5">
                                <h5>Selecione os produtos que irão participar da promoção</h5>
                            </div>
                            @foreach( $products_register as $product )
                            <div class="form-group col-md-4">
                                <label for="width">{{$product['name']}}</label></br>
                                <input type="checkbox" data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="SIM"  data-off-text="NÃO"  name="coupon[]" value="{{$product['id']}}">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('admin.coupons') }}" class="btn btn-danger col-md-3"><i class="fas fa-arrow-left"></i> Voltar</a>
                                <button type="submit" class="btn btn-success col-md-3"><i class="fa fa-save"></i> Salvar</button>
                            </div>
                        </div>
                    </div>
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>
@stop
