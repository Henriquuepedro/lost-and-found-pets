@extends('adminlte::page')

@section('title', 'Listagem de Cupons')

@section('content_header')
    <h1 class="m-0 text-dark">Cupons de Produtos</h1>
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
        table.dataTable tr td:last-child{
            padding: 4px 0px;
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
                "order": [[ 1, 'asc' ]]
            });
            $('[data-toggle="tooltip"]').tooltip();
        });

        $('.btnRequestDeleteCoupon').on('click', function () {
            const product_id    = $(this).closest('tr').attr('coupon-id');
            const product_name  = $(this).closest('tr').find('td:eq(1)').text();

            $('#modal-delete h5.name-coupon').text(product_name);
            $('#modal-delete  button.btnDeleteCoupon').val(product_id);
            $('#modal-delete').modal();
        })
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
                    <h3 class="card-title">Cupons Cadastrados</h3>
                    <a href="{{ route('admin.coupon.new') }}" class="btn btn-primary col-md-3"><i class="fa fa-plus"></i> Novo Cupom</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>Expira em</th>
                            <th>Percentual</th>
                            <th>Criado Em</th>
                            <th>Ação</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($arrCoupons as $coupon)
                                <tr coupon-id="{{ $coupon['id'] }}">
                                    <td>{{ $coupon['id'] }}</td>
                                    <td>{{ $coupon['name'] }}</td>
                                    <td>{{ $coupon['date_expired'] }}</td>
                                    <td>{{ $coupon['percentage'] }} %</td>
                                    <td data-order="{{ $coupon['datetime_order'] }}">{{ $coupon['created_at'] }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.coupon.edit', ['id' => $coupon['id']]) }}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Alterar"><i class="fas fa-edit"></i></a>
                                        <button class="btn btn-danger btnRequestDeleteCoupon btn-sm" data-toggle="tooltip" title="Excluir"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>Expira em</th>
                            <th>Percentual</th>
                            <th>Criado Em</th>
                            <th>Ação</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-delete" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.coupon.delete') }}" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h4 class="modal-title">Excluir Cupom</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <h5 class="text-danger font-weight-bold">Você tem certeza que deseja remover o cupom: </h5>
                        <h5 class="text-danger name-coupon"></h5>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-primary col-md-5" data-dismiss="modal">Cancelar operação</button>
                        <button type="submit" class="btn btn-danger btnDeleteCoupon col-md-5" name="coupon_id" value="">Excluir permanentemente</button>
                    </div>
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>
@stop
