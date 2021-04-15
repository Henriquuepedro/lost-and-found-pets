@extends('user.welcome')

@section('title', 'Sobre nós')

@section('js')
    <script>
        var userActive = 0;
        var animalActive = 0;
        var userLogged = 0;

        $(function() {
            getNewsConversations(true);
            getNewsMessagesUsers();

            setInterval(function(){
                getNewsConversations();
                getNewsMessagesUsers();
                getNewsMessagesConversation();
            }, 5000)
        });

        const getNewsMessagesConversation = async () => {
            if (userActive && animalActive) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: window.location.origin + "/queries/ajax/getNewMessageConversation",
                    data: { user: userActive, animal: animalActive },
                    dataType: 'json',
                    success: async response => {

                        let date;

                        if (!response.length) return false;

                        const check = checkShowAlertNewMessage();

                        await $(response).each(function (key, value) {
                            date =  moment(value.created_at, "YYYY-MM-DD HH:mm").format("DD/MM/YYYY HH:mm");

                             if (!$(`.chat .chat-history li[message-id="${value.id}"]`).length) {
                                 $('.chat .chat-history ul').append(`
                                    <li message-id="${value.id}" class="d-flex justify-content-end flex-wrap">
                                        <div class="message-data align-right w-100">
                                            <span class="message-data-time">${date}</span>
                                        </div>
                                        <div class="message other-message float-right">${value.content}</div>
                                    </li>
                                `);

                             }
                        });

                        if (check)
                            $('.chat-history').animate({scrollTop: $(window).scrollTop() + $(window).height()});

                    }, error: (e) => {
                        console.log(e);
                    }
                });
            }
        }

        const getNewsMessagesUsers = async () => {
            let usersAnimals = [];
            let from;
            $('ul.list li').each(function (){
                from = parseInt($(this).attr('user-id'));

                if (!$(this).find('div.status').length && from != userActive)
                    usersAnimals.push({
                        'from': from,
                        'animal_id': parseInt($(this).attr('animal-id'))
                    });
            });

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: window.location.origin + "/queries/ajax/getNewMessages",
                data: { usersAnimals },
                dataType: 'json',
                success: async response => {

                    //console.log(response);

                    let el;

                    await $(response).each(function (key, value) {
                        if (value.from != userActive) {
                            el = $(`.list li[user-id="${value.from}"][animal-id="${value.animal_id}"]`);
                            if (!el.find('div.status').length) {
                                el.find('.name').append('<div class="status"><i class="fa fa-circle"></i></div>');
                            }
                        }
                    });

                }, error: (e) => {
                    console.log(e);
                }
            });
        }

        const getNewsConversations = async (init = false) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: window.location.origin + "/queries/ajax/getUsers",
                data: {},
                dataType: 'json',
                success: response => {

                    //console.log(response);
                    if (response.users.length == 0) {
                        $('.people-list ul.list').empty().append(`
                            <li class="text-center no-users">
                                <h4 class="text-white">Você ainda não possui conversas.</h4>
                            </li>`);
                        $('.people-list .load-users').hide();
                    }

                    let listUsers = '';

                    if (response.users.length === $('.people-list ul.list li[user-id]').length) {
                        //console.log('Nenhuma nova conversa encontrada.')
                        return false;
                    }

                    let userActiveClass = null;
                    let animalActiveClass = null;
                    let active, read = '';
                    let start = {};

                    if ($('.people-list .list li.active').length) {
                        userActiveClass = $('.list li').attr('user-id');
                        animalActiveClass = $('.list li').attr('animal-id');
                    }

                    $(response.users).each(function (key, value) {

                        if (!$(`.list li[user-id="${value.user_id}"][animal-id="${value.animal_id}"]`).length) {

                            if (value.start && init) start = {'user': value.user_id, 'animal': value.animal_id};

                            active = userActiveClass == value.user_id && animalActiveClass == value.animal_id ? 'active' : '';
                            read = value.no_read ? '<div class="status"><i class="fa fa-circle"></i></div>' : '';

                            listUsers += `
                            <li class="${active}" user-id="${value.user_id}" animal-id="${value.animal_id}" user-name="${value.user_name}">
                                <div class="about">
                                    <div class="name d-flex justify-content-start align-items-center">
                                        ${value.user_name}
                                        ${read}
                                    </div>
                                    <div class="ad">${value.name}</div>
                                </div>
                            </li>`
                        }
                    });

                    $('.people-list .load-users').hide();

                    if (!$('.people-list ul.list li[user-id]').length)
                        $('.people-list ul.list').empty();

                    $('.people-list ul.list').prepend(listUsers);

                    userLogged = response.userLogged;

                    if (start.hasOwnProperty("user")) {
                        setTimeout(() => {
                            $(`.people-list ul.list li[user-id="${start.user}"][animal-id="${start.animal}"]`).trigger('click');
                        }, 250);
                    }

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
            const scrollUser = $('.chat-history').scrollTop() + $('.chat-history').height();
            const scrollChat = $('.chat-history ul').height();

            if (scrollChat <= scrollUser) {
                $('.alert-new-message').css('display', 'none');
                return true;
            } else if(showBtn) {
                $('.alert-new-message').css('display', 'flex');
            }

            return false;
        }

        $('.people-list ul.list').on('click', 'li:not(.no-users)', function(){
            const userId = $(this).attr('user-id');
            const animalId = $(this).attr('animal-id');
            const userName = $(this).attr('user-name');

            if (userActive == userId && animalActive == animalId) return false;

            userActive = userId;
            animalActive = animalId;

            $('.people-list .list li').removeClass('active');
            $(this).closest('li').addClass('active');
            $('.chat .chat-message textarea, .chat .chat-message button').show();
            $('.chat .chat-history ul').empty();
            $('.chat .load-chat').css('display', 'flex');
            $(this).find('.status').remove();
            $('.people-list .load-users').removeAttr('style');

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
                        console.log(value);
                        date =  moment(value.created_at, "YYYY-MM-DD HH:mm").format("DD/MM/YYYY HH:mm");
                        if (value.from == userId) {
                            listMessages += `
                                <li message-id="${value.id}" class="d-flex justify-content-end flex-wrap">
                                    <div class="message-data align-right w-100">
                                        <span class="message-data-time">${date}</span>
                                    </div>
                                    <div class="message other-message">${value.content}</div>
                                </li>
                            `;
                        } else {
                            listMessages += `
                                <li message-id="${value.id}" class="d-flex justify-content-start flex-wrap">
                                    <div class="message-data w-100">
                                        <span class="message-data-time">${date}</span>
                                    </div>
                                    <div class="message my-message">${value.content}</div>
                                </li>
                            `;
                        }
                    });

                    $('.chat .chat-header .chat-about .chat-with').text(userName);
                    //$('.chat .chat-header .chat-about .chat-num-messages').text(response.length + ' mensagens');

                    $('.chat .chat-history ul').empty().append(listMessages);
                    $('.chat-history').animate({scrollTop: $(window).scrollTop() + $(window).height()});

                    $('.chat .load-chat').hide();

                    $('.people-list .load-users').hide();

                }, error: (e) => {
                    console.log(e);
                }
            });
        });

        $('#sendMessage').click(function (){
            const content = $(this).closest('.chat-message').find('textarea').val();
            const userTo = userActive;
            const animalTo = animalActive;

            if (content.length === 0) return false;

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

                    const dateSend =  moment().format("DD/MM/YYYY HH:mm");

                    await $('.chat .chat-history ul').append(`
                        <li>
                            <div class="message-data w-100">
                                <span class="message-data-time">${dateSend}</span>
                            </div>
                            <div class="message my-message">${content}</div>
                        </li>
                    `);
                    $(this).closest('.chat-message').find('textarea').val('');
                    $('.chat-history').animate({scrollTop: $(window).scrollTop() + $(window).height()}, 'slow');

                }, error: (e) => {
                    console.log(e);
                }, complete: (e) => {
                    $(this).attr('disabled', false);
                }
            });
        });

        $('.chat-history').on('scroll', function(){
            checkShowAlertNewMessage(false);
        });

        $('.alert-new-message button').click(function (){
            $('.chat-history').animate({scrollTop: $(window).scrollTop() + $(window).height()});
            $('.alert-new-message').css('display', 'hide');
        });

    </script>
@endsection

@section('css')
    <style>

        .about {
            padding: 0px;
        }
        .people-list {
            background: #444753;
            border-radius: 5px;
            padding: 0px;
        }
        .people-list .search {
            padding: 20px;
        }
        .people-list .list {
            overflow-y: scroll;
            scroll-behavior: smooth;
        }
        .people-list .list::-webkit-scrollbar,
        .chat .chat-history::-webkit-scrollbar {
            width: 10px;
        }
        .people-list .list::-webkit-scrollbar-track,
        .chat .chat-history::-webkit-scrollbar-track {
            background: #444753;
        }
        .people-list .list::-webkit-scrollbar-thumb,
        .chat .chat-history::-webkit-scrollbar-thumb {
            background: #999;
        }
        .people-list .list::-webkit-scrollbar-thumb:hover,
        .chat .chat-history::-webkit-scrollbar-thumb:hover {
            background: #666;
        }
        .people-list .list{
            overflow-y: scroll;
            scroll-behavior: smooth;
        }
        .people-list input {
            border-radius: 3px;
            border: none;
            padding: 14px;
            color: white;
            background: #6a6c75;
            width: 90%;
            font-size: 14px;
        }
        .people-list input::placeholder {
            color: #ccc;
        }
        .people-list .fa-search {
            position: relative;
            left: -25px;
            color: #fff;
        }
        .people-list ul {
            padding: 20px;
            max-height: 640px;
        }
        .people-list ul li {
            padding: 10px 0 10px 5px;
        }
        .people-list ul li.active {
            border: 1px solid;
            border-radius: 5px;
            background: #292b33;
        }
        .people-list img {
            float: left;
        }
        .people-list .about {
            float: left;
        }
        .people-list .ad {
            float: left;
        }
        .people-list .status {
            color: #4f4fe2;
            padding-left: 10px;
            font-size: 10px;
        }
        .people-list .about .name,
        .people-list .about .ad{
            color: #aaa;
        }
        .people-list .about .name {
            font-weight: bold;
        }

        .chat {
            background: #f2f5f8;
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
            color: #434651;
            padding: 0px;
        }
        .chat .chat-header {
            padding: 10px;
            border-bottom: 2px solid white;
            cursor: default !important;
        }
        .chat .chat-header img {
            float: left;
        }
        .chat .chat-header .chat-about {
            float: left;
            padding-left: 10px;
            margin-top: 6px;
        }
        .chat .chat-header .chat-with {
            font-weight: bold;
            font-size: 16px;
        }
        .chat .chat-header .chat-num-messages {
            color: #92959e;
        }
        .chat .chat-header .fa-star {
            float: right;
            color: #d8dadf;
            font-size: 20px;
            margin-top: 12px;
        }
        .chat .chat-history {
            padding: 10px 25px 10px;
            border-bottom: 2px solid white;
            overflow-y: scroll;
            height: 459px;
        }
        .chat .chat-history .message-data {
            margin-bottom: 15px;
        }
        .chat .chat-history .message-data-time {
            color: #a8aab1;
            padding-left: 6px;
        }
        .chat .chat-history .message {
            color: white;
            padding: 18px 15px;
            line-height: 15px;
            font-size: 15px;
            border-radius: 7px;
            margin-bottom: 10px;
            width: 90%;
            position: relative;
        }
        .chat .chat-history .message:after {
            bottom: 100%;
            left: 7%;
            border: solid transparent;
            content: " ";
            height: 0;
            width: 0;
            position: absolute;
            pointer-events: none;
            border-bottom-color: #86bb71;
            border-width: 10px;
            margin-left: -10px;
        }
        .chat .chat-history .my-message {
            background: #86bb71;
        }
        .chat .chat-history .other-message {
            background: #94c2ed;
        }
        .chat .chat-history .other-message:after {
            border-bottom-color: #94c2ed;
            left: 93%;
        }
        .chat .chat-message {
            padding: 30px;
        }
        .chat .chat-message textarea {
            width: 100%;
            border: none;
            padding: 10px 20px;
            font: 14px/22px "Lato", Arial, sans-serif;
            margin-bottom: 10px;
            border-radius: 5px;
            resize: none;
            display: none;
        }
        .chat .chat-message button {
            float: right;
            color: #94c2ed;
            font-size: 16px;
            text-transform: uppercase;
            border: none;
            font-weight: bold;
            background: #f2f5f8;
            display: none;
        }
        .chat .chat-message button:hover {
            color: #75b1e8;
        }

        .online,
        .offline,
        .me {
            font-size: 15px;
        }

        .online {
            color: #86bb71;
        }

        .offline {
            color: #e38968;
        }

        .me {
            color: #94c2ed;
        }

        .align-left {
            text-align: left;
        }

        .align-right {
            text-align: right;
        }

        .float-right {
            float: right;
        }

        .list li:after {
            visibility: hidden;
            display: block;
            font-size: 0;
            content: " ";
            clear: both;
            height: 0;
        }

        .list li:hover {
            cursor: pointer;
        }
        .list li:hover .name,
        .list li:hover .ad {
            color: #fff;
        }
        .people-list ul li.active .name,
        .people-list ul li.active .ad {
            color: #fff;
        }
        .people-list .load-users {
            height: 100%;
            position: absolute;
            top: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-size: 35px;
            background-color: rgba(0,0,0,0.5);
        }
        .chat .load-chat {
            display: none;
            height: 100%;
            position: absolute;
            top: 0;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-size: 35px;
            background-color: rgba(0,0,0,0.5);
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
        #sendMessage {
            min-height: 50px;
            background-color: #275b8c;
            border-bottom-right-radius: 5px;
            border-top-right-radius: 5px
        }
        #message-to-send {
            border-bottom-right-radius: 0;
            border-top-right-radius: 0
        }

        @media (max-width:768px){
            #sendMessage {
                border-radius: 0 0 5px 5px;
            }
            #message-to-send {
                border-radius: 5px 5px 0 0;
            }
        }
    </style>
@endsection

@section('body')

    <div class="main">
        <div class="wrap">
            <div class="row mb-5">

                <div class="people-list float-left col-md-3" id="people-list">
                    <ul class="list"></ul>
                    <div class="col-md-12 load-users">
                        <i class="fa fa-sync fa-spin"></i>
                    </div>
                </div>

                <div class="chat float-right col-md-9">
                    <div class="chat-header clearfix">
                        <div class="chat-about">
                            <div class="chat-with">&nbsp;</div>
                            <div class="chat-num-messages">&nbsp;</div>
                        </div>
                    </div> <!-- end chat-header -->

                    <div class="chat-history">
                        <ul class="mb-0"></ul>
                        <div class="alert-new-message">
                            <button><i class="fas fa-angle-double-down"></i> Você tem novas mensagens</button>
                        </div>
                    </div> <!-- end chat-history -->

                    <div class="chat-message clearfix d-flex justify-content-between flex-wrap">
                        <textarea name="message-to-send" class="col-md-11 mb-0" id="message-to-send" placeholder ="Digite sua mensagem" rows="3"></textarea>

                        <button class="col-md-1" id="sendMessage"><i class="fas fa-paper-plane"></i></button>

                    </div> <!-- end chat-message -->
                    <div class="col-md-12 load-chat">
                        <i class="fa fa-sync fa-spin"></i>
                    </div>

                </div> <!-- end chat -->

            </div>
        </div>

@endsection
