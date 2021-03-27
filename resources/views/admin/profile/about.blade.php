@extends('adminlte::page')

@section('title', 'Sobre a Empresa')

@section('content_header')
    <h1 class="m-0 text-dark">Sobre a Empresa</h1>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/summernote/summernote-bs4.css') }}">
    <style>
    @media (min-width: 991px) {
        .note-color.note-color-all .note-dropdown-menu.dropdown-menu.show {
            left: -150px !important;
        }
    }
    #image_about{
        width: 100%;
    }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/summernote/summernote-bs4.min.js') }}"></script>
    <script>
        $(function () {
            $('[name="description_about"]').summernote({
                disableDragAndDrop:true,
                height: 235,
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
                },
                toolbar: [
                    // [groupName, [list of button]]
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']]
                ]
            })
        });

        $("[name='image_about']").on('change', function() {
            const input = this;
            console.log('entrou');
            console.log(input.files);
            console.log($(this));
            console.log($(this).val());

            if (input.files && input.files[0]) {
                var reader = new FileReader();
                console.log(reader);

                reader.onload = function (e) {
                    $('#image_about').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
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
                <form action="{{ route('admin.about.insert') }}" method="post" enctype="multipart/form-data">
                    <div class="card-header">
                        <h3 class="card-title">Dados sobre a empresa</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group text-center">
                                <img src="{{ $image_about }}" id="image_about" class="img-thumbnail">
                                <small class="text-danger">Utilize sempre imagens da proporção 1:1.5, caso contrário podem ocorrer distorções.<br> Serão aceitas as extensões jpeg, jpg e png!</small>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="image_about">
                                        <label class="custom-file-label text-left" for="exampleInputFile">Alterar Imagem</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <label>Título do Sobre</label>
                                        <input type="text" class="form-control col-md-12" name="title_about" value="{{ old('title_about') ?? $title_about }}">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label>Descrição do Sobre</label>
                                        <textarea type="text" class="form-control col-md-12" name="description_about">{{ old('description_about') ?? $description_about }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-success col-md-3"><i class="fa fa-save"></i> Salvar</button>
                    </div>
                {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </div>
@stop
