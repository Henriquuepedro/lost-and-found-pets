@extends('adminlte::page')

@section('title', 'Listagem de Clientes')

@section('content_header')
    <h1 class="m-0 text-dark">Listagem de Clientes</h1>
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
                <div class="card-header">
                    <h3 class="card-title">Clientes Cadastrados</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Criado Em</th>
                            <th>Ação</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($arrClients as $client)
                                <tr client-id="{{ $client['id'] }}">
                                    <td>{{ $client['id'] }}</td>
                                    <td>{{ $client['name'] }}</td>
                                    <td>{{ $client['email'] }}</td>
                                    <td data-order="{{ $client['datetime_order'] }}">{{ $client['created_at'] }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.client.view', ['id' => $client['id']]) }}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Visualizar"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Criado Em</th>
                            <th>Ação</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
