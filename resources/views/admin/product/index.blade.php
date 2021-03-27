@extends('adminlte::page')

@section('title', 'Listagem de Produtos')

@section('content_header')
    <h1 class="m-0 text-dark">Listagem de Produtos</h1>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
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

        $('.btnRequestDeleteProduct').on('click', function () {
            const product_id    = $(this).closest('tr').attr('product-id');
            const product_name  = $(this).closest('tr').find('td:eq(1)').text();

            $('#modal-delete h5.name-product').text(product_name);
            $('#modal-delete  button.btnDeleteProduct').val(product_id);
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
                    <h3 class="card-title">Produtos Cadastrados</h3>
                    <a href="{{ route('admin.products.new') }}" class="btn btn-primary col-md-3"><i class="fa fa-plus"></i> Novo Produto</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Valor</th>
                            <th>Estoque</th>
                            <th>Ativo</th>
                            <th>Criado Em</th>
                            <th>Ação</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($arrProducts as $product)
                                <tr product-id="{{ $product['id'] }}">
                                    <td class="text-center"><img src="{{ asset('user/img/products/' . $product['path']) }}" width="55" height="55"></td>
                                    <td>{{ $product['name'] }}</td>
                                    <td data-order="{{ $product['value_order'] }}">R$ {{ $product['value'] }}</td>
                                    <td data-order="{{ $product['stock_number'] }}">{!! $product['stock'] !!}</td>
                                    <td>{!! $product['active'] !!}</td>
                                    <td data-order="{{ $product['datetime_order'] }}">{{ $product['created_at'] }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.products.edit', ['id' => $product['id']]) }}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Alterar"><i class="fa fa-edit"></i></a>
                                        <button class="btn btn-danger btnRequestDeleteProduct btn-sm" data-toggle="tooltip" title="Excluir"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Valor</th>
                            <th>Estoque</th>
                            <th>Ativo</th>
                            <th>Criado Em</th>
                            <th>Ação</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-delete" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.products.delete') }}" method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h4 class="modal-title">Excluir Produto</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <h5 class="text-danger font-weight-bold">Você tem certeza que deseja remover o produto: </h5>
                            <h5 class="text-danger name-product"></h5>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-primary col-md-5" data-dismiss="modal">Cancelar operação</button>
                            <button type="submit" class="btn btn-danger btnDeleteProduct col-md-5" name="product_id" value="">Excluir permanentemente</button>
                        </div>
                        {!! csrf_field() !!}
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
@stop
