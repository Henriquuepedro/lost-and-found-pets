@extends('user.welcome')

@section('title', 'Redefinir Senha')

@section('js')
@endsection

@section('css')
@endsection

@section('body')

    <section class="hero-wrap hero-wrap-2" style="background-image: url({{ $settings['banner'] }});" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text align-items-end justify-content-center">
                <div class="col-md-9 ftco-animate mb-5 text-center">
                    <p class="breadcrumbs mb-0">
                        <span class="mr-2"><a href="{{ route('user.home') }}">Início <i class="fa fa-chevron-right"></i></a></span>
                        <span class="mr-2"><a href="{{ route('user.login') }}">Entrar <i class="fa fa-chevron-right"></i></a></span>
                        <span class="mr-2"><a href="{{ route('user.forgotPassword') }}">Recuperar Senha <i class="fa fa-chevron-right"></i></a></span>
                        <span>Redefinir Senha <i class="fa fa-chevron-right"></i></span></p>
                    <h2 class="mb-0 bread">Redefinir Senha</h2>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @if(isset($errors) && count($errors) > 0)
                        <div class="alert alert-danger col-md-offset-4 col-md-4">
                            <ol>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ol>
                        </div>
                    @endif
                </div>
                <div class="col-md-5 col-md-offset-3-5">
                    <div class="w-100 p-4">
                        <div class="d-flex align-items-center flex-wrap title-login">
                            <h2 class="contact-title">Redefinir</h2>
                        </div>
                        <form class="generalForm" action="{{ route('user.resetPassword.post') }}" method="post">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input class="form-control valid" name="email" id="email" type="email" value="{{$email}}" placeholder="Digite seu endereço de e-mail" required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input class="form-control valid" name="password" id="password" type="password" placeholder="Digite sua nova senha" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input class="form-control valid" name="password_confirmation" id="password_confirmation" type="password" placeholder="Confirme sua senha" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group mt-2 col-md-12">
                                    <button type="submit" class="btn btn-primary py-3 col-md-12">Redefinir</button>
                                </div>
                            </div>
                            {!! csrf_field() !!}
                            <input type="hidden" name="hash" value="{{$hash}}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
