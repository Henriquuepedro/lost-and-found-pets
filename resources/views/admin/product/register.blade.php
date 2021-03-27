@extends('adminlte::page')

@section('title', 'Cadastrar Produtos')

@section('content_header')
    <h1 class="m-0 text-dark">Cadastrar Produto</h1>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/jquery-image-uploader/src/image-uploader.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/summernote/summernote-bs4.css') }}">
    <style>
        .note-dropzone{
            display: none !important;
        }
        .image-uploader::after {
            content: 'CLIQUE PARA ADICIONAR MAIS IMAGENS';
            margin: 5px 15px;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #000;
            font-family: 'system-ui';
            display: flex;
            justify-content: center;
            background: #fff;
            border-radius: 10px;
            cursor: pointer
        }
        .image-uploader {
            background: #bdbdbd;
        }
        @media (max-width: 992px) and (min-width: 575px) {
            .image-uploader {
                min-height: 8rem;
                background: #bdbdbd;
            }
            .image-uploader .uploaded .uploaded-image {
                height: 130px !important;
                width: 125px;
            }
        }
        @media (max-width: 576px) {
            .image-uploader {
                min-height: 8rem;
                background: #bdbdbd;
            }
            .image-uploader .uploaded .uploaded-image {
                height: 250px !important;
                width: 250px;
            }
            .image-uploader .uploaded{
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
            }
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <script src="https://raw.githubusercontent.com/RobinHerbots/Inputmask/3.3.3/js/jquery.inputmask.js"></script>
    <script src="https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js"></script>
    <script type="text/javascript" src="{{ asset('vendor/jquery-image-uploader/src/image-uploader.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jquery-validation/dist/jquery.validate.js') }}"></script>
    <script>
        $(function () {
            $("input[data-bootstrap-switch]").each(function(){
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });
            $('[data-toggle="tooltip"]').tooltip({'html':true});
            $('input[name="value"], input[name="value_high_discount"]').mask('#.##0,00', {
                reverse: true
            });
            $('[name="stock"]').mask('#0', {
                reverse: true
            });
            $('[name="alert_stock"]').mask('#0', {
                reverse: true
            });
            $("[name='width']").inputmask("numeric", {
                min: 11,
                max: 105
            });
            $("[name='height']").inputmask("numeric", {
                min: 2,
                max: 105
            });
            $("[name='depth']").inputmask("numeric", {
                min: 16,
                max: 105
            });
            $("[name='weight']").inputmask("numeric", {
                min: 1
            });
            $('[name="description"]').summernote({
                disableDragAndDrop:true,
                height: 150,
                hint: {
                    match: /:([\-+\w]+)$/,
                    search: function (keyword, callback) {
                        callback($.grep(emojis, function (item) {
                            return item.indexOf(keyword)  === 0;
                        }));
                    },
                    template: function (item) {
                        var content = emojiUrls[item];
                        return '<img src="' + content + '" width="20" /> :' + item + ':';
                    },
                    content: function (item) {
                        var url = emojiUrls[item];
                        if (url) {
                            return $('<img />').attr('src', url).css('width', 20)[0];
                        }
                        return '';
                    }
                }
            })
            $.ajax({
                url: 'https://api.github.com/emojis',
                async: false
            }).then(function(data) {
                window.emojis = Object.keys(data);
                window.emojiUrls = data;
            });

            // Renderiza o plugin de imagens
            $('.input-images').imageUploader();
        });

       const createListError = (field, message, element) => {
           if ($(document).scrollTop() != 0)
               $('html, body').animate({scrollTop:0}, 'slow');

           $(element).css("border", "1px solid #bf1616");
           $('.error-form').show().find('ol').show().append(`<li><label id="weight-error" class="error" for="${field}" style="">${message}</label></li>`)
       }

        // Validar dados
        const container = $("div.error-form");
        // validate the form when it is submitted
        $("#formInsertProduct").validate({
            errorContainer: container,
            errorLabelContainer: $("ol", container),
            wrapper: 'li',
            rules: {
                images: {
                    images: true
                },
                name: {
                    required: true
                },
                value: {
                    required: true
                },
                stock: {
                    required: true,
                    number: true
                },
                width: {
                    required: true,
                    number: true
                },
                height: {
                    required: true,
                    number: true
                },
                depth: {
                    required: true,
                    number: true
                },
                weight: {
                    required: true,
                    number: true
                },
                description: {
                    required: true
                },
                primaryImage: {
                    required: true,
                    number: true
                }
            },
            invalidHandler: function(event, validator) {
                $('.upload-text').css("border", "unset");
                $('.note-editor.card').css("border", "1px solid #a9a9a9");
                if($('[name="description"]').summernote('isEmpty'))
                    createListError('description', 'Digite a descrição do produto corretamente', '.note-editor.card');
                if($('.image-uploader .uploaded .uploaded-image').length == 0)
                    createListError('images', 'Selecione pelo menos uma imagem para continuar', '.upload-text');
            },
            submitHandler: function(form) {
                $('.upload-text').css("border", "unset");
                $('.note-editor.card').css("border", "1px solid #a9a9a9");
                if($('[name="description"]').summernote('isEmpty'))
                    createListError('description', 'Digite a descrição do produto corretamente', '.note-editor.card');
                if($('.image-uploader .uploaded .uploaded-image').length == 0)
                    createListError('images', 'Selecione pelo menos uma imagem para continuar', '.upload-text');
                if ((parseFloat($('#width').val()) + parseFloat($('#height').val()) + parseFloat($('#depth').val())) > 200)
                    createListError('width', 'A soma da largura + altura + comprimento não pode ser maior que 200', '#width, #height, #depth');

                if($('.image-uploader .uploaded .uploaded-image').length != 0 &&  $('[name="description"]').summernote('isEmpty') == false && (parseFloat($('#width').val()) + parseFloat($('#height').val()) + parseFloat($('#depth').val())) <= 200) form.submit();
            }
        });
        $("[name='description']").on("summernote.change", function (e) {   // callback as jquery custom event
           if($('[name="description"]').summernote('isEmpty'))
                $('.note-editor.card').css("border", "1px solid #bf1616");
           else
               $('.note-editor.card').css("border", "1px solid #a9a9a9");
        });
        $('[name="images[]"]').on('change', function(){
            if($('.image-uploader .uploaded .uploaded-image').length == 0)
                $('.upload-text').css("border", "1px solid #bf1616");
            else
                $('.upload-text').css("border", "unset");
        });
        $('.uploaded-image .delete-image').on('click', function () {
            if($('.image-uploader .uploaded .uploaded-image').length == 0)
                $('.upload-text').css("border", "1px solid #bf1616");
            else
                $('.upload-text').css("border", "unset");
        })
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
                    <h3 class="card-title">Cadastrar Produto</h3>
                </div>
                <form action="{{ route('admin.products.insert') }}" enctype="multipart/form-data" id="formInsertProduct" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="name">Nome do Produto</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" title="Digite o nome do produto corretamente" placeholder="Digite o nome do produto" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="value">Valor do Produto</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text font-weight-bold">
                                          R$
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="value" name="value" value="{{ old('value') }}" title="Digite o valor do produto corretamente" placeholder="Digite o valor do produto" required>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="value_high_discount" class="d-flex justify-content-between">Valor De Venda Supeior <i class="fa fa-info-circle" data-toggle="tooltip" title="Use um valor de venda superior para que o valor do produto seja visualizado com desconto.<br><br> Exemplo: <br>De <s>R$ 150,00</s> <br>Por R$ 120,00 <br><br> Deixe em branco ou R$ 0,00 caso não precise usar."></i></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text font-weight-bold">
                                          R$
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="value_high_discount" name="value_high_discount" value="{{ old('value_high_discount') }}" title="Digite o valor de venda supeior" placeholder="Digite o valor do produto">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label for="stock">Quantidade em Estoque</label>
                                <input type="text" class="form-control" id="stock" name="stock" value="{{ old('stock') ?? 0 }}" title="Digite o estoque do produto corretamente" placeholder="Digite o estoque do produto" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="alert_stock" class="d-flex justify-content-between">Alerta de Estoque <i class="fa fa-info-circle" data-toggle="tooltip" title="Será alertado no indicador do dashboard e na listagem de produtos quando o estoque ultrapassar o estoque mínimo. <br><br> Informe zero caso não queira um alerta."></i></label>
                                <input type="text" class="form-control" id="alert_stock" name="alert_stock" value="{{ old('alert_stock') ?? 0 }}" title="Digite uma quantidade para alerta de estoque" placeholder="Digite o estoque do produto" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="width">Largura</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="width" name="width" value="{{ old('width') }}" title="Digite a largura do produto corretamente" placeholder="Digite a largura do produto" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold">
                                          cm
                                        </span>
                                    </div>
                                </div>
                                <small>11cm até 105cm</small>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="height">Altura</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="height" name="height" value="{{ old('height') }}" title="Digite a altura do produto corretamente" placeholder="Digite a altura do produto" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold">
                                          cm
                                        </span>
                                    </div>
                                </div>
                                <small>2cm até 105cm</small>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="depth">Comprimento</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="depth" name="depth" value="{{ old('depth') }}" title="Digite a comprimento do produto corretamente" placeholder="Digite a comprimento do produto" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold">
                                          cm
                                        </span>
                                    </div>
                                </div>
                                <small>16cm até 105cm</small>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="weight">Peso (gramas)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="weight" name="weight" value="{{ old('weight') }}" title="Digite o peso do produto corretamente" placeholder="Digite o peso do produto" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold">
                                          &nbsp;g&nbsp;
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="value">Ativo</label>
                                <div class="form-group col-md-12">
                                    <input type="checkbox" data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Sim"  data-off-text="Não" name="active" {{ old() ? (old('active') == "on" ? 'checked' : '') : 'checked' }}>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="description">Descrição do Produto</label>
                                <div class="mb-3">
                                    <textarea class="textarea" id="description" name="description" title="Digite a descrição do produto corretamente" required>{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="description">Imagens do Produto</label><br/>
                                <small class="text-danger">Adicione imagens com extensões png, jpeg, jpg e gif de até 2048k em uma proporção de 1:1</small>
                                <div class="input-field">
                                    <div class="input-images" style="padding-top: .5rem;"></div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="primaryImage" value=""/>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="{{ route('admin.products') }}" class="btn btn-danger col-md-3"><i class="fas fa-arrow-left"></i> Voltar</a>
                                <button type="submit" class="btn btn-success col-md-3"><i class="fa fa-save"></i> Salvar</button>
                            </div>
                        </div>
                    </div>
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>
@stop
