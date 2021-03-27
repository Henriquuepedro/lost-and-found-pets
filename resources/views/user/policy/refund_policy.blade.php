@extends('user.welcome')

@section('title', 'Política de Reembolso')

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
                    <p class="breadcrumbs mb-0"><span class="mr-2"><a href="{{ route('user.home') }}">Início <i class="fa fa-chevron-right"></i></a></span> <span>Política de Reembolso <i class="fa fa-chevron-right"></i></span></p>
                    <h2 class="mb-0 bread">Política de Reembolso</h2>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section ftco-no-pb">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-justify mb-5">
                    <h5>Prezamos a satisfação total de nossos clientes e por isso tornamos mais simples nossa política de reembolso, através dos seguintes passos:</h5>
                    <h5>1) Envie um e-mail para: <strong>sac@canecasolove.com.br</strong>  informando no campo Assunto: Reembolso.</h5>
                    <h5>2) No corpo do e-mail descreva o motivo.</h5>
                    <h5>3) Aguarde o resultado da análise do  setor responsável.</h5>
                    <h5>4) Após a autorização da empresa envie o produto para o nosso endereço, assim que for conferido, efetuaremos o reembolso imediatamente.</h5>
                    <h5>Fique ciente! Efetuando a compra você concorda com os termos descritos acima.</h5>
                    <h5>Confira os casos em que o reembolso se aplica:</h5>
                    <h5>- Produto com defeito (garantia de 30 dias).</h5>
                    <h5>- Arrependimento (até 7 dias após o recebimento do produto).</h5>
                    <h5 class="font-weight-bold">Dúvidas? FALE CONOSCO!</h5>
            </div>
        </div>
        </div>
    </section>

@endsection
