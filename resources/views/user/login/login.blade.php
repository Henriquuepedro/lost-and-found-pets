@extends('user.welcome')

@section('title', 'Entrar')

@section('js')
    <script>
        const signUpButton1 = $('.signUp')[0];
        const signUpButton2 = $('#register-account')[0];
        const signInButton = $('.signIn')[0];
        const container = $('#container')[0];

        $('.signUp').on('click', () => {
            $('#container').addClass("right-panel-active");
        });

        $('#register-account').on('click', () => {
            $('#container').addClass("right-panel-active");
        });

        $('.signIn').on('click', () => {
            $('#container').removeClass("right-panel-active");
        });

        $(function (){
            if (window.location.href.split('#').length === 2) {
                if (window.location.href.split('#').pop() === 'register') {
                    $('.signUp').trigger('click');
                }
            }
        });
    </script>
@endsection

@section('css')
    <style>

        .login h1 {
            font-weight: bold;
            margin: 0;
            font-size: 25px;
        }

        .login h2 {
            text-align: center;
        }

        .login p {
            font-size: 14px;
            font-weight: 100;
            line-height: 20px;
            letter-spacing: 0.5px;
            margin: 20px 0 30px;
        }

        .login span {
            font-size: 12px;
        }

        .login a {
            color: #333;
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
        }

        .login button {
            border-radius: 20px;
            border: 1px solid #FF4B2B;
            background-color: #FF4B2B;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
        }

        .login button:active {
            transform: scale(0.95);
        }

        .login button:focus {
            outline: none;
        }

        .login button.ghost {
            background-color: transparent;
            border-color: #FFFFFF;
        }

        .login form {
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            text-align: center;
        }

        .login input {
            background-color: #eee;
            border: none;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
        }

        .login .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.25),
            0 10px 10px rgba(0,0,0,0.22);
            position: relative;
            overflow: hidden;
            width: 768px;
            max-width: 100%;
            min-height: 480px;
        }

        .login .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .login .sign-in-container {
            left: 0;
            width: 50%;
            z-index: 2;
        }

        .login .container.right-panel-active .sign-in-container {
            transform: translateX(100%);
        }

        .login .sign-up-container {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
        }

        .login .container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }

        @keyframes show {
            0%, 49.99% {
                opacity: 0;
                z-index: 1;
            }

            50%, 100% {
                opacity: 1;
                z-index: 5;
            }
        }

        .login .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }

        .login .container.right-panel-active .overlay-container{
            transform: translateX(-100%);
        }

        .login .overlay {
            background: #C0392B;
            background: -webkit-linear-gradient(to right, #212529, #C0392B);
            background: linear-gradient(to right, #212529, #C0392B);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: 0 0;
            color: #FFFFFF;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .login .container.right-panel-active .overlay {
            transform: translateX(50%);
        }

        .login .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .login .overlay-left {
            transform: translateX(-20%);
        }

        .login .container.right-panel-active .overlay-left {
            transform: translateX(0);
        }

        .login .overlay-right {
            right: 0;
            transform: translateX(0);
        }

        .login .container.right-panel-active .overlay-right {
            transform: translateX(20%);
        }

        .login .social-container {
            margin: 20px 0;
        }

        .login .social-container a {
            border: 1px solid #DDDDDD;
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            height: 40px;
            width: 40px;
        }

        .login footer {
            background-color: #222;
            color: #fff;
            font-size: 14px;
            bottom: 0;
            position: fixed;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 999;
        }

        .login footer p {
            margin: 10px 0;
        }

        .login footer i {
            color: red;
        }

        .login footer a {
            color: #3c97bf;
            text-decoration: none;
        }

        .login {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        .alert {
            width: 63%;
        }
        .errors {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            padding-top: 20px;
        }
        .signUp-xs,
        .signIn-xs,
        .or-xs{
            display: none;
        }


        @media (max-width: 992px) {
            .login .sign-up-container {
                left: 0;
                width: 100%;
                opacity: 0;
                z-index: 1;
            }
            .login .sign-in-container {
                width: 100%;
                z-index: 2;
            }

            .login .container.right-panel-active .sign-in-container {
                transform: translateX(0%);
            }
            .login .container .sign-up-container {
                opacity: 1;
                animation: show 0.6s;
            }

            .login .overlay-container {
                position: revert;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 100;
            }
            .signUp-xs,
            .signIn-xs{
                display: block;
            }


            .login .container.right-panel-active .sign-up-container {
                transform: translateX(0%);
            }

            .login .container.right-panel-active .overlay-container {
                transform: translateX(0%);
            }


            .login .container.right-panel-active .overlay {
                transform: translateX(0%);
            }

            .login .container {
                min-height: 550px;
            }
            .overlay-container {
                display: none;
            }
            .or-xs {
                display: block;
            }
        }
    </style>
@endsection

@section('body')


    <div class="main">
        <div class="wrap errors">
            @if(session('success'))
                <div class="alert alert-success mt-2">{{session('success')}}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-danger mt-2">{{session('warning')}}</div>
            @endif
            @if(isset($errors) && count($errors) > 0)
                <div class="alert alert-danger col-md-offset-3 col-md-6">
                    <ol>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ol>
                </div>
            @endif
        </div>
        <div class="wrap login">
            <div class="container" id="container">
                <div class="form-container sign-up-container">
                    <form action="{{ route('user.register.post') }}" method="post">
                        <h1>Criar Conta</h1>
                        <div class="social-container">
                            @foreach($authsExternal as $auth)
                                <a href="{{ $auth['url'] }}" class="social"><i class="{{ $auth['icon'] }}"></i></a>
                            @endforeach
                        </div>
                        <span>ou use seu e-mail para se registrar</span>
                        <input type="text" name="name" placeholder="Nome Completo" required/>
                        <input type="email" name="email" placeholder="Email" required/>
                        <input type="password" name="password" placeholder="Senha" required/>
                        <input type="password" name="password_confirmation" placeholder="Confirme sua senha" required/>
                        <button>Cadastrar</button>
                        <p class="mb-3 mt-3 or-xs">ou</p>
                        <button type="button" class="signIn-xs signIn">Entrar</button>
                        {!! csrf_field() !!}
                    </form>
                </div>
                <div class="form-container sign-in-container">
                    <form action="{{ route('user.login.post') }}" method="post">
                        <h1>Entrar</h1>
                        <div class="social-container">
                            @foreach($authsExternal as $auth)
                                <a href="{{ $auth['url'] }}" class="social"><i class="{{ $auth['icon'] }}"></i></a>
                            @endforeach
                        </div>
                        <span>ou use sua conta</span>
                        <input type="email" name="email" placeholder="E-mail" required/>
                        <input type="password" name="password" placeholder="Senha" required/>
                        <a href="{{ route('user.forgotPassword') }}">Esqueceu sua senha?</a>
                        <button>Entrar</button>
                        <p class="mb-3 mt-3 or-xs">ou</p>
                        <button type="button" class="signUp-xs signUp">Cadastra-se</button>
                        {!! csrf_field() !!}
                    </form>
                </div>
                <div class="overlay-container">
                    <div class="overlay">
                        <div class="overlay-panel overlay-left">
                            <h1>Bem vindo de volta!</h1>
                            <p>Para se manter conectado conosco, faça o login com suas informações pessoais</p>
                            <button class="ghost signIn">Entrar</button>
                        </div>
                        <div class="overlay-panel overlay-right">
                            <h1>Olá, amigo!</h1>
                            <p>Insira seus dados pessoais e comece a jornada conosco!</p>
                            <button class="ghost signUp">Cadastrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{{--
    <section class="hero-wrap hero-wrap-2" style="background-image: url({{ $settings['banner'] }});" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text align-items-end justify-content-center">
                <div class="col-md-9 ftco-animate mb-5 text-center">
                    <p class="breadcrumbs mb-0"><span class="mr-2"><a href="{{ route('user.home') }}">Início <i class="fa fa-chevron-right"></i></a></span> <span>Entrar <i class="fa fa-chevron-right"></i></span></p>
                    <h2 class="mb-0 bread">Entrar</h2>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @if(session('success'))
                        <div class="alert alert-success mt-2">{{session('success')}}</div>
                    @endif
                    @if(session('warning'))
                        <div class="alert alert-danger mt-2">{{session('warning')}}</div>
                    @endif
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
                            <h2 class="contact-title">Acesso</h2>
                        </div>
                        <form class="generalForm" action="{{ route('user.login.post') }}" method="post">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input class="form-control valid" name="email" id="email" type="email" placeholder="Digite seu endereço de e-mail">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input class="form-control" name="password" id="password" type="password" placeholder="Digite uma senha">
                                        <a href="{{ route('user.forgotPassword') }}">Esqueceu sua senha?</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group mt-2 col-md-12">
                                    <button type="submit" class="btn btn-primary py-3 col-md-12">Entrar</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 text-center mb-5">
                                    <a href="{{ route('user.register') }}">Não tem conta? Cadastre-se</a>
                                </div>
                            </div>
                            {!! csrf_field() !!}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>--}}
@endsection
