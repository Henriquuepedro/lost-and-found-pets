@extends('user.welcome')

@section('title', 'Pedidos')

@section('js')
    <script>

        $('.acc-animal-header').click(function () {

            const element = $(this).closest('.acc-animal-card');
            $(this).parent().toggleClass('acc-delivery-cont-close acc-delivery-cont-open');
        })

    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('user/css/minhaconta/style.css')}}">
@endsection

@section('body')

    <div class="main">
        <div class="wrap">
            <div class="row">
                <div class="col-md-3 float-left">
                    <div class="acc-container-content col-md-12">
                        @include('user.account.menu')
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="acc-container">
                        <div class="acc-container-content col-md-12">
                            <div class="acc-content-column">

                                @if(count($dataAnimals) > 0)

                                <div class="acc-content-wrapper">
                                    <div class="acc-order-container">
                                        <div class="row">
                                            <div class="col-md-12 d-flex justify-content-between flex-wrap align-items-center">
                                                <h1 class="mt-0">Registros Realizados</h1>
                                                <a href="{{ route('user.account.animals.new') }}" class="btn btn-primary mr-0">Novo Registro</a>
                                            </div>
                                        </div>

                                        @foreach($dataAnimals as $animal)
                                        <div class="acc-order-card">
                                            <div class="acc-delivery-cont acc-delivery-cont-delivered  acc-delivery-cont-close">
                                                <div class="acc-order-header danger acc-animal-header">
                                                    <span class="acc-order-header-icon"></span>
                                                    <div class="acc-order-header-info">
                                                        <span class="acc-order-header-info-status">
                                                            {{ $animal['name'] }}
                                                        </span>

                                                        <span>
                                                            Registrado em:
                                                            <strong class="acc-delivery-prevision-days delivered">{{ date('d/m/Y H:i', strtotime($animal['created_at'])) }}</strong>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="acc-delivery-body acc-delivery-body-prod-open danger">
                                                    <div class="acc-order-info-cont acc-order-info-cont-delivered">
                                                        <ul class="acc-delivery-list">
                                                            <li class="acc-order-item-cont">
                                                                <div class="acc-order-product">
                                                                    <a target="_blank" rel="noopener noreferrer" class="img-product" href="">
                                                                        <figure><img class="acc-order-product-image" src="{{ asset($animal['path'] ? "user/img/animals/{$animal['id']}/{$animal['path']}" : 'user/img/animals/sem_foto.png') }}" alt="Sem imagem"></figure>
                                                                    </a>
                                                                    <div class="acc-order-product-truncate">
                                                                    <span class="acc-order-product-info" alt="" title="">
                                                                        <a class="acc-order-product-link" target="_blank" rel="noopener noreferrer" href="">Data do desaparecimento: {{ $animal['disappearance_date'] ? date('d/m/Y H:i', strtotime($animal['disappearance_date'])) : '' }}</a>
                                                                    </span>
                                                                        <p class="acc-order-product-info"><strong>Espécie: {{$animal['species']}}<br>Cor: {{$animal['color']}}<br> Tamanho: {{$animal['size']}}</strong></p>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                        <div class="boxBtnAction">
                                                            <a class="btn btn-primary col-md-4 py-1" href="{{ route('user.account.animal', array('id' => $animal['id'])) }}">
                                                                <i class="fas fa-file-alt"></i> Ver Detalhes
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @else
                                <div class="acc-content-wrapper">
                                    <div class="acc-order-container">
                                        <h3 class="text-center">Você ainda cadastrou nenhum animal!</h3>
                                        <p class="text-center mt-4">
                                            <a href="{{route('user.home')}}" class="link-primary cursor-pointer">Voltar para página inicial</a>
                                            <span> ou </span>
                                            <a href="" class="link-primary cursor-pointer">registrar animal perdido</a>.
                                        </p>
                                    </div>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
