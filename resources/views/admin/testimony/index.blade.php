@extends('adminlte::page')

@section('title', 'Depoimentos de Cliente')

@section('content_header')
    <h1 class="m-0 text-dark">Depoimentos de Clientes</h1>
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

        $('.btnRequestDeleteTestimony').on('click', function () {
            const product_id    = $(this).closest('tr').attr('testimony-id');
            const product_name  = $(this).closest('tr').find('td:eq(1)').text();

            $('#modal-delete h5.name-testimony').text(product_name);
            $('#modal-delete  button.btnDeleteTestimony').val(product_id);
            $('#modal-delete').modal();
        })


        $(document).on('click', '.btnRequestViewRate', function () {
            const rate_id   = $(this).closest('tr').attr('rate-id');
            const modal     = $('#modal-view');
            const status    = $(this).closest('tr').find('td:eq(4)').attr('data-animal');

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

                    modal.find('#animal-view').attr('href', response.order_url);
                    modal.find('#animal-view').text('Pedido: ' + response.order_id);

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
                    <h3 class="card-title">Depoimentos Cadastrados</h3>
                    <a href="{{ route('admin.testimonies.new') }}" class="btn btn-primary col-md-3"><i class="fa fa-plus"></i> Novo Depoimento</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Avaliação</th>
                            <th>Status</th>
                            <th>Primário</th>
                            <th>Criado Em</th>
                            <th>Ação</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($arrTestimonies as $testimony)
                                <tr testimony-id="{{ $testimony['id'] }}">
                                    <td class="text-center"><img src="{{ asset("user/img/testimony/{$testimony['picture']}") }}" width="55" height="55" style="border-radius: 50%"></td>
                                    <td>{{ $testimony['name'] }}</td>
                                    <td>{{ $testimony['rate'] }}</td>
                                    <td data-order="{{ $testimony['status_order'] }}">{!! $testimony['status'] !!}</td>
                                    <td data-order="{{ $testimony['primary_order'] }}">{!! $testimony['primary'] !!}</td>
                                    <td data-order="{{ $testimony['datetime_order'] }}">{{ $testimony['created_at'] }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.testimonies.edit', ['id' => $testimony['id']]) }}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Alterar"><i class="fas fa-edit"></i></a>
                                        <button class="btn btn-danger btnRequestDeleteTestimony btn-sm" data-toggle="tooltip" title="Excluir"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Avaliação</th>
                            <th>Status</th>
                            <th>Primário</th>
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
                <form action="{{ route('admin.testimonies.delete') }}" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h4 class="modal-title">Excluir Depoimento</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <h5 class="text-danger font-weight-bold">Você tem certeza que deseja remover o depoimento: </h5>
                        <h5 class="text-danger name-testimony"></h5>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-primary col-md-5" data-dismiss="modal">Cancelar operação</button>
                        <button type="submit" class="btn btn-danger btnDeleteTestimony col-md-5" name="testimony_id" value="">Excluir permanentemente</button>
                    </div>
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>
@stop
