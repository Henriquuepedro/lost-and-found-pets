@extends('user.welcome')

@section('title', 'Anunciar')

@section('js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jquery-image-uploader/src/image-uploader.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jquery-validation/dist/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script type="text/javascript" src="https://npmcdn.com/flatpickr@4.6.6/dist/l10n/pt.js"></script>
    <script>
        $(function(){
            $('#phone_contact').mask('(00) 0000-00009');
            $('#neigh, #city').select2();
            $('.flatpickr').flatpickr({
                enableTime: false,
                dateFormat: "d/m/Y",
                wrap: true,
                clickOpens: false,
                allowInput: true,
                locale: "pt"
            });
            CKEDITOR.replace('observation', {
                entities: false,
                toolbar: [
                    {
                        name: 'document',
                        items: ['Undo', 'Redo']
                    },
                    {
                        name: 'styles',
                        items: ['Format']
                    },
                    {
                        name: 'basicstyles',
                        items: ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat']
                    },
                    {
                        name: 'paragraph',
                        items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent']
                    },
                    {
                        name: 'links',
                        items: ['Link', 'Unlink']
                    }
                ],
                height: 150
            });

            // Renderiza o plugin de imagens
            // $('.input-images').imageUploader();

            $('[name="images[]"]').on('change', function(){
                if ($('.image-uploader .uploaded .uploaded-image').length == 0)
                    $('.upload-text').css("border", "1px solid #bf1616");
                else
                    $('.upload-text').css("border", "unset");
            });

            $('.uploaded-image .delete-image').on('click', function () {
                if ($('.image-uploader .uploaded .uploaded-image').length == 0)
                    $('.upload-text').css("border", "1px solid #bf1616");
                else
                    $('.upload-text').css("border", "unset");
            });

            // Carregando imagens já inseridas
            let preloaded = [];
            let codImage  = 0;
            let imagePrimary = 0;
            let options;
            if($('.images-pre').length === 1) {
                $('.images-pre input').each(function () {
                    codImage = $(this).attr('cod-img');
                    if($(this).attr('img-primary') == 1) imagePrimary = codImage;
                    preloaded.push({id: `old_${codImage}`, src: $(this).val()});
                });
                options = {
                    preloaded,
                    imagesInputName: 'images',
                    preloadedInputName: 'old_images'
                };
            }
            // Renderiza o plugin de imagens
            $('.input-images').imageUploader(options);

            // Adiciona class na imagem primária
            $(`.uploaded-image input[value="old_${imagePrimary}"]`).parents('.uploaded-image').addClass('primary-image');

            setTimeout(() => {
                if ($('[name="neight_id"]').val() != 0 && $('[name="neight_id"]').val() != null) {
                    getNeighs($('#city').val(), $('[name="neight_id"]').val());
                }
            }, 500);
        });

        // validate the form when it is submitted
        $("#formInsertAnimal").validate({
            errorContainer: $("div.error-form"),
            errorLabelContainer: $("ol", $("div.error-form")),
            wrapper: 'li',
            rules: {
                name: {
                    required: true
                },
                city: {
                    required: true
                },
                neigh: {
                    required: true
                }
            },
            invalidHandler: function(event, validator) {
                $('.upload-text').css("border", "unset");
                $('.note-editor.card').css("border", "1px solid #a9a9a9");
                /*if (!CKEDITOR.instances.observation.getData().length)
                    createListError('description', 'Digite a descrição do animal corretamente', '.note-editor.card');
                if ($('.image-uploader .uploaded .uploaded-image').length == 0)
                    createListError('images', 'Selecione pelo menos uma imagem para continuar', '.upload-text');*/
            },
            submitHandler: function(form) {
                $('.upload-text').css("border", "unset");
                $('.note-editor.card').css("border", "1px solid #a9a9a9");
                /*if (!CKEDITOR.instances.observation.getData().length)
                    createListError('description', 'Digite a descrição do animal corretamente', '.note-editor.card');
                if ($('.image-uploader .uploaded .uploaded-image').length == 0)
                    createListError('images', 'Selecione pelo menos uma imagem para continuar', '.upload-text');*/

                form.submit();
            }
        });

        $('#city').change(function (){
            const city = parseInt($(this).val());

            $('#neigh').empty().append('<option value="">Selecione a bairro</option>').attr('disabled', true);

            if (city === 0) return false;

            getNeighs(city)
        });

        const createListError = (field, message, element) => {
            if ($(document).scrollTop() != 0)
                $('html, body').animate({scrollTop:0}, 'slow');

            $(element).css("border", "1px solid #bf1616");
            $('.error-form').show().find('ol').show().append(`<li><label id="weight-error" class="error" for="${field}" style="">${message}</label></li>`)
        }

        const getNeighs = (city, neigh = null) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{ route('queries.ajax.getNeighsCity') }}",
                data: { city },
                dataType: 'json',
                success: response => {

                    let options = '';
                    let selected = '';
                    $(response).each(function (key, value) {

                        selected = neigh == value.id ? 'selected' : '';

                        options += `<option value="${value.id}" ${selected}>${value.name}</option>`;
                    });

                    $('#neigh').append(options).attr('disabled', false);

                }, error: () => {
                    console.log("Acorreu um problema, tente mais tarde!");
                }
            });
        }
    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/jquery-image-uploader/src/image-uploader.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_orange.css">
    <style>
        .image-uploader::after {
            content: 'CLIQUE PARA ADICIONAR IMAGENS';
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
        @media (max-width: 768px) {
            .input-images .upload-text span {
                text-align: center;
            }
            .image-uploader {
                min-height: 275px;
            }
        }
        .error-form ol {
            list-style: disc
        }
        .flatpickr a.input-button,
        .flatpickr button.input-button{
            height: calc(1.5em + 0.75rem + 3px);
            width: 50%;
            /*text-align: center;*/
            /*padding-top: 13%;*/
            cursor: pointer;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .flatpickr a.input-button:last-child,
        .flatpickr button.input-button:last-child{
            border-bottom-right-radius: 5px;
            border-top-right-radius: 5px;
        }
        #disappearance_date {
            border-bottom-right-radius: 0;
            border-top-right-radius: 0;
        }
        .btn-orange {
            background-color: #C0392B;
        }
        .btn-orange:hover {
            background-color: #9a2e23;
        }
    </style>
@endsection

@section('body')

    <div class="main">
        <div class="wrap">
            <div class="error-form alert alert-warning" style="display: {{ count($errors) == 0 ? 'none' : 'block' }}">
                <h5>Existem erros no envio do formulário, veja abaixo para corrigi-los.</h5>
                <ol class="col-md-12">
                    @foreach($errors->all() as $error)
                        <li><label id="name-error" class="error">{{ $error }}</label></li>
                    @endforeach
                </ol>
            </div>
            <form action="{{ route('user.account.animals.update') }}" method="POST" enctype="multipart/form-data" id="formInsertAnimal">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 d-flex flex-wrap">
                                <div class="form-group col-md-6">
                                    <label for="name">Nome do Animal</label>
                                    <input type="text" class="form-control" maxlength="256" name="name" id="name" value="{{ old() ? old('name') : $animal['name'] }}" title="É preciso informar o nome" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="species">Espécie <small>(<b>Exemplo</b>: Cachorro, Gato, Etc...)</small></label>
                                    <input type="text" class="form-control" maxlength="256" name="species" id="species" value="{{ old() ? old('species') : $animal['species'] }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Sexo</label>
                                    <br/>
                                    <div class="d-flex justify-content-between flex-wrap">
                                        <label><input type="radio" value="M" name="sex" {{ old() ? (old('sex') == 'M' ? 'checked' : '') : ($animal['sex'] == 'M' ? 'checked' : '') }}> Macho</label>
                                        <label><input type="radio" value="F" name="sex" {{ old() ? (old('sex') == 'F' ? 'checked' : '') : ($animal['sex'] == 'F' ? 'checked' : '') }}> Fêmea</label>
                                        <label><input type="radio" value="X" name="sex" {{ old() ? (old('sex') == 'X' ? 'checked' : '') : ($animal['sex'] == 'X' ? 'checked' : '') }}> Desconhecido</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex flex-wrap">
                                <div class="form-group col-md-3">
                                    <label for="age">Idade</label>
                                    <input type="text" class="form-control" maxlength="256" name="age" id="age" value="{{ old() ? old('age') : $animal['age'] }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="size">Porte</label>
                                    <input type="text" class="form-control" maxlength="7" name="size" id="size" value="{{ old() ? old('size') : $animal['size'] }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="color">Cor</label>
                                    <input type="text" class="form-control" maxlength="256" name="color" id="color" value="{{ old() ? old('color') : $animal['color'] }}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="race">Raça</label>
                                    <input type="text" class="form-control" maxlength="256" name="race" id="race" value="{{ old() ? old('race') : $animal['race'] }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex flex-wrap">
                                <div class="form-group flatpickr col-md-3">
                                    <label class="label-date-btns">Data do desaparecimento</label>
                                    <div class="d-flex">
                                    <input type="tel" name="disappearance_date" id="disappearance_date" class="form-control col-md-9"  value="{{ old() ? old('disappearance_date') : ($animal['disappearance_date'] == null ? date('d/m/Y') : date('d/m/Y', strtotime($animal['disappearance_date']))) }}" data-inputmask="'alias': 'datetime'" data-inputmask-inputformat="dd/mm/yyyy HH:MM" im-insert="false" data-input>
                                    <div class="input-button-calendar col-md-3 no-padding d-flex">
                                        <a class="input-button pull-left btn-orange" title="toggle" data-toggle>
                                            <i class="fa fa-calendar text-white"></i>
                                        </a>
                                        <a class="input-button pull-right btn-orange" title="clear" data-clear>
                                            <i class="fa fa-times text-white"></i>
                                        </a>
                                    </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="phone_contact">Telefone para contato</label>
                                    <input type="tel" class="form-control" maxlength="15" name="phone_contact" id="phone_contact" value="{{ old() ? old('phone_contact') : $animal['phone_contact'] }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="email_contact">E-mail para contato</label>
                                    <input type="email" class="form-control" maxlength="256" name="email_contact" id="email_contact" value="{{ old() ? old('email_contact') : $animal['email_contact'] }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex flex-wrap">
                                <div class="form-group col-md-3">
                                    <label for="city">Cidade do desaparecimento</label>
                                    <select class="form-control" name="city" id="city" title="É preciso informar a cidade" required>
                                        <option value="">Selecione a cidade</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city['id'] }}" {{ old() ? (old('city') == $city['id'] ? 'selected' : '') : ($animal['city'] == $city['id'] ? 'selected' : '') }}>{{ $city['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="neigh">Bairro do desaparecimento</label>
                                    <select class="form-control" name="neigh" id="neigh" title="É preciso informar o bairro" disabled required>
                                        <option value="">Selecione a bairro</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="place">Local de desaparecimento</label>
                                    <textarea class="form-control" name="place" id="place">{{ old() ? old('place') : $animal['place'] }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex flex-wrap">
                                <div class="form-group col-md-12">
                                    <label>Imagens do Animal</label><br/>
                                    <small class="text-danger">Adicione imagens com extensões png, jpeg, jpg e gif.</small>
                                    <div class="input-field">
                                        <div class="input-images" style="padding-top: .5rem;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex flex-wrap">
                                <div class="form-group col-md-12">
                                    <label for="observation">Observação</label>
                                    <textarea class="form-control" name="observation" id="observation">{{ old() ? old('observation') : $animal['observation'] }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a href="{{ route('user.account.animals') }}"><i class="fa fa-arrow-left"></i> Voltar</a>
                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Cadastrar</button>
                    </div>
                </div>

                <div class="images-pre">
                    @foreach($imagesAnimal as $images)
                        <input type="hidden" value="{{ asset('user/img/animals/' . $animal['id'] . '/' . $images['url']) }}" img-primary="{{ $images['primary'] }}" cod-img="{{ $images['cod'] }}"/>
                    @endforeach
                </div>
                <input type="hidden" name="animal_id" value="{{$animal['id']}}"/>
                <input type="hidden" name="primaryImage" value="old_{{$animal['primaryKey']}}1"/>
                <input type="hidden" name="neight_id" value="{{$animal['neigh']}}"/>
                {!! csrf_field() !!}
            </form>
        </div>
    </div>

@endsection
