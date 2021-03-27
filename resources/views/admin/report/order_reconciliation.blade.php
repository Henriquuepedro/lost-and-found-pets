@extends('adminlte::page')

@section('title', 'Conciliação de Pedidos')

@section('content_header')
    <h1 class="m-0 text-dark">Conciliação de Pedidos</h1>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        table.table-bordered.dataTable tbody th, table.table-bordered.dataTable tbody td{
            font-size: .8rem;
        }
        table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child:before,
        table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child:before{
            line-height: 15px;
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
            $('[data-toggle="tooltip"]').tooltip();
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
        })
    </script>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pedidos</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
{{--                            <th>Cliente</th>--}}
                            <th>Operação MP</th>
                            <th>Produto</th>
                            <th>Desconto</th>
                            <th>Frete</th>
                            <th>Pedido</th>
                            <th>Juros MP</th>
                            <th>Expectativa</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($arrOrders as $order)
                                <tr>
                                    <td data-order="{{ $order['id'] }}"><a href="{{ route('admin.orders.view', $order['id']) }}" target="_blank">{{ $order['id_view'] }}</a></td>
{{--                                    <td>{{ $animal['client'] }}</td>--}}
                                    <td><a href="https://www.mercadopago.com.br/activities/1?q={{ $order['transaction'] }}" target="_blank">{{ $order['transaction'] }}</a></td>
                                    <td data-order="{{ $order['gross_amount_order'] }}">{{ $order['gross_amount'] }}</td>
                                    <td data-order="{{ $order['discount_amount_order'] }}">{{ $order['discount_amount'] }}</td>
                                    <td data-order="{{ $order['value_ship_order'] }}">{{ $order['value_ship'] }}</td>
                                    <td data-order="{{ $order['net_amount_order'] }}">{{ $order['net_amount'] }}</td>
                                    <td data-order="{{ $order['fee_amount_order'] }}">{{ $order['fee_amount'] }}</td>
                                    <td data-order="{{ $order['expectancy_amount_order'] }}">{{ $order['expectancy_amount'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>#</th>
{{--                            <th>Cliente</th>--}}
                            <th>Operação MP</th>
                            <th>Produto</th>
                            <th>Desconto</th>
                            <th>Frete</th>
                            <th>Pedido</th>
                            <th>Juros MP</th>
                            <th>Expectativa</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
@stop
