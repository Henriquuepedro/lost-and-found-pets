@extends('user.welcome')

@section('title', 'Sobre nós')

@section('js')
@endsection

@section('css')
@endsection

@section('body')

    <div class="main">
        <div class="wrap">
            <div class="about">
                <div class="about-top row">
                    <div class="col-md-9">
                        <div class="about-desc">
                            <p>O LOCALIZAPET é um sistema desenvolvido por dois irmãos que cursam Sistemas de Informação e que, diante da necessidade de apresentar o Trabalho de Conclusão do Curso, tiveram a ideia de criar este sistema para ajudar na localização de animais perdidos.</p>
                            <p>Esta ideia surgiu após o cachorro deles fugir de casa em 2018. Na época, foram criados anúncios no Instagram e no Facebook, mas foi percebido que nos grupos onde era anunciado esses animais perdidos também haviam diversos outros anúncios que fugiam do tema principal. Após muita conversa e muitas ideias serem colocadas apenas no papel, em 2020 surgiu o TCC e foi decidido criar o sistema para ajudar as pessoas que sofrem com a perda dos seus animais de estimação.</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h3>Depoimentos</h3>
                        <ul class="comments-custom unstyled">
                            @foreach($testimonies as $testimony)
                            <li class="comments-custom_li">
                                <div class="icon"></div>
                                <div class="right-text">
                                    <h4 class="comments-custom_h">{{ $testimony->name }}:</h4>
                                    <div class="comments-custom_txt">
                                        <a href="#" title="Go to this comment">{{ $testimony->testimony }}</a>
                                    </div>
                                    <time>{{ date('d/m/Y', strtotime($testimony->created_at)) }}</time>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

@endsection
