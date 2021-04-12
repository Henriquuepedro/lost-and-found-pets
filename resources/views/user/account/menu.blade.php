@php
$route = Route::getCurrentRoute()->getName();
@endphp
<div class="acc-navigation">
    <div class="acc-submenu">
        <ul class="acc-submenu-list">
            <li class="acc-submenu-item">
                <a class="acc-submenu-link @if($route == "user.account") active @endif" href="{{ route('user.account') }}">
                    <i class="fas fa-user-alt icon-minhaconta"></i>
                    Minha Conta
                </a>
            </li>
            <li class="acc-submenu-item">
                <a class="acc-submenu-link @if($route == "user.account.animals" || $route == 'user.account.animal') active @endif" href="{{ route('user.account.animals') }}">
                    <i class="fas fa-search-location icon-minhaconta"></i>
                    Animais
                </a>
            </li>
            <li class="acc-submenu-item">
                <a class="acc-submenu-link @if($route == "user.account.chat") active @endif" href="{{ route('user.account.chat') }}">
                    <i class="fas fa-comments icon-minhaconta"></i>
                    Chat
                </a>
            </li>
            <li class="acc-submenu-item">
                <a class="acc-submenu-link popup-with-form" href="#" data-toggle="modal" data-target="#testimony">
                    <i class="fas fa-medal"></i>
                    Dê seu Depoimento
                </a>
            </li>
            <li class="acc-submenu-item">
                <a class="acc-submenu-link @if($route == "user.account.edit") active @endif" href="{{ route('user.account.edit') }}">
                    <i class="fas fa-user-cog"></i>
                    Cadastro
                </a>
            </li>
            <li class="acc-submenu-item">
                <a class="acc-submenu-link" href="{{ route('user.logout') }}">
                    <i class="fas fa-sign-out-alt"></i>
                    Sair
                </a>
            </li>
        </ul>
    </div>
</div>

<style>
    .input-group {
        position: relative;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        -ms-flex-align: stretch;
        align-items: stretch;
        width: 100%;
        margin-bottom: 20px;
    }
    .input-group>.custom-file, .input-group>.custom-select, .input-group>.form-control, .input-group>.form-control-plaintext {
        position: relative;
        -ms-flex: 1 1 0%;
        flex: 1 1 0%;
        min-width: 0;
        margin-bottom: 0;
    }
    .custom-file {
        position: relative;
        display: inline-block;
        width: 100%;
        height: calc(2.25rem + 2px);
        margin-bottom: 0;
    }
    .input-group>.custom-file {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
    }
    .custom-file-input {
        cursor: pointer;
    }
    .custom-file-input {
        position: relative;
        z-index: 2;
        width: 100%;
        height: calc(2.25rem + 2px);
        margin: 0;
        opacity: 0;
    }
    .custom-file-label {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1;
        height: calc(2.25rem + 10px);
        padding: .675rem .75rem;
        font-weight: 400;
        line-height: 2.1;
        color: #495057;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 0;
        box-shadow: none;
    }
    .custom-file-label {
        transition: background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    }
    .input-group>.custom-file:not(:last-child) .custom-file-label, .input-group>.custom-file:not(:last-child) .custom-file-label::after {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    .input-group>.custom-file:not(:last-child) .custom-file-label, .input-group>.custom-file:not(:last-child) .custom-file-label::after {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    .custom-file-input:lang(en)~.custom-file-label::after {
        content: "Selecionar";
    }
    .custom-file-label::after {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        z-index: 3;
        display: block;
        height: 2.75rem;
        padding: .375rem .75rem;
        line-height: 1.5;
        color: #495057;
        content: "Browse";
        background-color: #e9ecef;
        border-left: inherit;
        border-radius: 0;
        border-right: .1px solid #ced4da;
    }
    div[class^="star-rating"] label {
        cursor:pointer;
    }
    div[class^="star-rating"] label input{
        display:none;
    }
    div[class^="star-rating"] label i {
        font-size:25px;
        -webkit-transition-property:color, text;
        -webkit-transition-duration: .2s, .2s;
        -webkit-transition-timing-function: linear, ease-in;
        -moz-transition-property:color, text;
        -moz-transition-duration:.2s;
        -moz-transition-timing-function: linear, ease-in;
        -o-transition-property:color, text;
        -o-transition-duration:.2s;
        -o-transition-timing-function: linear, ease-in;
    }
    div[class^="star-rating"] label i:before {
        content:'\f005';
    }
    div[class^="star-rating"] label i.active {
        color:gold;
    }
</style>
<div class="modal" tabindex="-1" role="dialog" id="testimony">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('user.account.testimony') }}" enctype="multipart/form-data" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Faça um depoimento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 form-group">
                            <textarea name="testimony" class="form-control" placeholder="Digite seu depoimento" rows="5" required></textarea>
                        </div>
                        <div class=" form-group col-xl-12 col-md-12 form-group">
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="picture" name="picture" required>
                                    <label class="custom-file-label" id="labelTestimony" for="exampleInputFile">Selecione uma imagem sobre o depoimento</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 mb-4 text-center">
                            <div class="star-rating">
                                <label>
                                    <input type="radio" name="rate" value="1" required/>
                                    <i class="fa"></i>
                                </label>
                                <label>
                                    <input type="radio" name="rate" value="2" required/>
                                    <i class="fa"></i>
                                </label>
                                <label>
                                    <input type="radio" name="rate" value="3" required/>
                                    <i class="fa"></i>
                                </label>
                                <label>
                                    <input type="radio" name="rate" value="4" required/>
                                    <i class="fa"></i>
                                </label>
                                <label>
                                    <input type="radio" name="rate" value="5" required/>
                                    <i class="fa"></i>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success">Depor</button>
                </div>
                {!! csrf_field() !!}
            </form>
        </div>
    </div>
</div>
