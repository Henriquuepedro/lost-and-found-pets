@php
    if(!isset($_SESSION))session_start();

    $qty = 0;
    if(auth()->guard('client')->user()){
        $qty = \Illuminate\Support\Facades\DB::table('carts')->select(DB::raw('SUM(qty) as qty'))->where('user_id', auth()->guard('client')->user()->id)->first()->qty;
        $qty = (int)$qty;
    }
    elseif(isset($_SESSION['cart'])){
        foreach ($_SESSION['cart'] as $iten){
            $qty += (int)$iten['qty'];
        }
    }
@endphp

<!doctype html>
<html class="no-js" lang="zxx">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>@yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">



        <meta name="google-site-verification" content="" />
        <!-- Global site tag (gtag.js) - Google Analytics -->
{{--        <script async src="https://www.googletagmanager.com/gtag/js?id="></script>--}}
{{--        <script>--}}
{{--            window.dataLayer = window.dataLayer || [];--}}
{{--            function gtag(){dataLayer.push(arguments);}--}}
{{--            gtag('js', new Date());--}}

{{--            gtag('config', '');--}}
{{--        </script>--}}
        <!-- Google Tag Manager -->
{{--        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':--}}
{{--                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],--}}
{{--                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=--}}
{{--                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);--}}
{{--            })(window,document,'script','dataLayer','');--}}
{{--        </script>--}}
        <!-- End Google Tag Manager -->

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-178250251-1"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
        
          gtag('config', 'UA-178250251-1');
        </script>




        <meta name="keywords" content="Lider, Wook, Canecas, SoLove">
        <meta http-equiv="content-language" content="pt-br" />
        <meta name="author" content="SoLove">
        <meta name="description" content="" CONTENT="Author: SoLove, Category: Canecas"/>

        <meta property="og:locale" content="pt_BR" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="SoLove" />
        <meta property="og:description" content="" />
        <meta property="og:url" content="https://liderwook.com.br/" />
        <meta property="og:site_name" content="SoLove" />
        <meta property="og:updated_time" content="2020-07-01T00:00:00-03:00" />
        <meta property="og:image" content="https://liderwook.com.br/user/img/logo.png" />
        <meta property="og:image:secure_url" content="https://liderwook.com.br/user/img/logo.png" />
        <meta property="og:image:width" content="720" />
        <meta property="og:image:height" content="525" />

        <meta property="article:tag" content="Canecas" />
        <meta property="article:tag" content="SoLove" />
        <meta property="article:section" content="SoLove" />
        <meta property="article:published_time" content="2020-07-01T00:00:00-03:00" />
        <meta property="article:modified_time" content="2020-07-01T00:00:00-03:00" />

        <link rel="shortcut icon" type="image/x-{{ $settings['ext_logo'] }}" href="{{ $settings['logo'] }}">

        <!-- CSS here -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
        <link href="https://fonts.googleapis.com/css2?family=Spectral:ital,wght@0,200;0,300;0,400;0,500;0,700;0,800;1,200;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <link rel="stylesheet" href="{{ asset('user/css/animate.css')}}">

        <link rel="stylesheet" href="{{ asset('user/css/owl.carousel.min.css')}}">
        <link rel="stylesheet" href="{{ asset('user/css/owl.theme.default.min.css')}}">
        <link rel="stylesheet" href="{{ asset('user/css/magnific-popup.css')}}">

        <link rel="stylesheet" href="{{ asset('user/css/flaticon.css')}}">

        <link rel="stylesheet" href="{{ asset('user/css/style.css')}}">
        @yield('css')
    </head>
    <body>
        <!-- Google Tag Manager (noscript) -->
{{--        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>--}}
        <!-- End Google Tag Manager (noscript) -->
        <!-- Google Analytics -->
{{--        <script>--}}
{{--            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){--}}
{{--                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),--}}
{{--                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)--}}
{{--            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');--}}

{{--            ga('create', '', 'auto');--}}
{{--            ga('send', 'pageview');--}}
{{--        </script>--}}
        <!-- End Google Analytics -->

        <!-- header-start -->

        <div class="wrap">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 d-flex align-items-center">
{{--                        <p class="mb-0 phone pl-md-2">--}}
{{--                            <a href="#" class="mr-2"><span class="fa fa-phone mr-1"></span> {{ $settings['tel'] }}</a>--}}
{{--                            <a href="mailto:{{ $settings['email'] }}"><span class="fa fa-paper-plane mr-1"></span> {{ $settings['email'] }}</a>--}}
{{--                        </p>--}}
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end">
                        <div class="social-media mr-4">
                            <p class="mb-0 d-flex">
                                <a href="https://www.youtube.com/channel/UCwWAiWVBbrqmIQBT_JpDMDA" class="d-flex align-items-center justify-content-center" target="_blank"><span class="fa fa-youtube-play"><i class="sr-only">Facebook</i></span></a>
                                <a href="https://www.instagram.com/caneca.solove/" class="d-flex align-items-center justify-content-center" target="_blank"><span class="fa fa-instagram"><i class="sr-only">Instagram</i></span></a>
                            </p>
                        </div>
                        <div class="reg">
                            @if(auth()->guard('client')->user())
                                <p class="mb-0"><a href="{{ route('user.account') }}">Minha Conta</a></p>
                            @else
                            <p class="mb-0"><a href="{{ route('user.register') }}" class="mr-2">Criar Conta</a> <a href="{{ route('user.login') }}">Entrar</a></p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
            <div class="container welcome-mobile" style="background-color: #a23f25">
                @if(!auth()->guard('client')->user()) <span class="col-md-12 text-center"><a href="{{ route('user.login') }}" class="text-white">Bem-vindo! Identifique-se para fazer pedidos</a></span>@endif
            </div>
            <div class="container all">
                <div class="logo-header">
                    <a href="{{ route('user.home') }}">
                        <img src="{{ $settings['logo'] }}">
                    </a>
                    @if(!auth()->guard('client')->user()) <span><a href="{{ route('user.login') }}">Bem-vindo! Identifique-se para fazer pedidos</a></span> @endif
                </div>
                <div class="order-lg-last btn-group">
                    <a href="#" class="btn-cart dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="flaticon-shopping-bag"></span>
                        <div class="d-flex justify-content-center align-items-center"><small class="qty_cart_all">{{ $settings['cart']['qty_items'] }}</small></div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right cart-items">
                        @if(count($settings['cart']['items']) > 0)
                            <div class="content-items">
                                @foreach($settings['cart']['items'] as $iten)
                                    <div class="dropdown-item d-flex align-items-start cart-iten" product-id="{{ $iten['id'] }}">
                                        <div class="img" style="background-image: url({{ asset("user/img/products/{$iten['path_image']}") }});"></div>
                                        <div class="text pl-3">
                                            <h4><a href="{{ route("user.product", ['id' => $iten['id']]) }}">{{ $iten['name'] }}</a></h4>
                                            <p class="mb-0">
                                                <a class="price">R$ {{ $iten['value'] }}</a>
                                                <span class="quantity ml-3">Quantidade: <span>{{ $iten['qty'] }}</span></span>
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <a class="dropdown-item text-center btn-link d-block w-100 btn-open-cart-index" href="{{ route('user.cart') }}">
                                Abrir Carrinho
                                <span class="ion-ios-arrow-round-forward"></span>
                            </a>
                        @else
                            <div class="content-items">
                                <div class="dropdown-item no-items">
                                    <div class="text-center">
                                        <h5 class="no-margin">Carrinho vázio! <i class="far fa-surprise"></i></h5>
                                    </div>
                                </div>
                            </div>
                            <a class="dropdown-item text-center btn-link d-block w-100 btn-open-products-index" href="{{ route('user.products') }}">
                                Ver Produtos
                                <span class="ion-ios-arrow-round-forward"></span>
                            </a>
                        @endif
                    </div>
                </div>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="oi oi-menu"></span>
                    <i class="fas fa-bars"></i>
                </button>

                <div class="collapse navbar-collapse" id="ftco-nav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item {{ Route::currentRouteName() == "user.home" ? 'active' : ''}}"><a href="{{ route('user.home') }}" class="nav-link">Início</a></li>
                        <li class="nav-item {{ Route::currentRouteName() == "user.about" ? 'active' : ''}}"><a href="{{ route('user.about') }}" class="nav-link">Sobre</a></li>
                        <li class="nav-item {{ Route::currentRouteName() == "user.products" || Route::currentRouteName() == "user.product" ? 'active' : ''}}"><a href="{{ route('user.products') }}" class="nav-link">Produtos</a></li>
                        <li class="nav-item {{ Route::currentRouteName() == "user.contact" ? 'active' : ''}}"><a href="{{ route('user.contact') }}" class="nav-link">Contato</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- END nav -->

        @yield('body')

        <section class="pt-4 pb-4 banner-payment-sll">
            <div class="container">
                <div class="row">
                    <section class="col-lg-6 d-flex align-items-center mb-2 flex-wrap">
                        <h5 class="col-md-12 no-padding font-weight-bold">PAGUE COM</h5>
                        <img src="https://imgmp.mlstatic.com/org-img/MLB/MP/BANNERS/tipo2_575X40.jpg?v=1" class="col-md-12 no-padding">
                    </section>
                    <section class="col-lg-6">
                        <h5 class="col-md-12 no-padding font-weight-bold">SELOS</h5>
                        <div class="col-md-12 no-padding d-flex flex-wrap justify-content-start">
                            <a href="https://transparencyreport.google.com/safe-browsing/search?url={{str_replace("https://", "", str_replace("https://www", "", URL::to('/')))}}&hl=pt_BR" target="_blank"><img src="{{ asset('user/img/gallery/google-sll.png') }}" width="150"></a>
                            <a href="https://www.ssllabs.com/ssltest/analyze.html?d={{str_replace("https://", "", str_replace("https://www", "", URL::to('/')))}}" target="_blank"><img src="{{ asset('user/img/gallery/certificado-ssl.png') }}" width="150"></a>
                        </div>
                    </section>
                </div>
            </div>
        </section>
        <footer class="ftco-footer">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-sm-12 col-md">
                        <div class="ftco-footer-widget mb-4">
                            <div class="logo-footer">
                                <a href="{{ route('user.home') }}">
                                    <img src="{{ $settings['logo'] }}">
                                </a>
                            </div>
                            <p>Aqui você encontra a sua caneca de Madeira (Térmica), torneada e revestida com lâminas de alumínio.</p>
                            <ul class="ftco-footer-social list-unstyled mt-2">
                                <li class="ftco-animate"><a href="https://www.instagram.com/caneca.solove/" target="_blank"><span class="fa fa-instagram"></span></a></li>
                                <li class="ftco-animate"><a href="https://www.youtube.com/channel/UCwWAiWVBbrqmIQBT_JpDMDA" target="_blank"><span class="fa fa-youtube-play"></span></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md">
                        <div class="ftco-footer-widget mb-4 ml-md-4">
                            <h2 class="ftco-heading-2">Acesso</h2>
                            <ul class="list-unstyled">
                                <li><a href="{{ route('user.account') }}"><span class="fa fa-chevron-right mr-2"></span>Minha Conta</a></li>
                                <li><a href="{{ route('user.register') }}"><span class="fa fa-chevron-right mr-2"></span>Criar Conta</a></li>
                                <li><a href="{{ route('user.login') }}"><span class="fa fa-chevron-right mr-2"></span>Entrar</a></li>
                                <li><a href="{{ route('user.account.orders') }}"><span class="fa fa-chevron-right mr-2"></span>Meus Pedidos</a></li>
                                <li><a href="{{ route('user.products') }}"><span class="fa fa-chevron-right mr-2"></span>Produtos</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md">
                        <div class="ftco-footer-widget mb-4 ml-md-4">
                            <h2 class="ftco-heading-2">Informação</h2>
                            <ul class="list-unstyled">
                                <li><a href="{{ route('user.policy.refund') }}"><span class="fa fa-chevron-right mr-2"></span>Política de Reembolso</a></li>
                                <li><a href="{{ route('user.policy.security') }}"><span class="fa fa-chevron-right mr-2"></span>Segurança e Privacidade</a></li>
                                <li><a href="{{ route('user.policy.freight') }}"><span class="fa fa-chevron-right mr-2"></span>Fretes e Entregas</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md">
                        <div class="ftco-footer-widget mb-4">
                            <h2 class="ftco-heading-2">Fale Conosco</h2>
                            <div class="block-23 mb-3">
                                <ul>
                                    <li><a href="{{ route('user.contact') }}"><span class="fa fa-chevron-right mr-2"></span>Contato</a></li>
                                    <li><a href="{{ route('user.about') }}"><span class="fa fa-chevron-right mr-2"></span>Sobre nós</a></li>
{{--                                    <li><a href="#"><span class="icon fa fa-map marker"></span><span class="text">{!! $settings['address'] !!}</span></a></li>--}}
                                    <li><a href="#"><span class="icon fa fa-whatsapp"></span><span class="text">{{ $settings['tel'] }}</span></a></li>
                                    <li><a href="mailto:{{ $settings['email'] }}"><span class="icon fa fa-paper-plane"></span><span class="text">{{ $settings['email'] }}</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid px-0 py-5 bg-black">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="mb-0" style="color: rgba(255,255,255,.5);"><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                                Copyright &copy;<script>document.write(new Date().getFullYear());</script> Todos os direitos reservados. Por <a href="https://companyup.com.br/" target="_blank">CompanyUp.com.br</a>
                                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <!-- loader -->
        <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

        <script src="{{ asset('user/js/jquery.min.js') }}"></script>
        <script src="{{ asset('user/js/jquery-migrate-3.0.1.min.js') }}"></script>
        <script src="{{ asset('user/js/popper.min.js') }}"></script>
        <script src="{{ asset('user/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('user/js/jquery.easing.1.3.js') }}"></script>
        <script src="{{ asset('user/js/jquery.waypoints.min.js') }}"></script>
        <script src="{{ asset('user/js/jquery.stellar.min.js') }}"></script>
        <script src="{{ asset('user/js/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('user/js/jquery.magnific-popup.min.js') }}"></script>
        <script src="{{ asset('user/js/jquery.animateNumber.min.js') }}"></script>
        <script src="{{ asset('user/js/scrollax.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
        <script src="{{ asset('user/js/main.js') }}"></script>
        @yield('js')

        <script src="https://www.mercadopago.com/v2/security.js" view=
        @if (Route::currentRouteName() == 'user.home')"home"
        @elseif (Route::currentRouteName() == 'user.products')"search"
        @elseif (Route::currentRouteName() == 'user.product')"item"
        @endif></script>
    </body>
</html>
