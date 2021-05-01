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
            loadMessageChat();

            setInterval(function(){
                getNewsMessagesConversation();
            }, 5000)

        });

        const getNewsMessagesConversation = async () => {

            const user = $('#user_id_chat').val();
            const animal = $('#animal_id_chat').val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: window.location.origin + "/queries/ajax/getNewMessageConversation",
                data: { user, animal },
                dataType: 'json',
                success: async response => {

                    let date;

                    if (!response.length) return false;

                    const check = checkShowAlertNewMessage();

                    await $(response).each(function (key, value) {
                        date =  moment(value.created_at, "YYYY-MM-DD HH:mm").format("DD/MM/YYYY HH:mm");

                        if (!$(`#messages li[message-id="${value.id}"]`).length) {
                            $('#messages ul').append(`
                                <li message-id="${value.id}" class="d-flex justify-content-end flex-wrap">
                                    <div class="message other-message float-right">${value.content}<br/><small>${date}</small></div>
                                </li>
                            `);
                        }
                    });

                    if (check)
                        $('#messages').animate({scrollTop: $(window).scrollTop() + $(window).height()});

                }, error: (e) => {
                    console.log(e);
                }
            });
        }

        const loadMessageChat = () => {
            const userId = $('#user_id_chat').val();
            const animalId = $('#animal_id_chat').val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: window.location.origin + "/queries/ajax/getMessage",
                data: { userId, animalId },
                dataType: 'json',
                success: async response => {

                    let listMessages = '';
                    let date;
                    await $(response).each(function (key, value) {
                        date =  moment(value.created_at, "YYYY-MM-DD HH:mm").format("DD/MM/YYYY HH:mm");
                        if (value.from == userId) {
                            listMessages += `
                                <li message-id="${value.id}" class="d-flex justify-content-end flex-wrap">
                                    <div class="message other-message">${value.content}<br/><small>${date}</small></div>
                                </li>
                            `;
                        } else {
                            listMessages += `
                                <li message-id="${value.id}" class="d-flex justify-content-start flex-wrap">
                                    <div class="message my-message">${value.content}<br/><small>${date}</small></div>
                                </li>
                            `;
                        }
                    });

                    $('#messages ul').empty().append(listMessages);
                    setTimeout(() => {
                        $('#messages').animate({scrollTop: $(window).scrollTop() + $(window).height()});
                    },1000);

                }, error: (e) => {
                    console.log(e);
                }
            });
        }

        /**
         * @param showBtn
         * @return boolean true=está no fim.
         */
        const checkShowAlertNewMessage = (showBtn = true) => {
            const scrollUser = $('#messages').scrollTop() + $('#messages').height();
            const scrollChat = $('#messages ul').height();

            if (scrollChat <= scrollUser || (scrollChat + 10) <= scrollUser) {
                $('.alert-new-message').hide();
                return true;
            } else if(showBtn) {
                $('.alert-new-message').css('display', 'flex');
            }

            return false;
        }

        $('.alert-new-message button').click(function (){
            $('#messages').animate({scrollTop: $(window).scrollTop() + $(window).height()});
            $('.alert-new-message').hide();
        });

        $('#sendMessage').click(function (){
            const content = $('#message').val();
            const userTo = $('#user_id_chat').val();
            const animalTo = $('#animal_id_chat').val();

            if (content.length === 0) {
                alert('Escreva uma mensagem.')
                return false;
            }

            const btn = $(this);

            btn.attr('disabled', true);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: window.location.origin + "/queries/ajax/sendMessage",
                data: { userTo, animalTo, content },
                dataType: 'json',
                success: async () => {

                    const dateSend =  moment().format("DD/MM/YYYY HH:mm");

                    await $('#messages ul').append(`
                        <li>
                            <div class="message my-message">${content}<br/><small>${dateSend}</small></div>
                        </li>
                    `);
                    $('#message').val('');
                    $('#messages').animate({scrollTop: $(window).scrollTop() + $(window).height()}, 'slow');

                }, error: (e) => {
                    console.log(e);
                    btn.attr('disabled', false);
                }, complete: (e) => {
                    if (e.status === 401)
                        window.location.href = '{{ route('user.login') }}';

                    btn.attr('disabled', false);
                }
            });
        });

        $('#closeChat').click(function (){
            $('.chat-popup').slideUp('slow');
        });

        $('#openChat').click(function (){
            $('.chat-popup').slideDown('slow');
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
        /* CHAT */
        /* Button used to open the chat form - fixed at the bottom of the page */
        .chat-popup {
            background-color: #888;
            color: white;
            padding: 16px 20px;
            opacity: 1;
            position: fixed;
            bottom: 0;
            right: 67px;
            width: 400px;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
            border: 1px solid #C0392B;
            z-index: 1;
            border-bottom: 0;
            display: none;
        }
        .chat-popup .fa-times {
            cursor: pointer;
            font-size: 25px;
        }
        #sendMessage {
            margin-top: 10px;
            background-color: #d74a3c;
            padding: 10px 15px;
            border-radius: 5px;
            border: 1px solid #fff;
            color: #fff;
        }
        #sendMessage:hover {
            background-color: #993127;
        }
        #messages {
            background-color: #fff;
            height: 250px;
            width: 100%;
            border-radius: 5px;
            padding: 5px 2px;
            overflow-y: scroll;
        }
        .alert-new-message {
            position: absolute;
            top: 85px;
            display: none;
            justify-content: center;
            width: 100%;
        }
        .alert-new-message button {
            border: 1px solid #972519;
            background-color: #c0392b;
            border-radius: 15px;
            padding: 3px 30px;
            color: #fff;
            box-shadow: 12px 12px 28px -7px #000000;
        }
        .alert-new-message button:hover {
            border: 1px solid #79190f;
            background-color: #972519;
        }
        .alert-new-message button:active {
            border: 1px solid #641109;
            background-color: #79190f;
        }
        #messages .my-message {
            background: #86bb71;
        }
        #messages .other-message {
            background: #94c2ed;
        }
        #messages .message {
            color: white;
            padding: 5px 10px;
            line-height: 15px;
            font-size: 15px;
            border-radius: 7px;
            margin-bottom: 10px;
            width: 80%;
            position: relative;
        }
        #messages .message small {
            color: #000;
        }

        #messages::-webkit-scrollbar {
            width: 10px;
        }
        #messages::-webkit-scrollbar-track {
            background: #444753;
        }
        #messages::-webkit-scrollbar-thumb {
            background: #999;
        }
        #messages::-webkit-scrollbar-thumb:hover {
            background: #666;
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
            @if ($blockChat === false)
            <div class="row">
                <div class="col-md-12">
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 d-flex justify-content-between flex-wrap">
                    <a href="{{ route('user.animals.list') }}" class="btn btn-primary">Voltar</a>
                    <button id="openChat" class="btn btn-primary">Enviar Mensagem</button>
                </div>
            </div>
            @elseif ($blockChat === null)
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-between align-items-center flex-wrap">
                        <a href="{{ route('user.account.animal', ['id' => $animal['id']]) }}">Atualizar Cadastro</a>
                        <a href="{{ route('user.animals.list') }}" class="btn btn-primary">Voltar</a>
                    </div>
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
    <div class="chat-popup">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="h4">Conversa</h4>
            <i class="fa fa-times" id="closeChat"></i>
        </div>

        <div id="messages">
            <ul></ul>
            <div class="alert-new-message">
                <button><i class="fas fa-angle-double-down"></i> Você tem novas mensagens</button>
            </div>
        </div>

        <label for="message" class="mt-3"><b>Mensagem</b></label>
        <textarea id="message" class="form-control" rows="2"></textarea>

        <div class="d-flex justify-content-end">
            <button type="button" id="sendMessage" class="">Enviar Mensagem</button>
        </div>

        <input type="hidden" id="user_id_chat" value="{{ $animal['user_created'] }}">
        <input type="hidden" id="animal_id_chat" value="{{ $animal['id'] }}">
    </div>

@endsection
