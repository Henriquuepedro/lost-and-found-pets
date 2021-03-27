@extends('user.welcome')

@section('title', 'Pedidos')

@section('js')
    <script>

        // $('.acc-order-header').click(function () {
        //     const element = $(this).closest('.acc-order-card');
        //     $(this).parent().toggleClass('acc-delivery-cont-close acc-delivery-cont-open');
        //
        //     if(element.find(".acc-timeline").is(':visible') === false) {
        //         element.find('.acc-timeline-progress-bar').attr('style', 'width:0%');
        //     }
        //     else {
        //         setTimeout(() => {
        //             element.find('.acc-timeline-progress-bar').attr('style', 'width:0%')
        //         }, 300);
        //         setTimeout(() => {
        //             element.find('.acc-timeline-progress-bar').attr('style', 'width:33%')
        //         }, 600);
        //         setTimeout(() => {
        //             element.find('.acc-timeline-progress-bar').attr('style', 'width:66%')
        //         }, 1300);
        //         setTimeout(() => {
        //             element.find('.acc-timeline-progress-bar').attr('style', 'width:100%')
        //         }, 1900);
        //     }
        // })

        $('.acc-order-header').click(function () {

            const element = $(this).closest('.acc-order-card');
            $(this).parent().toggleClass('acc-delivery-cont-close acc-delivery-cont-open');

            if(element.find(".acc-timeline").is(':visible') === false) {
                element.find('.acc-timeline-progress-bar').attr('style', 'width:0%');
            }
            else {
                setTimeout(() => {
                    if(element.find('.acc-timeline-events-event.in_progress').attr('progress-fill') >= 0) element.find('.acc-timeline-progress-bar').attr('style', 'width:0%')
                }, 300);
                setTimeout(() => {
                    if(element.find('.acc-timeline-events-event.in_progress').attr('progress-fill') >= 33) element.find('.acc-timeline-progress-bar').attr('style', 'width:33%')
                }, 500);
                setTimeout(() => {
                    if(element.find('.acc-timeline-events-event.in_progress').attr('progress-fill') >= 66) element.find('.acc-timeline-progress-bar').attr('style', 'width:66%')
                }, 1200);
                setTimeout(() => {
                    if(element.find('.acc-timeline-events-event.in_progress').attr('progress-fill') >= 100) element.find('.acc-timeline-progress-bar').attr('style', 'width:100%')
                }, 1800);
            }
        })

    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('user/css/minhaconta/style.css')}}">
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
                    <div class="acc-container-content col-md-12">
                        @include('user.account.menu')
                        <div class="acc-content-column">

                            @if(count($dataOrders) > 0)

                            <div class="acc-content-wrapper">
                                <div class="acc-order-container">
                                    <h2 class="acc-page-header">Pedidos Realizados</h2>

                                    @foreach($dataOrders as $order)
                                    <div class="acc-order-card">
                                        <div class="acc-delivery-cont acc-delivery-cont-delivered  acc-delivery-cont-close">
                                            <div class="acc-order-header {{ $order['status']['progress']['cancelado'] ? 'danger' : ($order['status']['progress']['entregue'] ? 'success' : 'warning') }}">
                                                <span class="acc-order-header-icon"></span>
                                                <div class="acc-order-header-info">
                                                    <span class="acc-order-header-info-status">
                                                        @if($order['status']['progress']['recebido'])
                                                            Pedido Recebido
                                                        @elseif($order['status']['progress']['cancelado'])
                                                            Pedido Cancelado
                                                        @elseif($order['status']['progress']['aprovado'])
                                                            Pagamento Aprovado
                                                        @elseif($order['status']['progress']['em_transporte'])
                                                            Em Transporte
                                                        @elseif($order['status']['progress']['entregue'])
                                                            Pedido Entregue
                                                        @endif
                                                    </span>
                                                    @if($order['status']['progress']['cancelado'])
                                                    <span>

                                                    </span>
                                                    @elseif($order['status']['progress']['entregue'])
                                                    <span>
                                                        Entregue em:
                                                        <strong class="acc-delivery-prevision-days delivered">{{ $order['status']['dateStatus']['entregue'] }}</strong>
                                                    </span>
                                                    @else
                                                        <span>
                                                        Receba até:
                                                        <strong class="acc-delivery-prevision-days delivered">{{ date('d/m', strtotime($order['dataOrder']['delivery_date'])) }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="acc-delivery-body acc-delivery-body-prod-open {{ $order['status']['progress']['cancelado'] ? 'danger' : ($order['status']['progress']['entregue'] ? 'success' : 'warning') }}">
                                                <div class="acc-order-info-cont acc-order-info-cont-delivered">
                                                    <div class="acc-delivery-header">
                                                        <div class="acc-delivery-header-order">
                                                            Pedido: #{{ str_pad($order['dataOrder']['id'], 5, "0", STR_PAD_LEFT) }}
                                                        </div>
                                                    </div>
                                                    <ul class="acc-delivery-list  ">
                                                        @foreach($order['dataOrderItems'] as $key => $iten)
                                                            <li class="acc-order-item-cont {{$key > 0 ? 'hide-item-close' : ''}}">
                                                                <div class="acc-order-product">
                                                                    <a target="_blank" rel="noopener noreferrer" class="img-product" href="{{ route('user.product', ['id' => $iten['product_id']]) }}">
                                                                        <figure><img class="acc-order-product-image" src="{{ asset("user/img/products/" . $iten['product_id'] . "/" . $iten['path']) }}" alt="Sem imagem"></figure>
                                                                    </a>
                                                                    <div class="acc-order-product-truncate">
                                                                    <span class="acc-order-product-info" alt="{{ $iten['name'] }}" title="{{ $iten['name'] }}">
                                                                        <a class="acc-order-product-link" target="_blank" rel="noopener noreferrer" href="{{ route('user.product', ['id' => $iten['product_id']]) }}">{{ $iten['name'] }}</a>
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
                                                                <div class="acc-timeline-events-event {{$order['status']['progress']['recebido'] ? "in_progress" : ""}} {{$order['status']['done']['recebido'] ? "done" : ""}}">
                                                                    <div class="fill" style="animation-delay: 0ms;">
                                                                        @if($order['status']['progress']['recebido'])
                                                                            <div class="acc-timeline-events-event-icon" style="animation-delay: 300ms;">
                                                                                <i class="fas fa-file-alt" style="right: -3px"></i>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="acc-timeline-truncate">
                                                                        <div class="acc-timeline-item">
                                                                            Pedido recebido
                                                                            <span class="acc-timeline-item-since">
                                                                                   @if($order['status']['dateStatus']['recebido'] != "")
                                                                                    {{ $order['status']['dateStatus']['recebido'] }}
                                                                                @endif
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                                @if($order['status']['progress']['cancelado'])
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
                                                                                {{ $order['status']['dateStatus']['cancelado'] }}
                                                                             </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="acc-timeline-events-event {{$order['status']['progress']['aprovado'] ? "in_progress" : ""}} {{$order['status']['done']['aprovado'] ? "done" : ""}}" progress-fill="33">
                                                                        <div class="fill" style="animation-delay: 600ms;">
                                                                            @if($order['status']['progress']['aprovado'])
                                                                                <div class="acc-timeline-events-event-icon" style="animation-delay: 900ms;">
                                                                                    <i class="fas fa-money-check-alt"></i>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="acc-timeline-truncate">
                                                                            <div class="acc-timeline-item">
                                                                                Pagamento aprovado
                                                                                <span class="acc-timeline-item-since">
                                                                               @if($order['status']['dateStatus']['aprovado'] != "")
                                                                                        {{ $order['status']['dateStatus']['aprovado'] }}
                                                                                    @endif
                                                                             </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif


                                                                <div class="acc-timeline-events-event {{$order['status']['progress']['em_transporte'] ? "in_progress" : ""}} {{$order['status']['done']['em_transporte'] ? "done" : ""}}" progress-fill="66">
                                                                    <div class="fill" style="animation-delay: 1200ms;">
                                                                        @if($order['status']['progress']['em_transporte'])
                                                                            <div class="acc-timeline-events-event-icon" style="animation-delay: 1500ms;">
                                                                                <i class="fas fa-dolly"></i>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="acc-timeline-truncate">
                                                                        <div class="acc-timeline-item">
                                                                            Em transporte
                                                                            <span class="acc-timeline-item-since">
                                                                               @if($order['status']['dateStatus']['em_transporte'] != "")
                                                                                    {{ $order['status']['dateStatus']['em_transporte'] }}
                                                                                @endif
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="acc-timeline-events-event {{$order['status']['progress']['entregue'] ? "in_progress" : ""}}" progress-fill="100">
                                                                    <div class="fill" style="animation-delay: 1800ms;">
                                                                        @if($order['status']['progress']['entregue'])
                                                                            <div class="acc-timeline-events-event-icon" style="animation-delay: 2100ms;">
                                                                                <i class="fas fa-thumbs-up"></i>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="acc-timeline-truncate">
                                                                        <div class="acc-timeline-item">
                                                                            Pedido entregue
                                                                            <span class="acc-timeline-item-since">
                                                                               @if($order['status']['dateStatus']['entregue'] != "")
                                                                                    {{ $order['status']['dateStatus']['entregue'] }}
                                                                                @endif
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="boxBtnAction">
                                                        <a class="btn btn-primary col-md-4 py-1" href="{{ route('user.account.order', array('id' => $order['dataOrder']['id'])) }}">
                                                            <i class="fas fa-file-alt"></i> Ver Detalhes
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            @else
                            <div class="acc-content-wrapper">
                                <div class="acc-order-container">
                                    <h3 class="text-center">Você ainda não possuí pedidos!</h3>
                                    <p class="text-center mt-4">
                                        <a href="{{route('user.home')}}" class="link-primary cursor-pointer">Voltar para página inicial</a>
                                        <span> ou </span>
                                        <a href="{{route('user.products')}}" class="link-primary cursor-pointer">escolha um produto</a>.
                                    </p>
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
