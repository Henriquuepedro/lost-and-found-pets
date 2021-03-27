@extends('adminlte::page')

@section('title', 'Alterar Depoimento')

@section('content_header')
    <h1 class="m-0 text-dark">Alterar Depoimento</h1>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/jquery-image-uploader/src/image-uploader.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/summernote/summernote-bs4.css') }}">
    <style>
        #viewLogo{
            border-radius: 50%;
        }
        div[class^="star-rating"] label {
             cursor:pointer;
        }
        div[class^="star-rating"] label input{
            display:none;
        }
        div[class^="star-rating"] label i {
            font-size:25px;
            -webkit-transition-property:color, text;
            -webkit-transition-duration: .2s, .2s;
            -webkit-transition-timing-function: linear, ease-in;
            -moz-transition-property:color, text;
            -moz-transition-duration:.2s;
            -moz-transition-timing-function: linear, ease-in;
            -o-transition-property:color, text;
            -o-transition-duration:.2s;
            -o-transition-timing-function: linear, ease-in;
        }
        div[class^="star-rating"] label i:before {
            content:'\f005';
        }
        div[class^="star-rating"] label i.active {
            color:gold;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <script>
        $(function () {
            $("input[data-bootstrap-switch]").each(function(){
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });
        });
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewLogo').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $("#picture").change(function() {
            readURL(this);
        });
        $('div[class^="star-rating"] label i.fa').on('click mouseover',function(){
            // remove classe ativa de todas as estrelas
            const el = $(this).closest('div[class^="star-rating"]');
            el.find('label i.fa').removeClass('active');
            // pegar o valor do input da estrela clicada
            var val = $(this).prev('input').val();
            //percorrer todas as estrelas
            el.find('label i.fa').each(function(){
                /* checar de o valor clicado é menor ou igual do input atual
                *  se sim, adicionar classe active
                */
                var $input = $(this).prev('input');
                if($input.val() <= val){
                    $(this).addClass('active');
                }
            });
        });
        //Ao sair da div star-rating
        $('div[class^="star-rating"]').mouseleave(function(){
            //pegar o valor clicado
            const el = $(this).closest('div[class^="star-rating"]');
            var val = $(this).find('input:checked').val();
            //se nenhum foi clicado remover classe de todos
            if(val == undefined ){
                el.find('label i.fa').removeClass('active');
            } else {
                //percorrer todas as estrelas
                el.find('label i.fa').each(function(){
                    /* Testar o input atual do laço com o valor clicado
                    *  se maior, remover classe, senão adicionar classe
                    */
                    var $input = $(this).prev('input');
                    if($input.val() > val){
                        $(this).removeClass('active');
                    } else {
                        $(this).addClass('active');
                    }
                });
            }
        });
    </script>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="error-form alert alert-warning {{ count($errors) == 0 ? 'display-none' : '' }}">
                <h5>Existem erros no envio do formulário, veja abaixo para corrigi-los.</h5>
                <ol>
                    @foreach($errors->all() as $error)
                        <li><label id="name-error" class="error">{{ $error }}</label></li>
                    @endforeach
                </ol>
            </div>

            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Alterar Depoimento</h3>
                </div>
                <form action="{{ route('admin.testimonies.update') }}" enctype="multipart/form-data" id="formUpdateProduct" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-8">
                                <label for="name">Nome</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $dataTestimony['name'] }}" placeholder="Digite o nome de quem irá depor" {{ $dataTestimony['user_id'] != 0 ? 'disabled' : 'required' }}>
                            </div>
                            <div class="form-group col-md-4 text-center">
                                <label for="name">Avaliação de 1 à 5</label>

                                <div class="star-rating">
                                    <label>
                                        <input type="radio" name="rate" value="1" {{$dataTestimony['rate'] == 1 ? 'checked' : ''}} {{ $dataTestimony['user_id'] != 0 ? 'disabled' : 'required' }}/>
                                        <i class="fa {{$dataTestimony['rate'] >= 1 ? 'active' : ''}}"></i>
                                    </label>
                                    <label>
                                        <input type="radio" name="rate" value="2" {{$dataTestimony['rate'] == 2 ? 'checked' : ''}} {{ $dataTestimony['user_id'] != 0 ? 'disabled' : 'required' }}/>
                                        <i class="fa {{$dataTestimony['rate'] >= 2 ? 'active' : ''}}"></i>
                                    </label>
                                    <label>
                                        <input type="radio" name="rate" value="3" {{$dataTestimony['rate'] == 3 ? 'checked' : ''}} {{ $dataTestimony['user_id'] != 0 ? 'disabled' : 'required' }}/>
                                        <i class="fa {{$dataTestimony['rate'] >= 3 ? 'active' : ''}}"></i>
                                    </label>
                                    <label>
                                        <input type="radio" name="rate" value="4" {{$dataTestimony['rate'] == 4 ? 'checked' : ''}} {{ $dataTestimony['user_id'] != 0 ? 'disabled' : 'required' }}/>
                                        <i class="fa {{$dataTestimony['rate'] >= 4 ? 'active' : ''}}"></i>
                                    </label>
                                    <label>
                                        <input type="radio" name="rate" value="5" {{$dataTestimony['rate'] == 5 ? 'checked' : ''}} {{ $dataTestimony['user_id'] != 0 ? 'disabled' : 'required' }}/>
                                        <i class="fa {{$dataTestimony['rate'] == 5 ? 'active' : ''}}"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label for="width">Ativo</label></br>
                                <input type="checkbox" {{$dataTestimony['approved'] == 1 ? 'checked' : ''}} data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="SIM"  data-off-text="NÃO"  name="approved" value="1">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="width">Primário</label></br>
                                <input type="checkbox" {{$dataTestimony['primary'] == 1 ? 'checked' : ''}} data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="SIM"  data-off-text="NÃO"  name="primary" value="1">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="name">Alterar Logo</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="picture" name="picture" {{ $dataTestimony['user_id'] != 0 ? 'disabled' : '' }}>
                                        <label class="custom-file-label" for="exampleInputFile">Alterar</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4 text-center">
                                <label for="name">Visualização Logo</label><br>
                                <img src="{{ asset("user/img/testimony/{$dataTestimony['picture']}") }}" id="viewLogo" width="70" height="70">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="description">Descrição do Depoimento</label>
                                <div class="mb-3">
                                    <textarea class="form-control" rows="5" id="testimony" name="testimony" {{ $dataTestimony['user_id'] != 0 ? 'disabled' : 'required' }}>{{ old('testimony') ?? $dataTestimony['testimony'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('admin.testimonies') }}" class="btn btn-danger col-md-3"><i class="fas fa-arrow-left"></i> Voltar</a>
                                <button type="submit" class="btn btn-success col-md-3"><i class="fa fa-save"></i> Salvar</button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="testimony_id" value="{{ $dataTestimony['id'] }}">
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>
@stop
