@extends('adminlte::page')

@section('title', 'Atualizar Envio')

@section('content_header')
    <h1 class="m-0 text-dark">Atualizar Envio</h1>
@stop

@section('css')
    <style>
        .card-body::after, .card-footer::after, .card-header::after{
            display: none;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
    <script>
        $(function () {
            $('[name="date_time_post[]"]').mask('00/00/0000 00:00');
            $('[name="code_tracking[]"]').mask('SS000000000SS');
        })
        $('#btnNewTracking').on('click', function() {
            $('.tracking').append(`
                <div class="row d-flex justify-content-center">
                    <div class="form-group col-md-4">
                        <label>Código de rastreio</label>
                        <input type="text" class="form-control" name="code_tracking[]" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Data e hora da postagem (<i>dd/mm/yyyy hh:mm</i>)</label>
                        <input type="text" class="form-control" name="date_time_post[]" required>
                    </div>
                    <div class="form-group col-md-1">
                        <label>Remover</label>
                        <button class="btn btn-danger remove-tracking"><i class="fa fa-trash"></i></button>
                    </div>
                </div>
            `);
            $('[name="date_time_post[]"]').mask('00/00/0000 00:00');
            $('[name="code_tracking[]"]').mask('SS000000000SS');
        });
        $(document).on('click', '.remove-tracking', function(){
            $(this).closest('.row').remove();
        })
    </script>
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">

                <form action="{{ route('admin.orders.freight.update') }}" enctype="multipart/form-data" method="POST">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Atualizar Rastreio Pedido</h3>
                        <a target="_blank" class="btn btn-primary col-md-3" href="{{ route('admin.orders.freight.generateTag', ['id' => $id]) }}">GERAR ETIQUETA</a>
                    </div>
                    <div class="card-body">
                        <div class="tracking">
                            <div class="row d-flex justify-content-center">
                                <div class="form-group col-md-4">
                                    <label>Código de rastreio</label>
                                    <input type="text" class="form-control" name="code_tracking[]" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Data e hora da postagem (<i>dd/mm/yyyy hh:mm</i>)</label>
                                    <input type="text" class="form-control" name="date_time_post[]" required>
                                </div>
                                <div class="form-group col-md-1">
                                    &nbsp;
                                </div>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-center">
                            <div class="form-group col-md-4">
                               <button type="button" class="btn btn-primary col-md-12" id="btnNewTracking">Cadastrar Mais Código</button>
                            </div>
                            <div class="form-group col-md-1">
                                &nbsp;
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-success col-md-4"><i class="fa fa-truck"></i> Atualizar</button>
                            </div>
                        </div>
                    </div>
                    {!! csrf_field() !!}
                    <input type="hidden" name="order_id" value="{{ $id }}">
                </form>
            </div>
        </div>
    </div>
@stop
