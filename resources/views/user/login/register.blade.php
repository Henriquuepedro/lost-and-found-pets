@extends('user.welcome')

@section('title', 'Cadastro')

@section('js')
    <script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
    <script>
        $(function () {
            $('input[name="tel"]').trigger('blur').mask('(00) 0000-00009');
        })
    </script>
@endsection

@section('css')
    <style>

        @media (min-width: 768px) {
            .title-login a{
                padding-left: 20px;
            }
        }
    </style>
@endsection

@section('body')

    <section class="hero-wrap hero-wrap-2" style="background-image: url({{ $settings['banner'] }});" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text align-items-end justify-content-center">
                <div class="col-md-9 ftco-animate mb-5 text-center">
                    <p class="breadcrumbs mb-0"><span class="mr-2"><a href="{{ route('user.home') }}">Início <i class="fa fa-chevron-right"></i></a></span> <span>Criar Conta <i class="fa fa-chevron-right"></i></span></p>
                    <h2 class="mb-0 bread">Criar Conta</h2>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
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
                </div>
                <div class="w-100 p-md-5 p-4">
                    <div class="d-flex align-items-center flex-wrap title-login">
                        <h2 class="contact-title">Nova Conta</h2> <a href="{{ route('user.login') }}">Já tem conta? Fazer Login</a>
                    </div>
                    <form class="form-contact register generalForm" action="{{ route('user.register.post') }}" method="post">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <input class="form-control valid" name="name" id="name" type="text" value="{{ old('name') }}" placeholder="Digite seu nome completo" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input class="form-control valid" name="email" id="email" type="text" value="{{ old('email') }}" placeholder="Digite seu endereço de e-mail" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input class="form-control valid" name="tel" id="tel" type="text" value="{{ old('tel') }}" placeholder="Digite seu número de telefone" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input class="form-control" name="password" id="password" type="password" placeholder="Digite uma senha" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input class="form-control" name="password_confirmation" id="password_confirmation" type="password" placeholder="Digite novamente sua senha" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary py-3 col-md-3">Criar Conta</button>
                        </div>
                        {!! csrf_field() !!}
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
