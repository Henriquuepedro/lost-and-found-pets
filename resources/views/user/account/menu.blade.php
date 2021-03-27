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
                <a class="acc-submenu-link @if($route == "user.account.orders" || $route == 'user.account.order') active @endif" href="{{ route('user.account.orders') }}">
                    <i class="fas fa-box-open icon-minhaconta"></i>
                    Pedidos
                </a>
            </li>
            <li class="acc-submenu-item">
                <a class="acc-submenu-link @if($route == "user.account.address") active @endif" href="{{ route('user.account.address') }}">
                    <i class="fas fa-map-marked-alt"></i>
                    Endereços
                </a>
            </li>
            <li class="acc-submenu-item">
                <a class="acc-submenu-link popup-with-form" href="#" data-toggle="modal" data-target="#insert-testimony">
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
