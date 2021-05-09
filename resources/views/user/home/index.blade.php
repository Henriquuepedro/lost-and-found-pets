@extends('user.welcome')

@section('title', 'Início')

@section('js')
    <script type="text/javascript">
        $(function() {
            $('.carousel').carousel()
        });
    </script>
@endsection

@section('css')
@endsection

@section('body')
    <div class="content">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                @foreach($arrBanners as $key => $_)
                    <li data-target="#carouselExampleIndicators" data-slide-to="{{ $key }}" class="{{ $key === 0 ? 'active' : '' }}"></li>
                @endforeach
            </ol>
            <div class="carousel-inner">
                @foreach($arrBanners as $key => $banner)
                    <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                        <img class="d-block w-100" src="{{ $banner['path'] }}" alt="" />
                    </div>
                @endforeach
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
    <div class="main">
        <div class="wrap">
            <div class="content-top">
                <div class="section group">
                    <div class="col_1_of_3 span_1_of_3">
                        <div class="thumb-pad2">
                            <div class="thumbnail">
                                <h4>Projeto - Ajude o Chico</h4>
                                <figure><img src="{{ asset('user/images/pic.jpg') }}" alt=""></figure>
                                <div class="caption">
                                    <p>Este animal se encontra em uma situação bastante difícil e foi criada uma rifa emergencial para ajuda-lo. Para mais informações clique no link abaixo.</p>
                                    <a href="#" class="btn-default btn1">Ajudar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col_1_of_3 span_1_of_3">
                        <div class="thumb-pad2">
                            <div class="thumbnail">
                                <h4>Projeto - Ajude o Chico</h4>
                                <figure><img src="{{ asset('user/images/pic1.jpg') }}" alt=""></figure>
                                <div class="caption">
                                    <p>Este animal se encontra em uma situação bastante difícil e foi criada uma rifa emergencial para ajuda-lo. Para mais informações clique no link abaixo.</p>
                                    <a href="#" class="btn-default btn1">Ajudar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col_1_of_3 span_1_of_3">
                        <div class="thumb-pad2">
                            <div class="thumbnail">
                                <h4>Projeto - Ajude o Chico</h4>
                                <figure><img src="{{ asset('user/images/pic2.jpg') }}" alt=""></figure>
                                <div class="caption">
                                    <p>Este animal se encontra em uma situação bastante difícil e foi criada uma rifa emergencial para ajuda-lo. Para mais informações clique no link abaixo.</p>
                                    <a href="#" class="btn-default btn1">Ajudar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
