@extends('user.welcome')

@section('title', 'Pedido Realizado')

@section('js')
    <script src="{{ asset('vendor/icheck/icheck.js') }}"></script>
    <script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
    <script>
        $('.acc-animal-detail-header').on('click', function () {
            $(this).siblings().slideToggle('slow');
        })
    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('user/css/minhaconta/style.css')}}">
    <link rel="stylesheet" href="{{ asset('vendor/icheck/skins/all.css')}}">
    <style>
        .acc-content-column{
            width: 100%;
        }
        .acc-order-header{
            cursor: default;
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
                        <span>Pedido Realizado <i class="fa fa-chevron-right"></i></span></p>
                    <h2 class="mb-0 bread">Pedido Realizado</h2>
                </div>
            </div>
        </div>
    </section>


    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="acc-container">
                    <div class="acc-container-content col-md-12">
                        <div class="acc-content-column">
                            <div class="acc-content-wrapper">
                                <div class="acc-order-container">
                                    <h2 class="acc-page-header">Detalhes Do Pedido</h2>
                                    <div class="alert-warning alert mt-3">{{ $message_checkout_user }}</div>
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
                                    <div class="acc-payment-and-delivery">
                                        <div class="acc-type-card">
                                            <div class="text-center mt-3 mb-1">
                                                <h5 class="">
                                                    Você pode rastrear o seu pedido através da <a href="{{ route('user.account') }}"><u style="color: #b7472a">Minha Conta</u></a>.<br>
                                                    Agradecemos a sua compra!
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    @if($dataOrder[0]['type_payment'] == 2)
                                        <div class="acc-payment-and-delivery">
                                            <div class="acc-type-card">
                                                <div class="text-center mt-3 mb-4">
                                                    <h5 class="">
                                                        Pagamento via boleto bancário, imprima seu boleto!
                                                    </h5>
                                                </div>
                                                <div class="text-center">
                                                    <a href="{{$dataOrder[0]['link_billet']}}" class="btn btn-primary col-md-4" target="_blank">IMPRIMIR BOLETO</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="acc-order-card order-details">
                                        <div class="acc-delivery-cont acc-delivery-cont-delivered  acc-delivery-cont-open">
                                            <div class="acc-order-header">
                                                <span class="acc-order-header-icon"></span>
                                                <div class="acc-order-header-info">
                                                    <span class="acc-order-header-info-status delivered">Pedido Realizado</span>
                                                    <span>
                                                      Data estimada de entrega:
                                                        <strong class="acc-delivery-prevision-days delivered">{{ date('d/m', strtotime($dataOrder[0]['delivery_date'])) }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="acc-delivery-body acc-delivery-body-prod-open">
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

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="acc-payment-and-delivery">
                                        <div class="acc-type-card">
                                            <div class="text-center">
                                                <a href="{{ route('user.home') }}" class="btn btn-primary col-md-4">VOLTAR AO INÍCIO</a>
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

@endsection
