@extends('user.welcome')

@section('title', 'Redefinir Senha')

@section('js')
@endsection

@section('css')
    <style>

        .login h1 {
            font-weight: bold;
            margin-bottom: 10px;
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
            min-height: 400px;
        }

        .login .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .login .sign-in-container {
            left: 0;
            width: 100%;
            z-index: 2;
        }

        .login .container.right-panel-active .sign-in-container {
            transform: translateX(100%);
        }

        .login .sign-up-container {
            left: 0;
            width: 100%;
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
            width: 100%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }

        .login .container.right-panel-active .overlay-container{
            transform: translateX(-100%);
        }

        .login .overlay {
            background: #FF416C;
            background: -webkit-linear-gradient(to right, #FF4B2B, #FF416C);
            background: linear-gradient(to right, #FF4B2B, #FF416C);
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
            width: 100%;
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
            padding: 50px 0;
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
                <div class="form-container sign-in-container">
                    <form action="{{ route('user.resetPassword.post') }}" method="post">
                        <h1>Redefinir Senha</h1>
                        <input type="email" name="email" placeholder="E-mail" value="{{$email}}" readonly/>
                        <input type="password" name="password" id="password" placeholder="Digite sua nova senha" required>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirme sua senha" required>
                        <button>Redefinir Senha</button>
                        {!! csrf_field() !!}
                        <input type="hidden" name="hash" value="{{$hash}}">
                        <a href="{{ route('user.login') }}">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
