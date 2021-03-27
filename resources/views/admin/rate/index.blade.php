@extends('adminlte::page')

@section('title', 'Listagem de Avaliações')

@section('content_header')
    <h1 class="m-0 text-dark">Listagem de Avaliações</h1>
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
                "order": [[ 4, 'asc' ]]
            });
            $('[data-toggle="tooltip"]').tooltip();
        });

        $('.btnRequestChangeRate').on('click', function () {
            const rate_id   = $(this).closest('tr').attr('rate-id');
            const title     = $(this).closest('tr').find('td:eq(0)').text();
            const name      = $(this).closest('tr').find('td:eq(1)').text();
            const status    = $(this).closest('tr').find('td:eq(4)').attr('data-order');
            console.log(status);

            $('#modal-change h5.name-product').text(name);
            $('#modal-change h5.title-rate').text(title);
            $('#modal-change  button.btnChangeRate').val(rate_id);
            $('#modal-change  button.btnChangeRate').removeClass(status == 1 ? 'btn-success' : 'btn-danger');
            $('#modal-change  button.btnChangeRate').addClass(status == 0 ? 'btn-success' : 'btn-danger');
            $('#modal-change  button.btnChangeRate').text(status == 0 ? 'Aprovar' : 'Recusar');
            $('#modal-change  button.btnChangeRate').val(rate_id);
            $('#modal-change').modal();
        });

        $('.btnRequestDeleteRate').on('click', function () {
            const rate_id   = $(this).closest('tr').attr('rate-id');
            const title     = $(this).closest('tr').find('td:eq(0)').text();
            const name      = $(this).closest('tr').find('td:eq(1)').text();

            $('#modal-delete h5.name-product').text(name);
            $('#modal-delete h5.title-rate').text(title);
            $('#modal-delete  button.btnDeleteRate').val(rate_id);
            $('#modal-delete').modal();
        });

        $(document).on('click', '.btnRequestViewRate', function () {
            const rate_id   = $(this).closest('tr').attr('rate-id');
            const modal     = $('#modal-view');
            const status    = $(this).closest('tr').find('td:eq(4)').attr('data-order');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "./queries/ajax/viewRate",
                data: { rate_id },
                dataType: 'json',
                success: response => {
                    modal.find('#status-view').val(response.status);
                    modal.find('#date-view').val(response.created_at);

                    modal.find('#title-view').val(response.title);
                    modal.find('#description-view').val(response.description);
                    modal.find('#rate-view').val(response.rate);

                    modal.find('#order-view').attr('href', response.order_url);
                    modal.find('#order-view').text('Pedido: ' + response.order_id);

                    modal.find('#product-view').attr('href', response.product_url);
                    modal.find('#product-view').text(response.product);

                    modal.find('#user-view').attr('href', response.user_url);
                    modal.find('#user-view').text(response.user);

                    modal.modal();
                }, error: () => {
                    Toast.fire({
                        icon: 'error',
                        title: "Acorreu um problema, caso o problema persistir contate o suporte"
                    })
                }
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
                    <h3 class="card-title">Avaliações Realizadas</h3>
                    <a href="{{ route('admin.rate.new') }}" class="btn btn-primary col-md-3"><i class="fa fa-plus"></i> Nova Avaliação</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Título</th>
                            <th>Produto</th>
                            <th>Pedido</th>
                            <th>Avaliação</th>
                            <th>Situação</th>
                            <th>Criado Em</th>
                            <th>Ação</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($arrRates as $rate)
                                <tr rate-id="{{ $rate['id'] }}">
                                    <td>{{ $rate['title'] }}</td>
                                    <td>{{ $rate['product'] }}</td>
                                    <td>{{ $rate['order'] }}</td>
                                    <td>{{ $rate['rate'] }}</td>
                                    <td data-order="{{ $rate['status_order'] }}">{!! $rate['status'] !!}</td>
                                    <td data-order="{{ $rate['datetime_order'] }}">{{ $rate['created_at'] }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-primary btnRequestViewRate btn-sm" data-toggle="tooltip" title="Visualizar"><i class="fas fa-eye"></i></button>
                                        @if($rate['order'] == "ADM")
                                            <a href="{{ route('admin.rate.edit', ['id' => $rate['id']]) }}" class="btn btn-warning text-white btn-sm" data-toggle="tooltip" title="Alterar"><i class="fa fa-edit"></i></a>
                                        @else
                                        <button class="btn btn-{{ $rate['status_order'] == 1 ? 'danger' : 'success' }} btnRequestChangeRate btn-sm" data-toggle="tooltip" title="{{ $rate['status_order'] == 1 ? 'Recusar' : 'Aprovar' }}"><i class="fas fa-{{ $rate['status_order'] == 1 ? 'times' : 'check' }}"></i></button>
                                        @endif
                                        <button class="btn btn-danger btnRequestDeleteRate btn-sm" data-toggle="tooltip" title="Excluir"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Título</th>
                            <th>Produto</th>
                            <th>Pedido</th>
                            <th>Avaliação</th>
                            <th>Situação</th>
                            <th>Criado Em</th>
                            <th>Ação</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-change" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.rate.change') }}" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h4 class="modal-title">Alterar Status de Avaliação</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <h5 class="name-product"></h5>
                        <h5 class="title-rate mt-3"></h5>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-primary col-md-5" data-dismiss="modal">Cancelar operação</button>
                        <button type="submit" class="btn btn-danger btnChangeRate col-md-5" name="rate_id" value=""></button>
                    </div>
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-delete" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.rate.delete') }}" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h4 class="modal-title">Excluir Avaliação</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <h5 class="name-product"></h5>
                        <h5 class="title-rate mt-3"></h5>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-primary col-md-5" data-dismiss="modal">Cancelar operação</button>
                        <button type="submit" class="btn btn-danger btnDeleteRate col-md-5" name="rate_id" value="">Excluir permanentemente</button>
                    </div>
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-view" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Visualizar Avaliação</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Data da avaliação</label>
                            <input type="text" class="form-control" id="date-view" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Situação</label>
                            <input type="text" class="form-control" id="status-view" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Título</label>
                            <input type="text" class="form-control" id="title-view" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Descrição</label>
                            <textarea class="form-control" rows="3" id="description-view" readonly></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label>Pedido</label><br>
                            <a href="" target="_blank" class="btn btn-primary col-md-12" id="order-view" readonly>Ver Pedido</a>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Avaliação</label>
                            <input type="text" class="form-control" id="rate-view" readonly>
                        </div>
                        <div class="form-group col-md-8">
                            <label>Produto</label>
                            <a href="" target="_blank" class="btn btn-primary col-md-12" id="product-view" readonly>Ver Produto</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Usuário</label><br>
                            <a href="" target="_blank" class="btn btn-primary col-md-12" id="user-view" readonly>Ver Usuário</a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-primary col-md-5" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@stop
