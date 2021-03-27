@extends('adminlte::page')

@section('title', 'Listagem de Banners')

@section('content_header')
    <h1 class="m-0 text-dark">Listagem de Banners</h1>
@stop

@section('css')
{{--    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">--}}
    <style>
        #sortable{
            padding: 0px
        }
        #sortable li{
            list-style: none;
        }
        .banners .banner{
            cursor: move;
        }
        .card-footer::after{
            display: none;
        }
        .input-group-text.btn-success{
            color: #fff;
            background-color: #28a745;
            border-color: #28a745;
        }
        .input-group-text.btn-success:hover{
            background-color: #1e7e34;
            border-color: #1c7430;
        }
        .custom-file-input{
            cursor: pointer;
        }
        .banner i{
            color: #cc0202;
            font-size: 25px;
        }
        .banner i:hover {
            color: #8b0202;
            cursor: pointer;
        }

        @media (max-width: 992px) {
            #sortable li {
                flex-wrap: wrap;
                text-align: center;
            }
            #sortable li img{
                margin-top: 5px
            }
        }
    </style>
@stop

@section('js')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $( function() {
            $( "#sortable" ).sortable({
                scroll: true,
                revert: true
            });
            $( "#sortable" ).disableSelection();
            $('.banner-body').css('min-height', $('#sortable').height() + 30)
        });

        $('.btnRequestDeleteBanner').on('click', function () {
            const banner_id = $(this).closest('.banner').attr('banner-id');
            const banner_img= $(this).closest('.banner').find('img').attr('src');

            $('#modal-delete img.image-banner').attr('src', banner_img);
            $('#modal-delete button.btnDeleteBanner').val(banner_id);
            $('#modal-delete').modal();
        });

        $('#saveOrderBanner').on('click', function () {
            let banner_id;
            let order_banners = [];

            $('.banners .banner').each(function () {
                banner_id = parseInt($(this).attr('banner-id'));
                order_banners.push(banner_id);
            });


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: window.location.origin + "/admin/queries/ajax/rearrangeOrderBanners",
                data: { order_banners },
                dataType: 'json',
                success: response => {

                    $('.container-fluid .row .col-md-12 .alert.alert-danger, .container-fluid .row .col-md-12 .alert.alert-success').remove();

                    if(!response){
                        $('.container-fluid .row .col-md-12:eq(0)').prepend(`<div class="alert alert-danger mt-2">Não foi possível excluir o banner, atualize página tente novamente!</div>`);
                    }

                    $('.container-fluid .row .col-md-12:eq(0)').prepend(`<div class="alert alert-success mt-2">Ordem de visualização alterada com sucesso!</div>`);
                    $('html, body').animate({scrollTop:0}, 'slow');

                }, error: (e) => {
                    console.log(e);
                    $('.container-fluid .row .col-md-12 .alert.alert-danger, .container-fluid .row .col-md-12 .alert.alert-success').remove();
                    $('.container-fluid .row .col-md-12:eq(0)').prepend(`<div class="alert alert-danger mt-2">Acorreu um problema, caso o problema persistir contate o suporte!</div>`);
                    $('html, body').animate({scrollTop:0}, 'slow');
                }
            });
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
                        <h3 class="card-title">Cadastrar Novo Banner</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <form action="{{ route('admin.banners.insert') }}" method="post" enctype="multipart/form-data">
                                    <label>Selecione o banner</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="banner" name="banner" required>
                                            <label class="custom-file-label" for="exampleInputFile">Alterar</label>
                                        </div>
                                        <div class="input-group-append">
                                            <button class="input-group-text btn btn-success" id="sendBanner">Enviar</button>
                                        </div>
                                    </div>
                                    <small class="text-danger">O padrão das dimensões dos banners devem ser sempre as mesmas para todos os banners.</small>
                                    {!! csrf_field() !!}
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Banners Cadastrados</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="text-center">Ordem de como será listado os banners ao cliente.</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 banner-body">
                            @if(count($arrBanners) == 0)
                                <h5 class="text-danger text-center mt-5 mb-5">Adicione pelo menos um banner, caso contrário a página inicial ficará desproporcional!</h5>
                            @else
                            <ul id="sortable" class="banners d-flex justify-content-center flex-wrap">
                                @foreach( $arrBanners as $banner )
                                    <li class="banner col-md-6 mt-2 d-flex align-items-center" style="margin: 0px 1px" banner-id="{{ $banner['id'] }}">
                                        <i class="fa fa-trash col-md-2 btnRequestDeleteBanner"></i>
                                        <img class="col-md-10 img-thumbnail" src="{{ $banner['path'] }}">
                                    </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>
                @if(count($arrBanners) != 0)
                <div class="card-footer d-flex justify-content-between">
                    <a class="btn btn-danger" href="{{ route('admin.banners') }}"><i class="fa fa-times"></i> Ignorar Alterações</a>
                    <button class="btn btn-success" id="saveOrderBanner"><i class="fa fa-save"></i> Salvar Alteraçoes</button>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-delete" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.banners.delete') }}" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h4 class="modal-title">Excluir Banner</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body text-center col-md-12">
                        <h5 class="text-danger font-weight-bold">Você tem certeza que deseja remover o banner: </h5>
                        <img class="text-danger image-banner col-md-8 mt-3">
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-primary col-md-5" data-dismiss="modal">Cancelar operação</button>
                        <button type="submit" class="btn btn-danger btnDeleteBanner col-md-5" name="banner_id" value="">Excluir permanentemente</button>
                    </div>
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>
@stop
