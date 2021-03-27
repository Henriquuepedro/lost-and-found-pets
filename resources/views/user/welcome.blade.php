<!--A Design by W3layouts
Author: W3layout
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE HTML>
<html>
<head>
    <title>Free Smarty Website Template | Home :: w3layouts</title>
    <link href="{{ asset('user/css/style_1.css') }}" rel="stylesheet" type="text/css" media="all" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--slider-->
    <link href="{{ asset('user/css/slider.css') }}" rel="stylesheet" type="text/css" media="all"/>
    <script type="text/javascript" src="{{ asset('user/js/jquery-1.9.0.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('user/js/jquery.nivo.slider.js') }}"></script>
    <script type="text/javascript">
        $(window).load(function() {
            $('#slider').nivoSlider();
        });
    </script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
    @yield('css')
</head>
<body>
<div class="wrap-box"> </div>
<div class="header">
    <div class="wrap">
        <div class="header-top">
            <div class="cssmenu">
                <ul>
                    <li class="{{ Route::currentRouteName() == "user.home" ? 'active' : ''}}"><a href="{{ route('user.home') }}"><span>Início</span></a></li>
                    <li><a href="#"><span>Anunciar</span></a></li>
                    <li><a href="#"><span>Localizar</span></a></li>
                    <li class="{{ Route::currentRouteName() == "user.about" ? 'active' : ''}}"><a href="{{ route('user.about') }}"><span>Sobre</span></a></li>
                    <div class="clear"></div>
                </ul>
                <ul class="menu-small">
                    @if(auth()->guard('client')->user())
                        <li><a href="{{ route('user.account') }}"><span>Minha Conta</span></a></li><li><a href="{{ route('user.logout') }}"><span>Sair</span></a></li>
                    @else
                        <li><a href="{{ route('user.login') }}#register" class="mr-2" id="register-account"><span>Criar Conta</span></a></li><li><a href="{{ route('user.login') }}"><span>Entrar</span></a></li>
                    @endif
                </ul>
            </div>
            <div class="logo">
                <h1><a href="#">Smarty</a></h1>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>

@yield('body')

<div class="footer">
    <div class="wrap">
        <div class="footer-top">
            <div class="col_1_of_4 span_1_of_4">
                <h3>INFORMATION</h3>
                <ul class="first">
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Terms and conditions</a></li>
                    <li><a href="#">Legal Notice</a></li>
                </ul>
            </div>
            <div class="col_1_of_4 span_1_of_4">
                <h3>CATEGORIES</h3>
                <ul class="first">
                    <li><a href="#">New products</a></li>
                    <li><a href="#">top sellers</a></li>
                    <li><a href="#">Specials</a></li>
                </ul>
            </div>
            <div class="col_1_of_4 span_1_of_4">
                <h3>My ACCOUNT</h3>
                <ul class="first">
                    <li><a href="#">Your Account</a></li>
                    <li><a href="#">Personal info</a></li>
                    <li><a href="#">Prices</a></li>
                </ul>
            </div>
            <div class="col_1_of_4 span_1_of_4 footer-lastgrid">
                <h3>CONTACT US</h3>
                <ul class="follow_icon">
                    <li><a href="#"><img src="{{ asset('user/images/fb.png') }}" alt=""></a></li>
                    <li><a href="#"><img src="{{ asset('user/images/rss.png') }}" alt=""></a></li>
                    <li><a href="#"><img src="{{ asset('user/images/tw.png') }}" alt=""></a></li>
                    <li><a href="#"><img src="{{ asset('user/images/g+.png') }}" alt=""></a></li>
                </ul>
            </div>
            <div class="clear"></div>
        </div>
        <div class="copy">
            <p>Design by <a href="http://w3layouts.com">W3layouts</a></p>
        </div>
    </div>
</div>
@yield('js')
</body>
</html>
