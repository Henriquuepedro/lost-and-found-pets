@extends('adminlte::page')

@section('title', 'Emissão de Etiquetas')

@section('content_header')
    <h1 class="m-0 text-dark">Emissão de Etiquetas</h1>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        table.table-bordered.dataTable tbody th, table.table-bordered.dataTable tbody td{
            font-size: .8rem;
        }
        .card-body::after, .card-footer::after, .card-header::after{
            display: none;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        $(function () {
            $("#example1").DataTable({
                "responsive": true,
                "autoWidth": false,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Portuguese-Brasil.json"
                },
                "order": [[ 0, 'desc' ]]
            });
        });

        $('.btnOrderReceived').on('click', function () {
            const order_id      = $(this).closest('tr').attr('animal-id');
            const order_name    = $(this).closest('tr').find('td:eq(0)').text();
            const client_name   = $(this).closest('tr').find('td:eq(1)').text();

            $('#modal-received h5.client-animal').text(order_name + ' - ' + client_name);
            $('#modal-received button.btnSendOrderReceived').val(order_id);
            $('#modal-received').modal();
        })

        $('.btnOrderCancel').on('click', function () {
            const order_id      = $(this).closest('tr').attr('animal-id');
            const order_name    = $(this).closest('tr').find('td:eq(0)').text();
            const client_name   = $(this).closest('tr').find('td:eq(1)').text();

            $('#modal-cancel h5.client-animal').text(order_name + ' - ' + client_name);
            $('#modal-cancel button.btnSendOrderCancel').val(order_id);
            $('#modal-cancel').modal();
        });
        $('#genereateTags').on('click', function () {
            let orders = [];
            if($('.tag:checked').length == 0){
                alert("Selecione pelo menos um pedido!");
                return false;
            }

            $('.tag:checked').each(function () {
                orders.push($(this).closest('tr').attr('animal-id'));
            });

            window.open("./envios/etiqueta/" + orders.join('-'), '_blank');
        })
        $('#AllTags').on('click', function () {
            const status = $(this).is(':checked');
            $('.tag').each(function () {
                $(this).prop('checked', status);
            });
        });
    </script>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success mt-2">{{session('success')}}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-danger mt-2">{{session('warning')}}</div>
            @endif
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Etiqueas Para Emissão</h3>
                    <button id="genereateTags" class="btn btn-primary col-md-3"><i class="fas fa-ticket-alt"></i> Gerar Selecionados</button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Todos <input type="checkbox" id="AllTags"></th>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Transação MercadoPago</th>
                            <th>Itens</th>
                            <th>Realizado Em</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($arrOrders as $order)
                            <tr order-id="{{ $order['id'] }}">
                                <td class="text-center"><input type="checkbox" class="tag"></td>
                                <td data-order="{{ $order['id'] }}" class="text-center">{{ $order['id_view'] }}</td>
                                <td>{{ $order['name'] }}</td>
                                <td>{{ $order['tel'] }}</td>
                                <td>{{ $order['id_trans'] }}</td>
                                <td>{{ $order['item_count'] }}</td>
                                <td data-order="{{ $order['datetime_order'] }}">{{ $order['created_at'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th></th>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Transação MercadoPago</th>
                            <th>Itens</th>
                            <th>Realizado Em</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-received" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.orders.received') }}" method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h4 class="modal-title">Entregar</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <h5 class="font-weight-bold">Você tem certeza que deseja marcar o pedido como entregue? </h5>
                            <h5 class="client-order mb-3 mt-3"></h5>
                            <p>Use essa tela para entregas antecipadas do prazo previsto ou caso o cliente não atualizou o pedido!</p>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-primary col-md-5" data-dismiss="modal">Cancelar operação</button>
                            <button type="submit" class="btn btn-success btnSendOrderReceived col-md-5" name="order_id" value="">Marcar Como Entregue</button>
                        </div>
                        {!! csrf_field() !!}
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-cancel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.orders.cancel') }}" method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h4 class="modal-title">Cancelar</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <h5 class="font-weight-bold">Você tem certeza que deseja cancelar o pedido? </h5>
                            <h5 class="client-order mb-3 mt-3"></h5>
                            <p>Ao cancelar um pedido, o cliente verá em sua conta o pedido como cancelado!</p>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-primary col-md-5" data-dismiss="modal">Fechar</button>
                            <button type="submit" class="btn btn-danger btnSendOrderCancel col-md-5" name="order_id" value="">Cancelar Pedido</button>
                        </div>
                        {!! csrf_field() !!}
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
