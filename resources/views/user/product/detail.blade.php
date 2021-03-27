@extends('user.welcome')

@section('title', 'Detalhes do Produto')

@section('js')
{{--    <script src="https://www.mercadopago.com/v2/security.js" view="item"></script>--}}
    <script type="text/javascript" src="{{ asset('vendor/magic-zoom/magiczoomplus.js') }}"></script>
    <script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
    <script>
        $(function () {
            $('.galery-product .gallery').css('left', leftWidthCalc + '%');
            $('.qty-prdduct').mask('00');
            $('.input-cep').mask('00000-000')
            if($('.freights-cep .input-cep').val() != "") $('.button-calcula-cep').trigger('click');
        })
        var leftWidthCalc = 10; // posição inicial
        var sizeImgMin = 26.7; // porcentagem de cada mini imagem
        var qtyImages   = parseInt($('.gallery .item-wrapper').length) - 4;

        $(document).on('click', '.gallery-item', function (){
            console.log('entrou');
            if ($(this).hasClass('active')) return false;

            const background = $(this).attr('src');

            $('.gallery-item.active, .item-wrapper.active').removeClass('active');
            $(this).addClass('active');
            $(this).closest('.item-wrapper').addClass('active');
            $('.featured-item').attr('src', background);
            $('.MagicZoom').attr('href', background);
            $('.mz-lens img').attr('src', background);
            $('.magic-hidden-wrapper .mz-zoom-window img').attr('src', background);
            $('.feature a').attr('href', background);

        })

        const moveToRight = () => {
            if(qtyImages < 0) return ;
            if((((qtyImages * sizeImgMin)+16.6) * (-1)) == leftWidthCalc) leftWidthCalc = 10;
            else {
                leftWidthCalc -= sizeImgMin;
                if ((((qtyImages * sizeImgMin) + 16.6) * (-1)) > leftWidthCalc)
                    leftWidthCalc = ((qtyImages * sizeImgMin) + 16.6) * (-1)
            }
            $( ".gallery" ).animate({
                left: leftWidthCalc + '%'
            }, 500);
        }

        const moveToLeft = () => {
            if(qtyImages < 0) return ;

            if(leftWidthCalc == 0) leftWidthCalc = 10;
            else {
                leftWidthCalc += sizeImgMin;
                if (leftWidthCalc > 0 && leftWidthCalc != 10)
                    leftWidthCalc = ((qtyImages * sizeImgMin) + 16.6) * (-1);
            }
            $( ".gallery" ).animate({
                left: leftWidthCalc + '%'
            }, 500);
        }

        $('.quantity-right-plus').click(function () {
            let value = parseInt($('.qty-prdduct').val());
            if(value >= 99) return false;
            value += 1;
            $('.qty-prdduct').val(value);
        });

        $('.quantity-left-minus').click(function () {
            let value = parseInt($('.qty-prdduct').val());
            if(value <= 1) return false;
            value -= 1;

            $('.qty-prdduct').val(value);
        });
        $(document).on('click', '.button-calcula-cep', async function () {
            const element   = $(this).closest('.freights-cep');
            const product_id= element.attr('product-id');
            const cep       = element.find('.input-cep').val().replace(/[^\d]+/g, '');
            let consultaCep;

            if(cep.length != 8){
                Toast.fire({
                    icon: 'error',
                    title: 'CEP inválido, corrija e tente novamente!'
                });
                return false;
            }

            consultaCep = await $.getJSON(`https://viacep.com.br/ws/${cep}/json/`);

            if(consultaCep.erro){
                Toast.fire({
                    icon: 'error',
                    title: 'CEP inválido, corrija e tente novamente!'
                });
                return false;
            }

            loadPage(element);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: window.location.origin + "/queries/ajax/defineCepUser",
                data: { cep, product_id },
                dataType: 'json',
                success: result => {
                    console.log(result);

                    if(result.success == false){
                        Toast.fire({
                            icon: 'error',
                            title: result.data[0]
                        });

                        return false;
                    }

                    if($('.freights-detail-info li').length == 0){
                        $('.freights-detail-info').append(`<li class="freights-detail title">
                            <span class="font-weight-bold">Tipo</span>
                            <span class="font-weight-bold">Entrega</span>
                            <span class="font-weight-bold">Valor</span>
                        </li>`);
                        for(let i = 0; i < result.data.length; i++){
                            name = result.data[i].name == "PAC" ? `PAC&nbsp;&nbsp;&nbsp;&nbsp;` : result.data[i].name;
                            $('.freights-detail-info').append(`
                            <li class="freights-detail ${result.data[i].name}">
                                <span> ${name}</span>
                                <span>${result.data[i].date}</span>
                                <span class="text-right">R$ ${result.data[i].price}</span>
                            </li>`);
                        }
                    } else {
                        for(let i = 0; i < result.data.length; i++) {
                            $(`li.freights-detail.${result.data[i].name} span:eq(1)`).text(result.data[i].date);
                            $(`li.freights-detail.${result.data[i].name} span:eq(2)`).text('R$' + result.data[i].price);
                        }
                    }
                    Toast.fire({
                        icon: 'success',
                        title: 'Frete calculado!'
                    })
                    enableButtonsCart();
                }, error: e => {
                    console.log(e);
                    Toast.fire({
                        icon: 'warning',
                        title: "Acorreu um problema, aguarde enquanto tentamos novamente"
                    })
                    enableButtonsCart();
                    $('.button-calcula-cep').trigger('click');
                }
            });
        })

        const loadPage = element => {

            disableButtonsCart();

            element.find('.basket-productPrice').html("<i class='fa fa-spin fa-spinner'></i>");
            $(` .freights-detail.PAC span:eq(1),
                .freights-detail.PAC span:eq(2),
                .freights-detail.Sedex span:eq(1),
                .freights-detail.Sedex span:eq(2)`
            ).html("<i class='fa fa-spin fa-spinner'></i>");
        }

        const disableButtonsCart = () => {
            $('.button-calcula-cep, .add-to-cart').attr('disabled', true)
        }
        const enableButtonsCart = () => {
            $('.button-calcula-cep, .add-to-cart').attr('disabled', false)
        }
    </script>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/magic-zoom/magiczoomplus.css') }}" />
    <link rel="stylesheet" href="{{ asset('user/css/product/style.css')}}">
    <style>
        .galery-product .featured-item{
            height: 540px !important;
        }
        .tag-free-freight {
            background-color: #000;
            border-top: 1px solid #fff
        }
        .tag-free-freight a{
            display: flex;
            justify-items: center;
            align-items: center;
            justify-content: space-between;
            min-height: 60px;
            flex-wrap: wrap;
        }
        .tag-free-freight a span{
            font-size: 21px;
            font-family: auto;
            color: rgb(255,127,39);
        }
        .tag-free-freight a span:nth-child(2){
            font-weight: bold;
            font-size: 28px;
        }
        @media (max-width: 576px) {
            .galery-product .featured-item{
                height: 100% !important;
            }
            .products-related .ripple{
                /*min-width: 40%;*/
                width: 280px;
            }
            .products-related .ripple:first-child{
                /*margin-left: 60%;*/
            }
            .add-cart-prd-main{
                margin-left: 15px !important;
                margin-right: 15px !important;
            }
        }
        @media (max-width: 767px) {
            #v-pills-1 iframe{
                width: 100%;
            }
            .tag-free-freight a{
                justify-content: center;
            }
        }

        @media (min-width: 575px) and (max-width: 767px) {
            .galery-product .featured-item{
                height: 510px !important;
            }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .galery-product .featured-item{
                height: 330px !important;
            }
        }

        @media (min-width: 992px) and (max-width: 1200px) {
            .galery-product .featured-item{
                height: 450px !important;
            }
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
                        <span class="mr-2">
                            <a href="{{ route('user.home') }}">Início <i class="fa fa-chevron-right"></i></a>
                        </span>
                        <span class="mr-2">
                            <a href="{{ route('user.products') }}">Produtos <i class="fa fa-chevron-right"></i></a>
                        </span>
                        <span>
                            {{$dataProduct['product']['name']}} <i class="fa fa-chevron-right"></i>
                        </span>
                    </p>
                    <h2 class="mb-0 bread">Produto</h2>
                </div>
            </div>
        </div>
    </section>

    <section class="col-lg-12 tag-free-freight">
        <a href="{{ route('user.policy.freight') }}">
            <span>APROVEITE A PROMOÇÃO</span>
            <span>FRETE GRÁTIS</span>
            <span>CLIQUE AQUI E DESCUBRA</span>
        </a>
    </section>

    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-5 ftco-animate galery-product">
                    <div class="feature text-center">
                        <a href="{{asset('user/img/products/'.$dataProduct['product']['imagePrimary']) }}" data-options="zoomMode: magnifier; variableZoom: true" class="MagicZoom" id="MagicZoom">
                            <img class="featured-item image-holder r-3-2 transition" src="{{asset('user/img/products/'.$dataProduct['product']['imagePrimary']) }}"/>
                        </a>
                    </div>

                    <div class="gallery-wrapper">
                        <div class="galery-btn-left" onclick="moveToLeft()"></div>
                        <div class="gallery">
                            @foreach($dataProduct['images'] as $image)
                                {{--                                <div class="item-wrapper {{$image['primary'] == 1 ? 'active' : ''}}" data-zoom-id="MagicZoom" href="{{ asset('user/img/products/'.$image['path']) }}" data-image="{{ asset('user/img/products/'.$image['path']) }}">--}}
                                {{--                                    <img class="gallery-item image-holder r-3-2 {{$image['primary'] == 1 ? 'active' : ''}} transition" src="{{ asset('user/img/products/'.$image['path']) }}"/>--}}
                                {{--                                </div>--}}

                                <div class="item-wrapper {{$image['primary'] == 1 ? 'active' : ''}}" data-zoom-id="MagicZoom" href="{{ asset('user/img/products/'.$image['path']) }}" data-image="{{ asset('user/img/products/'.$image['path']) }}">
                                    <img class="gallery-item image-holder r-3-2 {{$image['primary'] == 1 ? 'active' : ''}} transition" src="{{ asset('user/img/products/'.$image['path']) }}"/>
                                </div>
                            @endforeach
                        </div>
                        <div class="galery-btn-right" onclick="moveToRight()"></div>
                    </div>
{{--                    <a href="images/prod-1.jpg" class="image-popup prod-img-bg"><img src="images/prod-1.jpg" class="img-fluid" alt="Colorlib Template"></a>--}}
                </div>
                <div class="col-lg-6 product-details pl-md-5 ftco-animate">
                    <h3>{{$dataProduct['product']['name']}}</h3>
                    <div class="rating d-flex">
                        <p class="text-left mr-2">
                            <a href="#" class="mr-2">{{$totalRate['rate_value']}}</a>
                        </p>
                        <div class="reputation main mr-2">
                            <div class="reputation-gray">
                                <span class="fa fa-star grey"></span>
                                <span class="fa fa-star grey"></span>
                                <span class="fa fa-star grey"></span>
                                <span class="fa fa-star grey"></span>
                                <span class="fa fa-star grey"></span>
                            </div>
                            <div class="reputation-gold">
                                @for($rate = 0; $rate < 5; $rate++)
                                    @php
                                        $rating = 0;
                                         if($totalRate['rate_percentage'] <= 0) $totalRate['rate_percentage'] = 0;
                                         if($totalRate['rate_percentage'] > 0){

                                             if($totalRate['rate_percentage'] >= 20) $rating = 20;
                                             else $rating = $totalRate['rate_percentage'];

                                             $totalRate['rate_percentage'] -= 20;
                                         }
                                    @endphp
                                    <span class="fa fa-star gold" style="width:{{$rating}}%"></span>
                                @endfor
                            </div>
                        </div>
                        <p class="text-left mr-4">
                            <p class="mr-2" style="color: #000;">{{ $totalRate['rate_total'] }} <span style="color: #bbb;">Avaliações</span></p>
                        </p>
                    </div>
                    <p class="price">
                        @if ($dataProduct['product']['use_value_high'])
                            <span class="price-sale">R$ {{$dataProduct['product']['value_high']}}</span>
                        @endif
                        <span>R$ {{$dataProduct['product']['value']}}</span>
                    </p>
                    <div class="row mt-4 d-flex justify-content-between">
                        <div class="input-group col-md-6 d-flex mb-3">
                            <span class="input-group-btn mr-2">
                                <button type="button" class="quantity-left-minus btn"  data-type="minus" data-field="">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </span>
                            <input type="text" id="quantity" name="quantity" class="quantity form-control input-number qty-prdduct" value="1" min="1" max="100">
                            <span class="input-group-btn ml-2">
                                <button type="button" class="quantity-right-plus btn" data-type="plus" data-field="">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </span>
                        </div>

                        <button type="button" product-id="{{$dataProduct['product']['id']}}" class="add-to-cart formated btn btn-primary py-3 add-cart-prd-main col-xs-12 col-md-4">ADD Carrinho</button>
                    </div>

                    <div class="btns-freights-product">
                        <div class="freights-cep btn-group-icon col-md-8" product-id="{{$dataProduct['product']['id']}}">
                            <input type="text" class="form-control input-cep" value="{{isset($_SESSION['cep']) ? $_SESSION['cep'] : ""}}">
                            <button class="btn btn-primary button-calcula-cep">CALCULAR</button>
                            <small class="link-search-cep"><a href="http://www.buscacep.correios.com.br/sistemas/buscacep/" target="_blank" class="link-primary">Não sei meu CEP</a></small>
                        </div>
                        <div class="freights-info col-md-8">
                            <small style="color: #000" class="font-weight-bold">Valores e prazos baseado em apenas uma unidade!</small>
                            <ul class="freights-detail-info no-padding">
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-12 nav-link-wrap">
                    <div class="nav nav-pills d-flex text-center" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link ftco-animate active mr-lg-1" id="v-pills-1-tab" data-toggle="pill" href="#v-pills-1" role="tab" aria-controls="v-pills-1" aria-selected="true">Descrição</a>

                        <a class="nav-link ftco-animate" id="v-pills-3-tab" data-toggle="pill" href="#v-pills-3" role="tab" aria-controls="v-pills-3" aria-selected="false">Avaliações</a>
                    </div>
                </div>
                <div class="col-md-12 tab-wrap">

                    <div class="tab-content bg-light" id="v-pills-tabContent">

                        <div class="tab-pane fade show active" id="v-pills-1" role="tabpanel" aria-labelledby="day-1-tab">
                            <div class="p-4">
                                {!! $dataProduct['product']['description'] !!}
                            </div>
                        </div>
                        <div class="tab-pane fade" id="v-pills-3" role="tabpanel" aria-labelledby="v-pills-day-3-tab">
                            <div class="row p-4">
                                <div class="col-md-12">
                                    <h3 class="mb-4">{{ $totalRate['rate_total'] }} Avaliações</h3>

                                    @if(count($arrRates) == 0)
                                        <h5 class="col-md-12 text-center">Esse produto ainda não tem avaliações</h5>
                                    @else
                                        @foreach( $arrRates as $rate )
                                            <div class="review">
                                                <div class="desc">
                                                    <h4 class="d-flex flex-nowrap">
                                                        <span class="text-left">{{$rate['nameUser']}}</span>
                                                        <span class="text-right">{{$rate['created_at']}}</span>
                                                    </h4>
                                                    <div class="reputation">
                                                        <div class="reputation-gray">
                                                            <span class="fa fa-star grey"></span>
                                                            <span class="fa fa-star grey"></span>
                                                            <span class="fa fa-star grey"></span>
                                                            <span class="fa fa-star grey"></span>
                                                            <span class="fa fa-star grey"></span>
                                                        </div>
                                                        <div class="reputation-gold">
                                                            @php $rate['rate'] = ($rate['rate']*100)/5; @endphp
                                                            @for($_rate = 0; $_rate < 5; $_rate++)
                                                                @php
                                                                    $rating = 0;
                                                                     if($rate['rate'] <= 0) $rate['rate'] = 0;
                                                                     if($rate['rate'] > 0){

                                                                         if($rate['rate'] >= 20) $rating = 20;
                                                                         else $rating = $rate['rate'];

                                                                         $rate['rate'] -= 20;
                                                                     }
                                                                @endphp
                                                                <span class="fa fa-star gold" style="width:{{$rating}}%"></span>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    <p class="no-margin">{{$rate['title']}}</p>
                                                    <h4 style="line-height: 15px;">
                                                        <span class="text-left">{{$rate['description']}}</span>
                                                    </h4>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-12 related">
                    <h3 class="title-related text-center">Produtos Relacionados</h3>
                    <hr class="hr-style">
                    <div class="related-all">
                        <div class="products-related justify-content-center flex-wrap d-flex">
                            @foreach($arrProductRelated as $product)
                                <div class="col-lg-3 col-md-6 col-xs-12 col-sm-6 ripple d-flex">
                                    <div class="product product-related ftco-animate">
                                        <div class="img d-flex align-items-center justify-content-center" style="background-image: url({{ asset("user/img/products/{$product['path']}") }});">
                                            <div class="desc">
                                                <p class="meta-prod d-flex">
                                                    <a href="#" class="d-flex align-items-center justify-content-center add-to-cart btn-product-add-cart" product-id="{{$product['id']}}"><span class="flaticon-shopping-bag"></span></a>
                                                    <a href="{{ route('user.product', ['id' => $product['id']]) }}" class="d-flex align-items-center justify-content-center btn-product-view"><span class="flaticon-visibility"></span></a>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text text-center">
                                            <h2>{{$product['name']}}</h2>
                                            <p class="mb-0">
                                                @if ($product['use_value_high'])
                                                    <span class="price price-sale">R$ {{$product['value_high']}}</span>
                                                @endif
                                                <span class="price">R$ {{$product['value']}}</span>
                                            </p>
                                            <p> <span class="price">3x R$ {{$product['parcel']}}</span></p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
