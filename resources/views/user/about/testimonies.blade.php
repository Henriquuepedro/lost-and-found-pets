@extends('user.welcome')

@section('title', 'Depoimentos')

@section('js')
@endsection

@section('css')
    <style>
        .reputation{
            width: 100%;
        }

        .reputation{
            width: 65%;
            cursor: pointer;
            padding-left: 45%;
        }
        .reputation .fa-star.grey{
            color: #555;
            font-size: 19px;
        }
        .reputation .fa-star.gold{
            color: gold;
            font-size: 19px;
        }
        .reputation .reputation-gold{
            font-size: 13px;
            display: flex;
            justify-content: start;
            position: relative;
            top: -19px;
        }
        .reputation .reputation-gray{
            font-size:13px;
            display: flex;
            justify-content: start;
        }
        .reputation .reputation-gold span{
            border: none;
            text-decoration: none;
            outline: none;
            overflow: hidden;
            display: inline-block;
        }
        .reputation .reputation-gray span{
            width: 20%;
        }
        .owl-prev i, .owl-next i{
            font-size: 30px;
        }
        .reputation.testimony-geral{
            width: 55%;
            padding-left: 45%;
        }
        .testimony{
            margin-bottom: 30px;
        }
        .testimony:first-child{
            margin-top: 50px;
        }
        .hr-new{
            background: linear-gradient(to left, currentColor calc(50% - 16px), transparent calc(50% - 16px), transparent calc(50% + 16px), currentColor calc(50% + 16px));
            background-color: transparent !important;
            border: none;
            height: 1px;
            overflow: visible;
            position: relative;
            margin: 4rem auto;
            color: #6d6d6d;
        }
        .hr-new::before{
            left: calc(50% - 0.5rem);
            background: currentColor;
            content: "";
            display: block;
            height: 1.6rem;
            position: absolute;
            top: calc(50% - 0.8rem);
            transform: rotate(22.5deg);
            width: 1px;
        }
        .hr-new::after{
            right: calc(50% - 0.5rem);
            background: currentColor;
            content: "";
            display: block;
            height: 1.6rem;
            position: absolute;
            top: calc(50% - 0.8rem);
            transform: rotate(22.5deg);
            width: 1px;
        }
        h4.title-testimony{
            font-size: 22px;
            font-weight: bold;
        }

        @media (max-width: 576px) {
            .reputation {
                width: 60%;
                padding-left: 30%;
            }
            .reputation.testimony-geral{
                width: 65%;
                padding-left: 30%;
            }
        }
        .testmonial_author img{
            border-radius: 50%;
        }
    </style>
@endsection

@section('body')

    <section class="hero-wrap hero-wrap-2" style="background-image: url({{ $settings['banner'] }});" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text align-items-end justify-content-center">
                <div class="col-md-9 ftco-animate mb-5 text-center">
                    <p class="breadcrumbs mb-0"><span class="mr-2"><a href="{{ route('user.home') }}">Início <i class="fa fa-chevron-right"></i></a></span> <span>Depoimentos <i class="fa fa-chevron-right"></i></span></p>
                    <h2 class="mb-0 bread">Depoimentos</h2>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="contact-title">Depoimentos de Clientes</h2>
                </div>
                <div class="col-md-6 offset-md-3">
                    @foreach( $testimonies as $testimony )
                    <div class="testimony">
                        <div class="reputation text-center">
                            <div class="reputation-gray">
                                <span class="fa fa-star grey"></span>
                                <span class="fa fa-star grey"></span>
                                <span class="fa fa-star grey"></span>
                                <span class="fa fa-star grey"></span>
                                <span class="fa fa-star grey"></span>
                            </div>
                            <div class="reputation-gold">
                                @php $testimony['rate'] = ($testimony['rate']*100)/5; @endphp
                                @for($_rate = 0; $_rate < 5; $_rate++)
                                    @php
                                        $rating = 0;
                                         if($testimony['rate'] <= 0) $testimony['rate'] = 0;
                                         if($testimony['rate'] > 0){

                                             if($testimony['rate'] >= 20) $rating = 20;
                                             else $rating = $testimony['rate'];

                                             $testimony['rate'] -= 20;
                                         }
                                    @endphp
                                    <span class="fa fa-star gold" style="width:{{$rating}}%"></span>
                                @endfor
                            </div>
                        </div>
                        <div class="single_testmonial d-flex justify-content-between">
                            <div class="testmonial_author text-center" style="width: 25%">
                                <div class="thumb">
                                    <img src="{{ asset("user/img/testimony/{$testimony['picture']}") }}" alt="">
                                </div>
                            </div>
                            <p style="width:75%">{{$testimony['testimony']}} - <strong style="color: #b7472a">{{$testimony['name']}}</strong></p>
                        </div>
                    </div>
                    <hr class="hr-new">
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
