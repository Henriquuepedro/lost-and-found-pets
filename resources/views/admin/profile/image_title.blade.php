@extends('adminlte::page')

@section('title', 'Imagem de Título')

@section('content_header')
    <h1 class="m-0 text-dark">Imagem de Título</h1>
@stop

@section('css')
    <style>
        .custom-file-input:hover{
            cursor: pointer;
        }
        .text-banner h1 span{
            position: absolute;
            top: 29%;
            left: 17%;
            width: 67%;
            line-height: 1.1;
            font-weight: 700;
            font-style: italic;
            font-size: 80px;
            color: #fff;
            -webkit-text-stroke-width: 0px;
            text-align: center;
            -webkit-text-stroke-color: #fff;
            font-family: auto;
            transform: rotate(-4deg);
            z-index: 1;
        }
        .overlay-image-banner{
            position: absolute;
            top: 0;
            bottom: 0;
            content: '';
            opacity: .4;
            background: #000;
            z-index: 0;
        }
    </style>
@stop

@section('js')
    <script>
        $(function () {
            $('.overlay-image-banner').width($('.card-body img').width() - 13)
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
                    <div class="card-header">
                        <h3 class="card-title">Alterar Imagem</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <form action="{{ route('admin.image_title.insert') }}" method="post" enctype="multipart/form-data">
                                    <div class="col-md-12 form-group">
                                        <label>Selecione a imagem <i style="padding-left: 15px;">( Apenas jpg )</i></label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="image_title" name="image_title" required>
                                                <label class="custom-file-label" for="exampleInputFile">Alterar</label>
                                            </div>
                                            <div class="input-group-append">
                                                <button class="input-group-text btn btn-success">Enviar</button>
                                            </div>
                                        </div>
                                        <small class="text-danger">A dimensão ideal para usar é de: 1920x1280px (proporção aproximada: 1.5:1), algo diferente disso pode ficar desproporcional</small>
                                    </div>
                                    {!! csrf_field() !!}
                                </form>
{{--                                <form action="{{ route('admin.image_title.insert') }}" method="post" enctype="multipart/form-data">--}}
{{--                                    <div class="col-md-12 form-group">--}}
{{--                                        <label>Texto de visualização (<i>Página Inicial</i>)</label>--}}
{{--                                        <div class="input-group">--}}
{{--                                            <input type="text" class="form-control" name="message_title" value="{{ $text }}">--}}
{{--                                            <span class="input-group-append">--}}
{{--                                                <button type="submit" class="btn btn-success btn-flat">Atualizar</button>--}}
{{--                                            </span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    {!! csrf_field() !!}--}}
{{--                                </form>--}}
                            </div>
                        </div>
                    </div>
                </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Imagem Atual</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-center">
                            <img src="{{ $image }}" class="col-md-10">
{{--                            <div class="text-banner">--}}
{{--                                <h1><span>{{ $text }}</span></h1>--}}
{{--                            </div>--}}
{{--                            <div class="overlay-image-banner col-md-10"></div>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
