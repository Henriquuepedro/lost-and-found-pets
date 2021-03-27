@extends('user.welcome')

@section('title', 'Cadastro')

@section('js')
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('user/css/minhaconta/style.css')}}">
@endsection

@section('body')
    <div class="main">
        <div class="wrap">
            <div class="row">
                <div class="col-md-3 float-left">
                    <div class="acc-container-content col-md-12">
                        @include('user.account.menu')
                    </div>
                </div>
                <div class="col-md-9">
                    @if((isset($errors) && count($errors) > 0) || session('success'))
                        <div class="col-md-12 mb-4">
                            @if(isset($errors) && count($errors) > 0)
                                <div class="alert alert-danger">
                                    <h4>Existem erros no formulário, veja abaixo para corrigi-los.</h4>
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
                    @endif
                    <div class="col-md-12">
                        <div class="contact-form">
                            <h1 class="mt-0">Alterar Cadastro</h1>
                            <form action="{{ route('user.account.edit.post') }}" method="post">
                                <div class="col-md-12">
                                    <label>Nome</label>
                                    <input class="textbox" name="name" id="name" type="text" value="{{ old('name') ?? $arrDataClient['name'] }}" placeholder="Digite seu nome completo" required>
                                </div>
                                <div class="col-md-12">
                                    <label>E-mail</label>
                                    <input class="textbox" name="email" id="email" type="email" value="{{ old('email') ?? $arrDataClient['email'] }}" placeholder="Digite seu endereço de e-mail" required>
                                </div>

                                <div class="alert alert-info text-center">Caso não queira alterar a senha, deixe-a em branco!</div>


                                <div class="col-md-4 float-left pr-md-1">
                                    <label>Senha Atual</label>
                                    <input class="textbox" name="password_current" id="password_current" type="password" placeholder="Digite a senha atual">
                                </div>
                                <div class="col-md-4 float-left pr-md-1">
                                    <label>Nova Senha</label>
                                    <input class="textbox" name="password" id="password" type="password" placeholder="Digite uma nova senha">
                                </div>
                                <div class="col-md-4 float-right">
                                    <label>Confirmação da senha</label>
                                    <input class="textbox" name="password_confirmation" id="password_confirmation" type="password" placeholder="Digite novamente sua nova senha">
                                </div>
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <button type="submit" class="btn btn-primary">Alterar Cadastro</button>
                                    </div>
                                </div>
                                {!! csrf_field() !!}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
