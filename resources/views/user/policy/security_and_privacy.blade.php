@extends('user.welcome')

@section('title', 'Segurança e Privacidade')

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
                    <p class="breadcrumbs mb-0"><span class="mr-2"><a href="{{ route('user.home') }}">Início <i class="fa fa-chevron-right"></i></a></span> <span>Segurança e Privacidade <i class="fa fa-chevron-right"></i></span></p>
                    <h2 class="mb-0 bread">Segurança e Privacidade</h2>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section ftco-no-pb">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-justify mb-5">
                    <h5>Garantir a segurança e privacidade de seus dados são muito importantes para nós! As informações são <strong>confidênciais e não divulgáveis</strong>, a não ser que sejam exigidas por lei. Portanto nosso compromisso é de garantir a sua privacidade enquanto navega em nosso site. Quando você inicia a navegação as informações são codificadas. Isto garante que as mesmas não sejam lidas ou alteradas por terceiros. Apenas os clientes têm condições de alterar as suas informações.</h5>
                    <h5>Quando o cliente realiza sua primeira compra em nosso site, é solicitado o preenchimento de um cadastro. Para facilitar suas próximas compras, basta fazer o login informando seu e-mail e senha. As informações que ficam armazenadas em nossos cadastros são apenas seu nome, e-mail, telefone(s) e posteriormente o endereço de envio.</h5>
                    <h5>Pode ficar tranquilo!!! Seus dados de pagamento não ficarão armazenados em nosso sistema, pois o processo de validação de pagamento é efetuado pelos operadores a cada compra.</h5>
                    <h5 class="font-weight-bold">Dúvidas? FALE CONOSCO!</h5>

                </div>
            </div>
        </div>
    </section>

@endsection
