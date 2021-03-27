@extends('user.welcome')

@section('title', 'Cadastro')

@section('js')
    <script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
    <script>
        $(function () {
            $('input[name="tel"]').trigger('blur').mask('(00) 0000-00009');
        })

        $(document).scroll(function () {
            if ($(document).width() > 750){
                if ($(document).scrollTop() >= 325 && $(document).scrollTop() < 370) {
                    $('.acc-navigation .acc-submenu').css('padding-top', 65 - (370 - $(document).scrollTop()))
                } else if ($(document).scrollTop() >= 370) {
                    $('.acc-navigation .acc-submenu').css('padding-top', 65)
                } else {
                    $('.acc-navigation .acc-submenu').css('padding-top', 0);
                }
            }
        })
    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('user/css/minhaconta/style.css')}}">
@endsection

@section('body')
    <div class="main">
        <div class="wrap">
            <div class="about-top">
                <div class="about">
                    <div class="contact">
                        <div class="col-md-12">
                            @if(isset($errors) && count($errors) > 0)
                                <div class="alert alert-danger">
                                    <h4>Existem erros no cadastro, veja abaixo para corrigi-los.</h4>
                                    <ol>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            @endif

                            @if(session('success'))
                                <div class="alert alert-success">{{session('success')}}</div>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <div class="contact-form">
                                <h1>Alterar Cadastro</h1>
                                <form action="{{ route('user.account.edit.post') }}" method="post">
                                    <div>
                                        <label>Nome</label>
                                        <input class="textbox" name="name" id="name" type="text" value="{{ old('name') ?? $arrDataClient['name'] }}" placeholder="Digite seu nome completo" required>
                                    </div>
                                    <div>
                                        <label>E-mail</label>
                                        <input class="textbox" name="email" id="email" type="email" value="{{ old('email') ?? $arrDataClient['email'] }}" placeholder="Digite seu endereço de e-mail" required>
                                    </div>

                                    <div class="alert alert-info text-center">Caso não queira alterar a senha, deixe-a em branco!</div>


                                    <div class="col-md-4 float-right">
                                        <label>Senha Atual</label>
                                        <input class="textbox" name="password_current" id="password_current" type="password" placeholder="Digite a senha atual">
                                    </div>
                                    <div class="col-md-4 float-right">
                                        <label>Senha</label>
                                        <input class="textbox" name="password" id="password" type="password" placeholder="Digite uma nova senha">
                                    </div>
                                    <div class="col-md-4 float-left">
                                        <label>Confirmação da senha</label>
                                        <input class="textbox" name="password_confirmation" id="password_confirmation" type="password" placeholder="Digite novamente sua nova senha">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 form-group">
                                            <button type="submit" class="btn">Alterar Cadastro</button>
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                </form>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
