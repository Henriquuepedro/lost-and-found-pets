@extends('user.welcome')

@section('title', 'Contato')

@section('js')
@endsection

@section('css')
@endsection

@section('body')

    <div class="main">
        <div class="wrap">
            <div class="about-top">
                <div class="about">
                    <div class="contact">
                        <div class="col-md-12">
                            <div class="company_address">
                                <div class="contact-left">
                                    <h3>Contato</h3>
                                    <p>Email: <a href="mailto:{{$addressEmail}}">{{$addressEmail}}</a></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            @if(session('success'))
                                <div class="alert alert-success mt-3 col-md-12">{{session('success')}}</div>
                            @endif
                            @if(session('warning'))
                                <div class="alert alert-danger mt-3 col-md-12">{{session('warning')}}</div>
                            @endif
                            @if(isset($errors) && count($errors) > 0)
                                <div class="alert alert-danger">
                                    <h4>Existem erros no envio, veja abaixo para corrigi-los.</h4>
                                    <ol>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <div class="contact-form">
                                <h3 class="h3">Contatar</h3>
                                <form class="contactForm" action="{{ route('user.mail.contact') }}" method="POST" id="contactForm">
                                    <div>
                                        <span><label class="label" for="name">Nome Completo</label></span>
                                        <span><input type="text" class="textbox" name="name" id="name" placeholder="Nome Completo" value="{{ old('name') }}" required></span>
                                    </div>
                                    <div>
                                        <span><label class="label" for="email">Endereço de E-mail</label></span>
                                        <span><input type="email" class="textbox" name="email" id="email" placeholder="Endereço de E-mail" value="{{ old('email') }}" required></span>
                                    </div>
                                    <div>
                                        <span><label class="label" for="subject">Assunto</label></span>
                                        <span><input type="text" class="textbox" name="subject" id="subject" placeholder="Assunto" value="{{ old('subject') }}" required></span>
                                    </div>
                                    <div>
                                        <span><label class="label">Mensagem</label></span>
                                        <span><textarea name="message" class="textbox" id="message" cols="30" rows="4" placeholder="Mensagem" required>{{ old('message') }}</textarea></span>
                                    </div>
                                    <div>
                                        <input type="submit" value="Enviar Mensagem" class="btn btn-primary">
                                    </div>
                                    {!! csrf_field() !!}
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
