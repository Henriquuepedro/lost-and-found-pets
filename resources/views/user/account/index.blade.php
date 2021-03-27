@extends('user.welcome')

@section('title', 'Minha Conta')

@section('js')
    <script>

        $('.acc-order-header').click(function(){
            $(this).parent().toggleClass('acc-delivery-cont-close acc-delivery-cont-open')
        })

        $('.acc-order-header').click(function () {
            if($(".acc-timeline").is(':visible') === false) {
                $('.acc-timeline-progress-bar').attr('style', 'width:0%');
            }
            else {
                setTimeout(() => {
                    if($('.acc-timeline-events-event.in_progress').attr('progress-fill') >= 0) $('.acc-timeline-progress-bar').attr('style', 'width:0%')
                }, 300);
                setTimeout(() => {
                    if($('.acc-timeline-events-event.in_progress').attr('progress-fill') >= 33) $('.acc-timeline-progress-bar').attr('style', 'width:33%')
                }, 500);
                setTimeout(() => {
                    if($('.acc-timeline-events-event.in_progress').attr('progress-fill') >= 66) $('.acc-timeline-progress-bar').attr('style', 'width:66%')
                }, 1200);
                setTimeout(() => {
                    if($('.acc-timeline-events-event.in_progress').attr('progress-fill') >= 100) $('.acc-timeline-progress-bar').attr('style', 'width:100%')
                }, 1800);
            }
        })

        $(document).scroll(function () {
            if ($(document).width() > 925){
                if ($(document).scrollTop() >= 375 && $(document).scrollTop() < 570) {
                    $('.acc-navigation .acc-submenu').css('padding-top', 35 - (570 - $(document).scrollTop()))
                } else if ($(document).scrollTop() >= 570) {
                    $('.acc-navigation .acc-submenu').css('padding-top', 75)
                } else {
                    $('.acc-navigation .acc-submenu').css('padding-top', 0);
                }
            }
        })

    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('user/css/minhaconta/style.css')}}">
@endsection

@section('body')


    <div class="main">
        <div class="wrap">
            <div class="col-md-3">
                @if(isset($errors) && count($errors) > 0)
                    @foreach($errors->all() as $error)
                        <div class="alert alert-warning col-md-12 mt-2">{{ $error }}</div>
                    @endforeach
                @endif
                @if(session('success'))
                    <div class="alert alert-success col-md-12 mt-2">{{session('success')}}</div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-danger col-md-12 mt-2">{{session('warning')}}</div>
                @endif
                <div class="col-md-12 mb-5">
                    <h4>Bem-vindo, {{ auth()->guard('client')->user()->name }}</h4>
                </div>
                <div class="acc-container-content col-md-12">
                    @include('user.account.menu')
                </div>
            </div>
        </div>
    </div>
@endsection
