@extends('user.welcome')

@section('title', 'Listagem de Produtos')

@section('js')
{{--    <script src="https://www.mercadopago.com/v2/security.js" view="search"></script>--}}
    <script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('vendor/noUiSlider/nouislider.js') }}"></script>
    <script>
        $('select[name="animal"]').change(function () {
            $('#form-search-products').submit();
        });
        $(function () {
            $('#price-min, #price-max').on('blur', function () {
                priceSlider.noUiSlider.set([$('#price-min').val(), $('#price-max').val()]);
            })

            const priceInputMax = document.getElementById('price-max');
            const priceInputMin = document.getElementById('price-min');
            var priceSlider = document.getElementById('price-slider');
            if (priceSlider) {
                console.log(priceInputMin.value);
                console.log(priceInputMax.value);
                noUiSlider.create(priceSlider, {
                    start: [parseFloat(priceInputMin.value), parseFloat(priceInputMax.value)],
                    connect: true,
                    range: {
                        'min': parseFloat($('#min-all-price').val()),
                        'max': parseFloat($('#max-all-price').val())
                    }
                });

                priceSlider.noUiSlider.on('update', function( values, handle ) {
                    var value = values[handle];
                    handle ? priceInputMax.value = value : priceInputMin.value = value
                });
            }
            $('.input-number').each(function() {
                var $this = $(this),
                    $input = $this.find('input[type="number"]'),
                    up = $this.find('.qty-up'),
                    down = $this.find('.qty-down');

                down.on('click', function () {
                    var value = parseInt($input.val()) - 1;
                    value = value < 1 ? 1 : value;
                    $input.val(value);
                    $input.change();
                    updatePriceSlider($this , value)
                })

                up.on('click', function () {
                    var value = parseInt($input.val()) + 1;
                    $input.val(value);
                    $input.change();
                    updatePriceSlider($this , value)
                })
            });

            function updatePriceSlider(elem , value) {
                if ( elem.hasClass('price-min') ) {
                    console.log('min')
                    priceSlider.noUiSlider.set([value, null]);
                } else if ( elem.hasClass('price-max')) {
                    console.log('max')
                    priceSlider.noUiSlider.set([null, value]);
                }
            }
        })

    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/noUiSlider/nouislider.css') }}">
    <style>
        .list-products .filter .btn-search,
        .list-products .filter-mobile .btn-search{
            padding: 0px 15px;
        }
        .list-products .filter .btn-search i,
        .list-products .filter-mobile .btn-search i{
            font-size: 19px;
        }

        .input-number {
            position: relative;
        }

        .input-number input[type="number"]::-webkit-inner-spin-button, .input-number input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .input-number input[type="number"] {
            -moz-appearance: textfield;
            height: 40px;
            width: 100%;
            border: 1px solid rgba(0, 0, 0, 1);
            background-color: rgba(255, 255, 255, 1);
            padding: 0px 35px 0px 15px;
            border-radius: 0px;
        }

        .input-number .qty-up, .input-number .qty-down {
            position: absolute;
            display: block;
            width: 20px;
            height: 20px;
            border: 1px solid rgba(0, 0, 0, 1);
            background-color: rgba(255, 255, 255, 1);
            text-align: center;
            font-weight: 700;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .input-number .qty-up {
            right: 0;
            top: 0;
            border-bottom: 0px;
            border-top-right-radius: 0px;
        }

        .input-number .qty-down {
            right: 0;
            bottom: 0;
            border-bottom-right-radius: 0px;
        }

        .input-number .qty-up:hover, .input-number .qty-down:hover {
            background-color: #E4E7ED;
            color: #000;
        }
        .noUi-connect{
            background: #b7472a
        }
        .products-search,
        .products-order{
            font-size: 15px;
            line-height: 15px;
            height: 40px !important;
            border-radius: 0;
        }
        @media (max-width: 991px) {
            .filter{
                width: 100%;
                padding-left: 15px;
                padding-right: 15px;
            }
            .col-md-9-products{
                width: 100%;
                padding-left: 15px;
                padding-right: 15px;
            }
        }
        @media (min-width: 992px) {
            .filter {
                width: 25%;
                padding-left: 15px;
                padding-right: 15px;
            }
            .col-md-9-products {
                width: 75%;
                padding-left: 15px;
                padding-right: 15px;
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
                    <p class="breadcrumbs mb-0"><span class="mr-2"><a href="{{ route('user.home') }}">Início <i class="fa fa-chevron-right"></i></a></span> <span>Produtos <i class="fa fa-chevron-right"></i></span></p>
                    <h2 class="mb-0 bread">Produtos</h2>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="filter">
                    <form action="{{ route('user.products') }}" method="GET" id="form-search-products">
                        <div class="row">
                            <div class="form-group col-md-12 mt-3">
                                <label class="font-weight-bold">Por Nome: </label>
                                <input type="text" class="form-control products-search" placeholder="PROCURE POR UM PRODUTO" name="search" value="@if(isset($_GET['search'])){{$_GET['search']}}@endif" autocomplete="off">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 form-group">
                                <label class="mb-3 font-weight-bold">Por Preço: </label>
                                <div id="price-slider" class="col-md-12"></div>
                            </div>
                            <div class="d-flex justify-content-between col-md-12 form-group">
                                <div class="input-number price-min" style="width: 46%">
                                    <input id="price-min" type="number" step="0.01" value="{{$filter['price']['min'] == null ? $valuesMinMax['min'] : $filter['price']['min']}}" name="priceStart">
                                    <span class="qty-up">+</span>
                                    <span class="qty-down">-</span>
                                </div>
                                <span>-</span>
                                <div class="input-number price-max" style="width: 46%">
                                    <input id="price-max" type="number" step="0.01" value="{{$filter['price']['max'] == null ? $valuesMinMax['max'] : $filter['price']['max']}}" name="priceFinish">
                                    <span class="qty-up">+</span>
                                    <span class="qty-down">-</span>
                                </div>
                            </div>
                            <div class="col-md-12 form-group">
                                <button type="submit" class="btn btn-primary py-2 px-5 col-md-12">Filtrar</button>
                                <a href="{{ route('user.products') }}" class="btn btn-secondary py-2 px-5 col-md-12 mt-3">Limpar</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-9-products">
                    <div class="row d-flex justify-content-end">
                        <div class="form-group col-md-12 col-lg-4 col-xs-12 col-sm-12 mt-3">
                            {{--                                <h4 class="font-weight-bold">Ver Primero: </h4>--}}
                            <select class="form-control products-order" name="order">
                                <option @if((isset($_GET['animal']) && $_GET['animal'] == "") || !isset($_GET['animal'])) selected @endif value="">Ordenar ...</option>
                                <option @if(isset($_GET['animal']) && $_GET['animal'] == "price_min_max")) selected @endif value="price_min_max">Menor Preço</option>
                                <option @if(isset($_GET['animal']) && $_GET['animal'] == "price_max_min")) selected @endif value="price_max_min">Maior Preço</option>
                                <option @if(isset($_GET['animal']) && $_GET['animal'] == "name_a_z")) selected @endif value="name_a_z">De A à Z</option>
                                <option @if(isset($_GET['animal']) && $_GET['animal'] == "name_z_a")) selected @endif value="name_z_a">De Z à A</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">

                        @if(count($arrProduct) > 0)
                            @foreach($arrProduct as $product)

                                <div class="col-xs-12 col-sm-6 col-md-4 d-flex">
                                    <div class="product ftco-animate">
                                        <div class="img d-flex align-items-center justify-content-center" style="background-image: url({{ asset("user/img/products/{$product['path_image']}") }});">
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
                                            <p> <span class="price">3x R$ {{$product['value_parcel']}}</span></p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-md-12 text-center">
                                <h2>Não foram contrados produtos.</h2>
                                <a href="{{route('user.home')}}" class="link-primary cursor-pointer">Voltar para página inicial</a>
                                <span> ou </span>
                                <a href="{{route('user.products')}}" class="link-primary cursor-pointer">ir para todos os produto</a>.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <input type="hidden" id="min-all-price" value="{{$valuesMinMax['min']}}">
    <input type="hidden" id="max-all-price" value="{{$valuesMinMax['max']}}">

@endsection
