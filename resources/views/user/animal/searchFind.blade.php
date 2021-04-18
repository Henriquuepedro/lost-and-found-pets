@extends('user.welcome')

@section('title', 'Anunciar')

@section('js')
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script>
        $(function () {
            $('.carousel').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                fade: true,
                asNavFor: '.slider-nav'
            });

            $('.slider-nav').slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                asNavFor: '.carousel',
                dots: false,
                centerMode: true,
                focusOnSelect: true
            });

        });

        $('#sendMessage').click(function (){
            const content = $('#message').val();
            const userTo = $('#user_id').val();
            const animalTo = $('#animal_id').val();

            if (content.length === 0) {
                alert('Escreva uma mensagem.')
                return false;
            }

            $(this).attr('disabled', true);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: window.location.origin + "/queries/ajax/sendMessage",
                data: { userTo, animalTo, content },
                dataType: 'json',
                success: async () => {

                    window.location.href = '{{ route('user.account.chat') }}';

                }, error: (e) => {
                    console.log(e);
                }, complete: (e) => {
                    if (e.status === 401)
                        window.location.href = '{{ route('user.login') }}';
                }
            });
        });


    </script>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>

    <style>
        .carousel .slick-slide.slick-active {
            box-shadow: 0 0 0 0;
            border: 0 none;
            outline: 0;
        }
        .carousel .slick-slide {
            height: 350px;
        }

        .slider-nav .slick-slide {
            cursor: pointer;
            box-shadow: 0 0 0 0;
            border: 0 none;
            outline: 0;
        }

        .slider-nav .slick-slide img {
            max-height: 115px;
        }

        .slider-nav .slick-prev.slick-arrow {
            display: contents;
            color: transparent;
        }
        .slider-nav .slick-prev.slick-arrow::before {
            position: absolute;
            top: 40%;
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            content: "\f137";
            z-index: 1;
            font-size: 30px;
            color: #000;
            padding-left: 5px;
        }

        .slider-nav .slick-next.slick-arrow {
            display: contents;
            color: transparent;
        }
        .slider-nav .slick-next.slick-arrow::after {
            position: absolute;
            top: 40%;
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            content: "\f138";
            z-index: 1;
            font-size: 30px;
            color: #000;
            right: 0;
            padding-right: 20px;
        }
        .tab-content .tab-pane {
            padding: 15px;
            background: #ffe;
            border: 1px solid #dee2e6;
        }
        #specification .table tr:nth-child(1) td {
            border-top: 0 !important;
        }
        #specification .table tr td b {
            color: #C0392B
        }

        .place label {
            color: #aaa;
            font-size: 17px;
        }

        .place p {
            color: #333;
            font-size: 20px;
        }

        #dataAnimal.nav-tabs .nav-item a {
            height: 45px;
            font-size: 20px;
        }

        #dataAnimal.nav-tabs .nav-item.show .nav-link,
        #dataAnimal.nav-tabs .nav-link.active {
            background-color: #C0392B;
            color: #fff !important;
        }

        #dataAnimal.nav-tabs .nav-link:hover {
            background-color: #C0392B;
            color: #fff !important;
        }

        #dataAnimal.nav-tabs .nav-link:not(.active) {
            color: #d74a3c;
        }
        #description ol,
        #description ul {
            list-style: revert;
            margin: revert;
            padding: revert;
        }
    </style>

@endsection

@section('body')

    <div class="main">
        <div class="wrap">
            <div class="col-md-12 mb-3 place card p-3">
                <div class="col-md-12 text-center mb-2">
                    <h4 class="h4 underline col-md-12 mb-0">{{ $animal['name'] }}</h4>
                    <small><b>Data do anúncio:</b> {{ date('d/m/Y H:i', strtotime($animal['created_at'])) }}</small>
                </div>
            </div>

            <div class="col-md-12 mb-3 place card p-3">
                <div class="col-md-12 d-flex justify-content-center mb-2">


                    <div class="carousel col-md-8">
                        @if (count($imagesAnimal))
                            @foreach($imagesAnimal as $image)
                                <div class="d-flex justify-content-center"><img src="{{ asset("user/img/animals/{$image['animal_id']}/thumbnail_{$image['path']}") }}" alt="js" /></div>
                            @endforeach
                        @else
                                <div class="d-flex justify-content-center"><img src="{{ asset('user/img/animals/sem_foto.png') }}" /></div>
                        @endif
                    </div>


                </div>
                <div class="col-md-12 d-flex justify-content-center mb-3">
                    <div class="slider-nav col-md-5">
                        @if (count($imagesAnimal))
                            @foreach($imagesAnimal as $image)
                                <div class="d-flex justify-content-center"><img src="{{ asset("user/img/animals/{$image['animal_id']}/thumbnail_{$image['path']}") }}" alt="js" /></div>
                            @endforeach
                        @else
                            <div class="d-flex justify-content-center"><img src="{{ asset('user/img/animals/sem_foto.png') }}" /></div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-3 place card p-3">
                <h4 class="h4 underline col-md-12">Local do desaparecimento</h4>
                <div class="row col-md-12">
                    <div class="col-md-3">
                        <label>Cidade</label>
                        <p>{{ $animal['city_name']->name ?? 'Não informado' }}</p>
                    </div>
                    <div class="col-md-3">
                        <label>Bairro</label>
                        <p>{{ $animal['neigh_name']->name ?? 'Não informado' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label>Local de desaparecimento</label>
                        <p>{{ $animal['place'] ?? 'Não informado' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-3 card p-3">
                <h4 class="h4 underline col-md-12">Detalhes</h4>
                <ul class="nav nav-tabs" id="dataAnimal" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="description-tab" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="true">Descrição</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="specification-tab" data-toggle="tab" href="#specification" role="tab" aria-controls="specification" aria-selected="false">Característica</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="description" role="tabpanel" aria-labelledby="description-tab">
                        {!! $animal['observation'] ?? 'Não informado' !!}
                    </div>
                    <div class="tab-pane" id="specification" role="tabpanel" aria-labelledby="specification-tab">
                        <table class="table col-md-12">
                            <tbody>
                                <tr>
                                    <td style="width: 30%"><b>Espécie</b></td>
                                    <td style="width: 70%">{{ $animal['species'] }}</td>
                                </tr>
                                <tr>
                                    <td><b>Sexo</b></td>
                                    <td>{{ $animal['sex'] }}</td>
                                </tr>
                                <tr>
                                    <td><b>Idade</b></td>
                                    <td>{{ $animal['age'] }}</td>
                                </tr>
                                <tr>
                                    <td><b>Porte</b></td>
                                    <td>{{ $animal['size'] }}</td>
                                </tr>
                                <tr>
                                    <td><b>Cor</b></td>
                                    <td>{{ $animal['color'] }}</td>
                                </tr>
                                <tr>
                                    <td><b>Raça</b></td>
                                    <td>{{ $animal['race'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="col-md-12 mb-3 place card p-3">
                <h4 class="h4 underline col-md-12">Informações adicionais</h4>
                <div class="row col-md-12">
                    <div class="col-md-4">
                        <label>Data do desaparecimento</label>
                        <p>{{ $animal['disappearance_date'] ? date('d/m/Y', strtotime($animal['disappearance_date'])) : 'Não informado' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label>Telefone para Contato</label>
                        <p>{{ $animal['phone_contact'] ?? 'Não informado' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label>E-mail para contato</label>
                        <p>{{ $animal['email_contact'] ?? 'Não informado' }}</p>
                    </div>
                </div>
            </div>
            @if (!$blockChat)
            <div class="row">
                <div class="col-md-12">
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <h5 class="h5">Entre em contato pelo chat.</h5>
                    <textarea id="message" class="form-control"></textarea>
                </div>
                <div class="col-md-12 d-flex justify-content-between flex-wrap">
                    <a href="{{ route('user.animals.list') }}" class="btn btn-primary">Voltar</a>
                    <button id="sendMessage" class="btn btn-primary">Enviar Mensagem</button>
                </div>
                <input type="hidden" id="user_id" value="{{ $animal['user_created'] }}">
                <input type="hidden" id="animal_id" value="{{ $animal['id'] }}">
            </div>
            @else
                <div class="row">
                    <div class="col-md-12 text-center mt-2 mb-3">
                        <h5 class="h5">Faça o <a href="{{ route('user.login') }}">login</a> para enviar uma mensagem.</h5>
                    </div>
                    <div class="col-md-12">
                        <a href="{{ route('user.animals.list') }}" class="btn btn-primary">Voltar</a>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
