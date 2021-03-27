@extends('user.welcome')

@section('title', 'Fretes e Entregas')

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
                    <p class="breadcrumbs mb-0"><span class="mr-2"><a href="{{ route('user.home') }}">Início <i class="fa fa-chevron-right"></i></a></span> <span>Fretes e Entregas <i class="fa fa-chevron-right"></i></span></p>
                    <h2 class="mb-0 bread">Fretes e Entregas</h2>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section ftco-no-pb">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-justify mb-5">
                    <h5 class="text-solove"><strong>*ATENÇÃO!* Disponibilizamos FRETE GRÁTIS nas compras acima de R$190,00. Lembrando que, o desconto do frete será aplicado na página de finalização da compra dos produtos e com entrega na modalidade PAC (Entrega convencional), caso o cliente optar pela modalidade SEDEX deverá pagar o valor do frete.</strong></h5>
                    <h5>Nossas entregas padrão são realizadas por meio dos correios à todo território nacional mas, em casos específicos nos reservamos ao cliente outras formas de entrega afim de melhorar o serviço e satisfação do mesmo. Para saber mais entre em contato conosco através do {{ $settings['email'] }}.</h5>
                    <h5><i>ATENÇÃO!</i> Os prazos de entrega podem variar de acordo com o peso dos produtos, local de entrega e tipo de envio que for selecionado pelo cliente.</h5>
                    <h5>Para consultar os prazos e as modalidades de entrega disponíveis, basta informar o CEP do endereço de entrega no fechamento da compra e verificar os prazos disponibilizados pelos Correios ou outros transportadores disponíveis. Na página de entrada há um resumo de sua compra. Para verificar todas as informações, basta clicar no número do pedido.</h5>

                    <h5 class="font-weight-bold"><i>Posso alterar o endereço de entrega?</i></h5>
                    <h5><i>Após efetuar a compra <u>não é possível alterar a mudança do endereço de entrega</u> para outra faixa de CEP em virtude da variação de frete e alíquotas de impostos que já foram recolhidas, como por exemplo, o ICMS (Imposto sobre Circulação de Mercadorias e Serviços).</i></h5>

                    <h5>Eventualmente, disponibilizamos FRETE GRÁTIS para todo Brasil, fique atento nas promoções!</h5>

                    <h5 class="font-weight-bold">Você ainda tem alguma dúvida? então FALE CONOSCO!</h5>

                </div>
            </div>
        </div>
    </section>

@endsection
