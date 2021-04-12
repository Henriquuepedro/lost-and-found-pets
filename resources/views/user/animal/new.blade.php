@extends('user.welcome')

@section('title', 'Anunciar')

@section('js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jquery-image-uploader/src/image-uploader.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jquery-validation/dist/jquery.validate.js') }}"></script>
    <script>
        $(function(){
            $('#phone_contact').mask('(00) 0000-00009');
            $('#disappearance_date').mask('00/00/0000 00:00');
            $('#neigh, #city').select2();
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
            $('.input-images').imageUploader();

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
            });
        });

        // validate the form when it is submitted
        $("#formInsertAnimal").validate({
            errorContainer: $("div.error-form"),
            errorLabelContainer: $("ol", $("div.error-form")),
            wrapper: 'li',
            rules: {
                images: {
                    images: true
                },
                name: {
                    required: true
                },
                city: {
                    required: true
                },
                neigh: {
                    required: true
                },
                place: {
                    required: true,
                }
            },
            invalidHandler: function(event, validator) {
                $('.upload-text').css("border", "unset");
                $('.note-editor.card').css("border", "1px solid #a9a9a9");
                if(!CKEDITOR.instances.observation.getData().length)
                    createListError('description', 'Digite a descrição do animal corretamente', '.note-editor.card');
                if($('.image-uploader .uploaded .uploaded-image').length == 0)
                    createListError('images', 'Selecione pelo menos uma imagem para continuar', '.upload-text');
            },
            submitHandler: function(form) {
                $('.upload-text').css("border", "unset");
                $('.note-editor.card').css("border", "1px solid #a9a9a9");
                if(!CKEDITOR.instances.observation.getData().length)
                    createListError('description', 'Digite a descrição do animal corretamente', '.note-editor.card');
                if($('.image-uploader .uploaded .uploaded-image').length == 0)
                    createListError('images', 'Selecione pelo menos uma imagem para continuar', '.upload-text');
                if ((parseFloat($('#width').val()) + parseFloat($('#height').val()) + parseFloat($('#depth').val())) > 200)
                    createListError('width', 'A soma da largura + altura + comprimento não pode ser maior que 200', '#width, #height, #depth');

                if($('.image-uploader .uploaded .uploaded-image').length != 0 && CKEDITOR.instances.observation.getData().length != 0) form.submit();
            }
        });

        $('#city').change(function (){
            const city = parseInt($(this).val());

            $('#neigh').empty().append('<option value="">Selecione a bairro</option>').attr('disabled', true);

            if (city === 0) return false;

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
                    $(response).each(function (key, value) {
                         options += `<option value="${value.id}">${value.name}</option>`;
                    });

                    $('#neigh').append(options).attr('disabled', false);

                }, error: () => {
                    console.log("Acorreu um problema, aguarde enquanto tentamos novamente");
                    Toast.fire({
                        icon: 'warning',
                        title: "Acorreu um problema, aguarde enquanto tentamos novamente"
                    })
                    enableButtonsCart();
                    $('.update-qty').trigger('mouseleave');
                    element.find('.update-qty').trigger('click');
                }
            });
        });

        const createListError = (field, message, element) => {
            if ($(document).scrollTop() != 0)
                $('html, body').animate({scrollTop:0}, 'slow');

            $(element).css("border", "1px solid #bf1616");
            $('.error-form').show().find('ol').show().append(`<li><label id="weight-error" class="error" for="${field}" style="">${message}</label></li>`)
        }
    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/jquery-image-uploader/src/image-uploader.css') }}">
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
        .error-form ol {
            list-style: disc
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
            <form action="{{ route('user.account.animals.insert') }}" method="POST" enctype="multipart/form-data" id="formInsertAnimal">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 d-flex flex-wrap">
                                <div class="form-group col-md-6">
                                    <label for="name">Nome do Animal</label>
                                    <input type="text" class="form-control" maxlength="256" name="name" id="name" title="É preciso informar o nome">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="species">Espécie</label>
                                    <input type="text" class="form-control" maxlength="256" name="species" id="species">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Sexo</label>
                                    <br/>
                                    <div class="d-flex justify-content-between flex-wrap">
                                        <label><input type="radio" value="M" name="sex"> Macho</label>
                                        <label><input type="radio" value="F" name="sex"> Fêmea</label>
                                        <label><input type="radio" value="X" name="sex"> Desconhecido</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex flex-wrap">
                                <div class="form-group col-md-3">
                                    <label for="age">Idade</label>
                                    <input type="text" class="form-control" maxlength="256" name="age" id="age">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="size">Porte</label>
                                    <input type="text" class="form-control" maxlength="4" name="size" id="size">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="color">Cor</label>
                                    <input type="text" class="form-control" maxlength="256" name="color" id="color">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="race">Raça</label>
                                    <input type="text" class="form-control" maxlength="256" name="race" id="race">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex flex-wrap">
                                <div class="form-group col-md-3">
                                    <label for="disappearance_date">Data do desaparecimento</label>
                                    <input type="text" class="form-control" maxlength="256" name="disappearance_date" id="disappearance_date">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="phone_contact">Telefone para contato</label>
                                    <input type="tel" class="form-control" maxlength="15" name="phone_contact" id="phone_contact">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="email_contact">E-mail para contato</label>
                                    <input type="email" class="form-control" maxlength="256" name="email_contact" id="email_contact">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex flex-wrap">
                                <div class="form-group col-md-3">
                                    <label for="city">Cidade do desaparecimento</label>
                                    <select class="form-control" name="city" id="city" title="É preciso informar a cidade">
                                        <option value="">Selecione a cidade</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city['id'] }}">{{ $city['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="neigh">Bairro do desaparecimento</label>
                                    <select class="form-control" name="neigh" id="neigh" title="É preciso informar o bairro" disabled>
                                        <option value="">Selecione a bairro</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="place">Local de desaparecimento</label>
                                    <textarea class="form-control" name="place" id="place" title="É preciso informar o local do desaparecimento"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex flex-wrap">
                                <div class="form-group col-md-12">
                                    <label for="description">Imagens do Animal</label><br/>
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
                                    <textarea class="form-control" name="observation" id="observation"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a href="{{ route('user.account') }}"><i class="fa fa-arrow-left"></i> Voltar</a>
                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Cadastrar</button>
                    </div>
                </div>
                <input type="hidden" name="primaryImage" value=""/>
                {!! csrf_field() !!}
            </form>
        </div>
    </div>

@endsection
