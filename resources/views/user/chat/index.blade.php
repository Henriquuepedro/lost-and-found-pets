@extends('user.welcome')

@section('title', 'Sobre nós')

@section('js')
    <script>
        var userActive = 0;
        var animalActive = 0;

        $(function() {
            getUsers();
        })

        const getUsers = () => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: window.location.origin + "/queries/ajax/getUsers",
                data: {},
                dataType: 'json',
                success: response => {

                    let listUsers = '';
                    $(response.users).each(function (key, value) {
                        listUsers += `
                        <li class="clearfix">
                            <div class="about" user-id="${value.id}" animal-id="1" user-name="${value.name}">
                                <div class="name d-flex justify-content-start align-items-center">
                                    ${value.name}
                                    <div class="status">
                                        <i class="fa fa-circle"></i>
                                    </div>
                                </div>
                                <div class="ad">Animal Teste ${value.id}</div>
                            </div>
                        </li>`
                    })

                    $('.people-list ul.list').empty().append(listUsers);

                }, error: (e) => {
                    console.log(e);
                }
            });
        }

        $('.people-list ul.list').on('click', '.about', function(){
            const userId = $(this).attr('user-id');
            const animalId = $(this).attr('animal-id');
            const userName = $(this).attr('user-name');

            userActive = userId;
            animalActive = animalId;

            $('.people-list .list li').removeClass('active');
            $(this).closest('li').addClass('active');
            $('.chat .chat-message textarea, .chat .chat-message button').show();

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
                                <li class="clearfix">
                                    <div class="message-data align-right">
                                        <span class="message-data-time">${date}</span>
                                    </div>
                                    <div class="message other-message float-right">${value.content}</div>
                                </li>
                            `;
                        } else {
                            listMessages += `
                                <li>
                                    <div class="message-data">
                                        <span class="message-data-time">${date}</span>
                                    </div>
                                    <div class="message my-message">${value.content}</div>
                                </li>
                            `;
                        }
                    });

                    $('.chat .chat-header .chat-about .chat-with').text(userName);
                    $('.chat .chat-header .chat-about .chat-num-messages').text(response.length + ' mensagens');

                    $('.chat .chat-history ul').empty().append(listMessages);
                    $('.chat-history').animate({scrollTop: $(window).scrollTop() + $(window).height()});

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

                    const dateSend =  moment().format("YYYY-MM-DD HH:mm");

                    await $('.chat .chat-history ul').append(`
                        <li>
                            <div class="message-data">
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
        }
        .chat .chat-header {
            padding: 20px;
            border-bottom: 2px solid white;
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
            padding: 30px 30px 20px;
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
            padding: 18px 20px;
            line-height: 26px;
            font-size: 16px;
            border-radius: 7px;
            margin-bottom: 30px;
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
        .chat .chat-message .fa-file-o,
        .chat .chat-message .fa-file-image-o {
            font-size: 16px;
            color: gray;
            cursor: pointer;
        }
        .chat .chat-message button {
            float: right;
            color: #94c2ed;
            font-size: 16px;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
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

        .clearfix:after {
            visibility: hidden;
            display: block;
            font-size: 0;
            content: " ";
            clear: both;
            height: 0;
        }

        .clearfix:hover {
            cursor: pointer;
        }
        .clearfix:hover .name,
        .clearfix:hover .ad {
            color: #fff;
        }
        .people-list ul li.active .name,
        .people-list ul li.active .ad {
            color: #fff;
        }
    </style>
@endsection

@section('body')

    <div class="main">
        <div class="wrap">
            <div class="row mb-5">

                <div class="people-list float-left col-md-3" id="people-list">
                    <div class="search">
                        <input type="text" placeholder="search" />
                        <i class="fa fa-search"></i>
                    </div>
                    <ul class="list">

                    </ul>
                </div>

                <div class="chat float-right col-md-9">
                    <div class="chat-header clearfix">
                        <div class="chat-about">
                            <div class="chat-with">&nbsp;</div>
                            <div class="chat-num-messages">&nbsp;</div>
                        </div>
                    </div> <!-- end chat-header -->

                    <div class="chat-history">
                        <ul></ul>
                    </div> <!-- end chat-history -->

                    <div class="chat-message clearfix d-flex justify-content-between">
                        <textarea name="message-to-send" class="col-md-11 mb-0" id="message-to-send" placeholder ="Type your message" rows="3" style="border-bottom-right-radius: 0;border-top-right-radius: 0"></textarea>

                        <button class="col-md-1" id="sendMessage" style="background-color: #275b8c;border-bottom-right-radius: 5px;border-top-right-radius: 5px"><i class="fas fa-paper-plane"></i></button>

                    </div> <!-- end chat-message -->

                </div> <!-- end chat -->

            </div>
        </div>

@endsection
