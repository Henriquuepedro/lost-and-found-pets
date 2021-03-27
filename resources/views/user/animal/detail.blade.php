@extends('user.welcome')

@section('title', 'Detalhes do Pedido')

@section('js')
    <script>

        $(function () {
            setTimeout(() => {
                $('.acc-delivery-cont').toggleClass('acc-delivery-cont-close acc-delivery-cont-open');
                setTimeout(() => {
                    if ($('.acc-timeline-events-event.in_progress').attr('progress-fill') >= 0) $('.acc-timeline-progress-bar').attr('style', 'width:0%')
                }, 300);
                setTimeout(() => {
                    if ($('.acc-timeline-events-event.in_progress').attr('progress-fill') >= 33) $('.acc-timeline-progress-bar').attr('style', 'width:33%')
                }, 500);
                setTimeout(() => {
                    if ($('.acc-timeline-events-event.in_progress').attr('progress-fill') >= 66) $('.acc-timeline-progress-bar').attr('style', 'width:66%')
                }, 1200);
                setTimeout(() => {
                    if ($('.acc-timeline-events-event.in_progress').attr('progress-fill') >= 100) $('.acc-timeline-progress-bar').attr('style', 'width:100%')
                }, 1800);
            }, 1000);
        })
        $('.acc-animal-detail-header').on('click', function () {
            $(this).siblings().slideToggle('slow');
        })
        $(document).scroll(function () {
            if ($(document).width() > 750){
                if ($(document).scrollTop() >= 450 && $(document).scrollTop() < 520) $('.acc-navigation .acc-submenu').css('padding-top', ($(document).scrollTop() - 440))
                else if ($(document).scrollTop() >= 520) $('.acc-navigation .acc-submenu').css('padding-top', 85)
                else $('.acc-navigation .acc-submenu').css('padding-top', 0);
            }
        });
        $('div[class^="star-rating"] label i.fa').on('click mouseover',function(){
            // remove classe ativa de todas as estrelas
            const el = $(this).closest('div[class^="star-rating"]');
            el.find('label i.fa').removeClass('active');
            // pegar o valor do input da estrela clicada
            var val = $(this).prev('input').val();
            //percorrer todas as estrelas
            el.find('label i.fa').each(function(){
                /* checar de o valor clicado é menor ou igual do input atual
                *  se sim, adicionar classe active
                */
                var $input = $(this).prev('input');
                if($input.val() <= val){
                    $(this).addClass('active');
                }
            });
        });
        //Ao sair da div star-rating
        $('div[class^="star-rating"]').mouseleave(function(){
            //pegar o valor clicado
            const el = $(this).closest('div[class^="star-rating"]');
            var val = $(this).find('input:checked').val();
            //se nenhum foi clicado remover classe de todos
            if(val == undefined ){
                el.find('label i.fa').removeClass('active');
            } else {
                //percorrer todas as estrelas
                el.find('label i.fa').each(function(){
                    /* Testar o input atual do laço com o valor clicado
                    *  se maior, remover classe, senão adicionar classe
                    */
                    var $input = $(this).prev('input');
                    if($input.val() > val){
                        $(this).removeClass('active');
                    } else {
                        $(this).addClass('active');
                    }
                });
            }
        });

        $('#btnOrderRating').on('click', function () {
            $('#mark-rate .form-rate-animal').each(function () {
                if(!$('div[class^="star-rating-"] input[name^="rate_"]:checked').length) {
                    alert('É preciso avaliar o produto entre 1 e 5 estrelas!');
                }
            })
        });

        $('#mark-received form, #mark-rate form').on('submit', function (){
            $('button[type="submit"]', this).attr('disabled', true);
        })

    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('user/css/minhaconta/style.css')}}">
    <style>
        .acc-type-card.tracking{
            border: 1px solid #ccc !important;
            border-left: 4px solid #ffc107 !important;
        }
        .acc-type-card.received{
            border: 1px solid #ccc !important;
            border-left: 4px solid #28a745 !important;
        }
        .acc-type-card.tracking button,
        .acc-type-card.received button{
            margin-left: 30px;
        }

        @media (max-width: 992px) {
            .acc-type-card.tracking button,
            .acc-type-card.received button{
                margin-left: 0px;
                margin-top: 10px;
            }
        }
        div[class^="star-rating"] label {
            cursor:pointer;
        }
        div[class^="star-rating"] label input{
            display:none;
        }
        div[class^="star-rating"] label i {
            font-size:25px;
            -webkit-transition-property:color, text;
            -webkit-transition-duration: .2s, .2s;
            -webkit-transition-timing-function: linear, ease-in;
            -moz-transition-property:color, text;
            -moz-transition-duration:.2s;
            -moz-transition-timing-function: linear, ease-in;
            -o-transition-property:color, text;
            -o-transition-duration:.2s;
            -o-transition-timing-function: linear, ease-in;
        }
        div[class^="star-rating"] label i:before {
            content:'\f005';
        }
        div[class^="star-rating"] label i.active {
            color:gold;
        }
    </style>
@endsection

@section('body')

    <section class="hero-wrap hero-wrap-2" style="background-image: url({{ $settings['banner'] }});" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text align-items-end justify-content-center">
                <div class="col-md-9 ftco-animate mb-5 text-center">
                    <p class="breadcrumbs mb-0">
                        <span class="mr-2"><a href="{{ route('user.home') }}">Início <i class="fa fa-chevron-right"></i></a></span>
                        <span class="mr-2"><a href="{{ route('user.account') }}">Minha Conta <i class="fa fa-chevron-right"></i></a></span>
                        <span>Pedidos <i class="fa fa-chevron-right"></i></span></p>
                    <h2 class="mb-0 bread">Pedidos</h2>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="acc-container">
                    @if(session('success'))
                        <div class="alert alert-success mt-2">{{session('success')}}</div>
                    @endif
                    @if(session('warning'))
                        <div class="alert alert-danger mt-2">{{session('warning')}}</div>
                    @endif
                    <div class="acc-container-content col-md-12">
                        @include('user.account.menu')
                        <div class="acc-content-column">
                            <div class="acc-content-wrapper">
                                <div class="acc-order-container">
                                    <h2 class="acc-page-header">Detalhes Do Pedido</h2>
                                    <div class="acc-payment-and-delivery">
                                        <div class="acc-type-card">
                                            <div class="acc-order-detail-header">
                                                <span class="acc-payment-open-icon"></span>
                                                <strong class="acc-payment-title acc-payment-title-total">
                                                    Detalhes Compra
                                                </strong>
                                            </div>
                                            <div class="acc-payment-address-body">
                                                <div class="acc-payment-values ">
                                                    <p>
                                                        Data da compra: {{ date('d/m/Y', strtotime($dataOrder[0]['created_at'])) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="acc-payment-values-cont">
                                            <div class="acc-order-detail-header">
                                                <span class="acc-payment-open-icon"></span>
                                                <strong class="acc-payment-title acc-payment-title-total">
                                                    Datalhes Pagamento
                                                </strong>
                                            </div>
                                            <div class="acc-payment-address-body">
                                                <p class="acc-payment-values">
                                                    <span>Pagamento</span>
                                                    <span>
                                                        @if($dataOrder[0]['type_payment'] == 1)
                                                            Cartão de crédito ({{$dataOrder[0]['qty_parcels']}}x)
                                                        @elseif($dataOrder[0]['type_payment'] == 2)
                                                            Boleto
                                                        @endif
                                                    </span>
                                                </p>
                                                <br/>
                                                @if($dataOrder[0]['type_payment'] == 1)
                                                    <p class="acc-payment-values">
                                                        <span>Parcelas</span>
                                                        <span>
                                                        R$ {{ number_format($dataOrder[0]['parcel_card'], 2, ',', '.') }}
                                                    </span>
                                                    </p>
                                                @endif
                                                <p class="acc-payment-values">
                                                    <span>Produtos</span>
                                                    <span>
                                                        R$ {{ number_format($amount_products, 2, ',', '.') }}
                                                    </span>
                                                </p>
                                                <p class="acc-payment-values">
                                                    <span>Desconto @if($dataOrder[0]['coupon_name'] != "")(<strong style="font-weight: bold">{{ $dataOrder[0]['coupon_name'] }}</strong>)@endif</span>
                                                    <span>
                                                        R$ {{ number_format($dataOrder[0]['extra_amount'], 2, ',', '.') }}
                                                    </span>
                                                </p>
                                                <p class="acc-payment-values">
                                                    <span>Frete via {{ $dataOrder[0]['type_ship'] }}</span>
                                                    <span>
                                                        R$ {{ number_format($dataOrder[0]['type_payment'] == 2 ? (float)$dataOrder[0]['value_ship'] : $dataOrder[0]['value_ship'], 2, ',', '.') }}
                                                    </span>
                                                </p>
                                                <p class="acc-payment-values acc-payment-values-total">
                                                    <span>Total</span>
                                                    <span>
                                                        R$ {{ number_format($dataOrder[0]['type_payment'] == 2 ? (float)$dataOrder[0]['net_amount'] : $dataOrder[0]['value_total_with_tax_card'], 2, ',', '.') }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="acc-type-address">
                                            <div class="acc-order-detail-header">
                                                <span class="acc-payment-open-icon"></span>
                                                <strong class="acc-payment-title">
                                                    Endereço
                                                </strong>
                                            </div>
                                            <div class="acc-payment-address-body">
                                                <div>
                                                    <strong>
                                                        {{ $dataOrder[0]['name_sender'] }}
                                                    </strong>
                                                    <p>
                                                        {{ $dataOrder[0]['address'] }}, {{ $dataOrder[0]['number'] }}@if($dataOrder[0]['complement'] != "") - {{ $dataOrder[0]['complement'] }}@endif<br>
                                                        @if($dataOrder[0]['reference'] != ""){{ $dataOrder[0]['reference'] }}<br>@endif
                                                        {{ $dataOrder[0]['neighborhood'] }} - {{ $dataOrder[0]['city'] }} - {{ $dataOrder[0]['state'] }}<br>
                                                        CEP: {{ preg_replace("/([0-9]{2})([0-9]{3})([0-9]{3})/", "$1.$2-$3", $dataOrder[0]['cep']) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($dataOrder[0]['type_payment'] == 2 && (!$status['progress']['aprovado'] && !$status['done']['aprovado']))
                                        <div class="acc-payment-and-delivery">
                                            <div class="acc-type-card">
                                                <div class="text-center mt-3 mb-4">
                                                    <h5>
                                                        Pagamento via boleto bancário, imprima seu boleto!
                                                    </h5>
                                                </div>
                                                <div class="text-center">
                                                    <a href="{{$dataOrder[0]['link_billet']}}" class="btn btn-primary col-md-4" target="_blank">IMPRIMIR BOLETO</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif


                                    @if(count($trackings) > 0 && $status['progress']['entregue'] == false)

                                        <div class="acc-payment-and-delivery">
                                            <div class="acc-type-card tracking">
                                                <div class="mt-3 mb-4 col-md-12">
                                                    <h5>
                                                        Seu pedido já foi enviado. <button data-toggle="modal" data-target="#view-tracking" class="btn btn-primary btn-warning py-1">Acompanhar envio</button>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="acc-payment-and-delivery">
                                            <div class="acc-type-card received">
                                                <div class="mt-3 mb-4 col-md-12">
                                                    <h5>
                                                        Já recebeu seu pedido? <button data-toggle="modal" data-target="#mark-received" class="btn btn-success py-1">Avisar recebimento</button>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif(!$status['rated'] && $status['progress']['entregue'])
                                        <div class="acc-payment-and-delivery">
                                            <div class="acc-type-card received">
                                                <div class="mt-3 mb-4 col-md-12">
                                                    <h5>
                                                        Tem um minuto para avaliar o que achou da compra? <button data-toggle="modal" data-target="#mark-rate" class="btn btn-success py-1">Avaliar</button>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="acc-order-card order-details">
                                        <div class="acc-delivery-cont acc-delivery-cont-delivered  acc-delivery-cont-close">
                                            <div class="acc-order-header {{ $status['progress']['cancelado'] ? 'danger' : ($status['progress']['entregue'] ? 'success' : 'warning') }}">
                                                <span class="acc-order-header-icon"></span>
                                                <div class="acc-order-header-info">
                                                    <span class="acc-order-header-info-status">
                                                        @if($status['progress']['recebido'])
                                                            Pedido Recebido
                                                        @elseif($status['progress']['cancelado'])
                                                            Pedido Cancelado
                                                        @elseif($status['progress']['aprovado'])
                                                            Pagamento Aprovado
                                                        @elseif($status['progress']['em_transporte'])
                                                            Em Transporte
                                                        @elseif($status['progress']['entregue'])
                                                            Pedido Entregue
                                                        @endif
                                                    </span>
                                                    @if($status['progress']['cancelado'])
                                                    <span>

                                                    </span>
                                                    @elseif($status['progress']['entregue'])
                                                        <span>
                                                        Entregue em:
                                                        <strong class="acc-delivery-prevision-days delivered">{{ $status['dateStatus']['entregue'] }}</strong>
                                                    </span>
                                                    @else
                                                        <span>
                                                        Receba até:
                                                        <strong class="acc-delivery-prevision-days delivered">{{ date('d/m', strtotime($dataOrder[0]['delivery_date'])) }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="acc-delivery-body acc-delivery-body-prod-open {{ $status['progress']['cancelado'] ? 'danger' : ($status['progress']['entregue'] ? 'success' : 'warning') }}">
                                                <div class="acc-order-info-cont acc-order-info-cont-delivered">
                                                    <div class="acc-delivery-header">
                                                        <div class="acc-delivery-header-order">
                                                            Pedido: #{{ str_pad($dataOrder[0]['order_id'], 5, "0", STR_PAD_LEFT) }}
                                                        </div>
                                                    </div>
                                                    <ul class="acc-delivery-list">

                                                        @foreach($dataOrder as $iten)
                                                            <li class="acc-order-item-cont">
                                                                <div class="acc-order-product justify-content-center">
                                                                    <a target="_blank" class="img-product" href="{{ route('user.product', ['id' => $iten['product_id']]) }}">
                                                                        <figure>
                                                                            <img class="acc-order-product-image" src="{{ asset("user/img/products/" . $iten['image_prd']) }}">
                                                                        </figure>
                                                                    </a>
                                                                    <div class="acc-order-product-truncate">
                                                                    <span class="acc-order-product-info" alt="{{ $iten['description'] }}" title="{{ $iten['description'] }}">
                                                                        <a class="acc-order-product-link" target="_blank" href="{{ route('user.product', ['id' => $iten['product_id']]) }}">
                                                                            {{ $iten['description'] }}
                                                                        </a>
                                                                    </span>
                                                                        <p class="acc-order-product-info"><strong>{{ (int)$iten['quantity'] }} {{ $iten['quantity'] > 1 ? "unidades" : "unidade" }} - R$ {{ number_format($iten['total_iten'], 2, ',', '.') }}</strong></p>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endforeach

                                                    </ul>
                                                    <div class="acc-timeline">
                                                        <div class="acc-timeline-timeline">
                                                            <div class="acc-timeline-progress" progress-fill="0">
                                                                <div class="acc-timeline-progress-bar" style="width: 0%;"></div>
                                                            </div>
                                                            <div class="acc-timeline-events">
                                                                <div class="acc-timeline-events-event {{$status['progress']['recebido'] ? "in_progress" : ""}} {{$status['done']['recebido'] ? "done" : ""}}">
                                                                    <div class="fill" style="animation-delay: 0ms;">
                                                                        @if($status['progress']['recebido'])
                                                                            <div class="acc-timeline-events-event-icon" style="animation-delay: 300ms;">
                                                                                <i class="fas fa-file-alt" style="right: -3px"></i>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="acc-timeline-truncate">
                                                                        <div class="acc-timeline-item">
                                                                            Pedido recebido
                                                                            <span class="acc-timeline-item-since">
                                                                                   @if($status['dateStatus']['recebido'] != "")
                                                                                    {{ $status['dateStatus']['recebido'] }}
                                                                                @endif
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                                @if($status['progress']['cancelado'])
                                                                    <div class="acc-timeline-events-event in_progress" progress-fill="33">
                                                                        <div class="fill" style="animation-delay: 600ms;">
                                                                            <div class="acc-timeline-events-event-icon" style="animation-delay: 900ms;">
                                                                                <i class="fas fa-times" style="font-size: 30px;right: -2px;top: -2px;"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="acc-timeline-truncate">
                                                                            <div class="acc-timeline-item">
                                                                                Pedido cancelado
                                                                                <span class="acc-timeline-item-since">
                                                                                {{ $status['dateStatus']['cancelado'] }}
                                                                             </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="acc-timeline-events-event {{$status['progress']['aprovado'] ? "in_progress" : ""}} {{$status['done']['aprovado'] ? "done" : ""}}" progress-fill="33">
                                                                        <div class="fill" style="animation-delay: 600ms;">
                                                                            @if($status['progress']['aprovado'])
                                                                                <div class="acc-timeline-events-event-icon" style="animation-delay: 900ms;">
                                                                                    <i class="fas fa-money-check-alt"></i>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="acc-timeline-truncate">
                                                                            <div class="acc-timeline-item">
                                                                                Pagamento aprovado
                                                                                <span class="acc-timeline-item-since">
                                                                               @if($status['dateStatus']['aprovado'] != "")
                                                                                        {{ $status['dateStatus']['aprovado'] }}
                                                                                    @endif
                                                                             </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif


                                                                <div class="acc-timeline-events-event {{$status['progress']['em_transporte'] ? "in_progress" : ""}} {{$status['done']['em_transporte'] ? "done" : ""}}" progress-fill="66">
                                                                    <div class="fill" style="animation-delay: 1200ms;">
                                                                        @if($status['progress']['em_transporte'])
                                                                            <div class="acc-timeline-events-event-icon" style="animation-delay: 1500ms;">
                                                                                <i class="fas fa-dolly"></i>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="acc-timeline-truncate">
                                                                        <div class="acc-timeline-item">
                                                                            Em transporte
                                                                            <span class="acc-timeline-item-since">
                                                                               @if($status['dateStatus']['em_transporte'] != "")
                                                                                    {{ $status['dateStatus']['em_transporte'] }}
                                                                                @endif
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="acc-timeline-events-event {{$status['progress']['entregue'] ? "in_progress" : ""}}" progress-fill="100">
                                                                    <div class="fill" style="animation-delay: 1800ms;">
                                                                        @if($status['progress']['entregue'])
                                                                            <div class="acc-timeline-events-event-icon" style="animation-delay: 2100ms;">
                                                                                <i class="fas fa-thumbs-up"></i>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="acc-timeline-truncate">
                                                                        <div class="acc-timeline-item">
                                                                            Pedido entregue
                                                                            <span class="acc-timeline-item-since">
                                                                               @if($status['dateStatus']['entregue'] != "")
                                                                                    {{ $status['dateStatus']['entregue'] }}
                                                                                @endif
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(count($trackings) > 0 && $status['progress']['entregue'] == false)
    <div class="modal fade" id="view-tracking" tabindex="-1" role="dialog" aria-labelledby="view-tracking" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="view-tracking">Códigos de rastreio</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            @if(count($trackings) > 0)
                                @foreach($trackings['codes'] as $key => $tracking)
                                    <p class="text-center">
                                        <span class="font-weight-bold">Postado em:</span>
                                        {{ date('d/m/Y H:i', strtotime($trackings['dates'][$key])) }} -
                                        <span class="font-weight-bold">Código: </span>
                                        <a href="https://linketrack.com/track?codigo={{ $tracking }}" target="_blank">{{ $tracking }}</a>
                                    </p>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-primary py-2 col-md-3" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mark-received" tabindex="-1" role="dialog" aria-labelledby="mark-received" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('user.orders.received') }}" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mark-received">Códigos de rastreio</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="text-center">Já recebeu seu pedido?</h4>
                                <input type="hidden" name="order_id" value="{{ $dataOrder[0]['order_id'] }}">
                                {!! csrf_field() !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between flex-wrap">
                        <button type="button" class="btn btn-primary py-2 col-md-3" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-success py-2 col-md-5" id="btnOrderReceived">Já recebi, atualizar pedido</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @elseif(!$status['rated'])
    <div class="modal fade" id="mark-rate" tabindex="-1" role="dialog" aria-labelledby="mark-rate" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('user.rates.rate') }}" method="post" id="rate-form" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mark-rate">Faça sua avaliação!</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body generalForm">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="text-center">De 1 a 5, como você avalia {{ count($dataOrder) > 1 ? 'os produtos' : 'o produto' }}</h5>
                            </div>
                        </div>
                        @foreach($dataOrder as $iten)
                            <div class="row form-rate-order">
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="title_{{$iten['product_id']}}" rows="2" placeholder="Digite um título ..." required>
                                </div>
                                <div class="col-md-4 d-flex justify-content-center flex-wrap align-items-center form-group">
                                    <a target="_blank" class="img-product" href="{{ route('user.product', ['id' => $iten['product_id']]) }}">
                                        <figure>
                                            <img class="acc-order-product-image" src="{{ asset("user/img/products/" . $iten['image_prd']) }}" width="70" height="70">
                                        </figure>
                                    </a>
                                    <div class="star-rating-{{$iten['product_id']}}">
                                        <label>
                                            <input type="radio" name="rate_{{$iten['product_id']}}" value="1" required/>
                                            <i class="fa"></i>
                                        </label>
                                        <label>
                                            <input type="radio" name="rate_{{$iten['product_id']}}" value="2" required/>
                                            <i class="fa"></i>
                                        </label>
                                        <label>
                                            <input type="radio" name="rate_{{$iten['product_id']}}" value="3" required/>
                                            <i class="fa"></i>
                                        </label>
                                        <label>
                                            <input type="radio" name="rate_{{$iten['product_id']}}" value="4" required/>
                                            <i class="fa"></i>
                                        </label>
                                        <label>
                                            <input type="radio" name="rate_{{$iten['product_id']}}" value="5" required/>
                                            <i class="fa"></i>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-xl-12 col-md-12 form-group">
                                    <textarea name="description_{{$iten['product_id']}}" rows="4" class="form-control" placeholder="Digite o que achou do produto ..." required></textarea>
                                </div>
                            </div>
                        @endforeach
                        {!! csrf_field() !!}
                        <input type="hidden" name="order_id" value="{{ $dataOrder[0]['order_id'] }}">
                    </div>
                    <div class="modal-footer d-flex justify-content-between flex-wrap">
                        <button type="button" class="btn btn-primary py-2 col-md-3" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-success py-2 col-md-5" id="btnOrderRating">Avaliar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection
