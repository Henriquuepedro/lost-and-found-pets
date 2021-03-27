@extends('user.welcome')

@section('title', 'Endereços')

@section('js')
    <script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
    <script>
        $(function () {
            $('input[name="cep"], input[name="cep_update"]').mask('00000-000');
        });

        $(document).scroll(function () {
            if ($(document).width() > 750){
                if ($(document).scrollTop() >= 325 && $(document).scrollTop() < 370) {
                    $('.acc-navigation .acc-submenu').css('padding-top', 65 - (370 - $(document).scrollTop()))
                } else if ($(document).scrollTop() >= 370) {
                    $('.acc-navigation .acc-submenu').css('padding-top', 65)
                } else {
                    $('.acc-navigation .acc-submenu').css('padding-top', 0);
                }
            }
        })

        $('input[name="cep"]').blur(function () {
            const cep = $(this).val().replace(/[^\d]+/g, '');

            if(cep.length != 8) return false;

            $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, resultado => {
                console.log(resultado);
                if(!resultado.erro){
                    const endereco = resultado.logradouro;
                    const bairro = resultado.bairro;
                    const estado = resultado.uf;
                    const cidade = resultado.localidade;

                    $('input[name="address"]').val(endereco);
                    $('input[name="neighborhood"]').val(bairro);
                    $('input[name="city"]').val(cidade);
                    $('input[name="state"]').val(estado);
                }
                if(resultado.erro){
                    alert( "CEP inválido ou inexistente!");
                }
            });
        });

        $('input[name="cep_update"]').blur(function () {
            const cep = $(this).val().replace(/[^\d]+/g, '');

            if(cep.length != 8) return false;

            $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, resultado => {
                console.log(resultado);
                if(!resultado.erro){
                    const endereco = resultado.logradouro;
                    const bairro = resultado.bairro;
                    const estado = resultado.uf;
                    const cidade = resultado.localidade;

                    $('input[name="address_update"]').val(endereco);
                    $('input[name="neighborhood_update"]').val(bairro);
                    $('input[name="city_update"]').val(cidade);
                    $('input[name="state_update"]').val(estado);
                }
                if(resultado.erro){
                    alert( "CEP inválido ou inexistente!");
                }
            });
        });
        $('.editAddress').on('click', function () {
            const elementPai = $(this).closest('.card-body');
            const address_id = elementPai.attr('address-id');

            const cep           = elementPai.find(".cep").attr('cep');
            const address       = elementPai.find(".address-number").attr('address');
            const number        = elementPai.find(".address-number").attr('number');
            const complement    = elementPai.find(".complement").attr('complement');
            const reference     = elementPai.find(".reference").attr('reference');
            const neighborhood  = elementPai.find(".neighborhood").attr('neighborhood');
            const city          = elementPai.find(".city-state").attr('city');
            const state         = elementPai.find(".city-state").attr('state');

            const elementPopUp = $('#update-form-address');

            elementPopUp.find('input[name="address_id"]').val(address_id);
            elementPopUp.find('input[name="cep_update"]').val(cep);
            elementPopUp.find('input[name="address_update"]').val(address);

            elementPopUp.find('input[name="number_update"]').val(number);
            elementPopUp.find('input[name="complement_update"]').val(complement);
            elementPopUp.find('input[name="reference_update"]').val(reference);

            elementPopUp.find('input[name="neighborhood_update"]').val(neighborhood);
            elementPopUp.find('input[name="city_update"]').val(city);
            elementPopUp.find('input[name="state_update"]').val(state);
        })
        $('.removeAddress').on('click', function () {
            const elementPai = $(this).closest('.card-body');
            const address_id = elementPai.attr('address-id');

            const address       = elementPai.find(".address-number").attr('address');
            const number        = elementPai.find(".address-number").attr('number');

            console.log(address, number);

            const elementPopUp = $('#deleteAddress');

            elementPopUp.find('input[name="address_id"]').val(address_id);
            elementPopUp.find('input[name="address_delete"]').val(address + ', ' + number);
        });

        $('#formNewAddress').on('submit', function () {
            $('button[type="submit"]', this).attr('disabled', true);
        });
    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('user/css/minhaconta/style.css')}}">
@endsection

@section('body')

    <section class="hero-wrap hero-wrap-2" style="background-image: url({{ $settings['banner'] }});" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text align-items-end justify-content-center">
                <div class="col-md-9 ftco-animate mb-5 text-center">
                    <p class="breadcrumbs mb-0">
                        <span class="mr-2"><a href="{{ route('user.home') }}">Início <i class="fa fa-chevron-right"></i></a></span>
                        <span class="mr-2"><a href="{{ route('user.account') }}">Minha Conta <i class="fa fa-chevron-right"></i></a></span>
                        <span>Endereços <i class="fa fa-chevron-right"></i></span></p>
                    <h2 class="mb-0 bread">Endereços</h2>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="acc-container">
                    <div class="acc-container-content col-md-12">
                        @include('user.account.menu')
                        <div class="acc-content-column">
                            <div class="acc-content-wrapper">
                                <div class="acc-order-container">
                                    <h2 class="contact-title">Endereços</h2>
                                    @if(session('success'))
                                        <div class="alert alert-success mt-3">{{session('success')}}</div>
                                    @endif
                                    @if(session('warning'))
                                        <div class="alert alert-danger mt-3">{{session('warning')}}</div>
                                    @endif

                                    <div class="accordion mt-3" id="accordionExample">
                                        @foreach($arrAddress as $address)
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0 col-md-10 no-padding">
                                                    <button class="btn-address col-md-12 no-padding text-left" type="button" data-toggle="collapse" data-target="#address-{{ $address['id'] }}" aria-expanded="true">
                                                        {{"{$address['address']}, {$address['number']}"}}
                                                    </button>
                                                </h5>
                                                @if($address['default'] == 1)
                                                <p class="col-md-2 text-right"><i class="fas fa-map-marker-alt"></i> Padrão</p>
                                                @endif
                                            </div>

                                            <div id="address-{{ $address['id'] }}" class="collapse" data-parent="#accordionExample">
                                                <div class="card-body" style="display: block" address-id="{{ $address['id'] }}">
                                                    <table class="table-address">
                                                        <tbody>
                                                            <tr class="address-number" address="{{$address['address']}}" number="{{$address['number']}}">
                                                                <td>Endereço: </td>
                                                                <td>{{ "{$address['address']}, {$address['number']}" }}</td>
                                                            </tr>
                                                            <tr class="complement" complement="{{ $address['complement'] }}">
                                                                <td>Complemento: </td>
                                                                <td>{{ $address['complement'] }}</td>
                                                            </tr>
                                                            <tr class="reference" reference="{{ $address['reference'] }}">
                                                                <td>Referência: </td>
                                                                <td>{{ $address['reference'] }}</td>
                                                            </tr>
                                                            <tr class="cep" cep="{{ $address['cep'] }}">
                                                                <td>CEP: </td>
                                                                <td>{{ $address['cep'] }}</td>
                                                            </tr>
                                                            <tr class="neighborhood" neighborhood="{{ $address['neighborhood'] }}">
                                                                <td>Bairro: </td>
                                                                <td>{{ $address['neighborhood'] }}</td>
                                                            </tr>
                                                            <tr class="city-state" city="{{ $address['city'] }}" state="{{ $address['state'] }}">
                                                                <td>Cidade/UF: </td>
                                                                <td>{{ "{$address['city']} / {$address['state']}" }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <div class="col-md-12 text-center mt-5">
                                                        <button class="btn btn-primary py-2 col-md-4 btn-sm editAddress" data-toggle="modal" data-target="#update-form-address">Alterar</button>
                                                        <button class="btn btn-danger py-2 col-md-4 btn-sm removeAddress" data-toggle="modal" data-target="#deleteAddress">Excluir</button>
                                                        @if($address['default'] == 0)
                                                            <form action="{{ route('user.account.address.default') }}" method="POST" class="mt-2">
                                                                <input type="hidden" name="address_id" value="{{ $address['id'] }}">
                                                                {!! csrf_field() !!}
                                                                <button type="submit" class="btn btn-success py-2 col-md-4 btn-sm">Definir Padrão</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-3">
                                        @if(isset($errors) && count($errors) > 0)
                                            <div class="alert alert-danger">
                                                <h6>Existem erros no cadastro, veja abaixo para corrigi-los.</h6>
                                                <ol>
                                                    @foreach($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ol>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-12 no-padding mt-3">
                                        <div class="row mb-3">
                                            <div class="col-sm-12">
                                                <h2 class="contact-title">Cadastrar Novo Endereço</h2>
                                            </div>
                                        </div>
                                        <form class="generalForm" action="{{ route('user.account.address.post') }}" method="post" id="formNewAddress">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>CEP</label>
                                                        <input class="form-control valid" name="cep" id="cep" type="text" value="{{ old('cep') }}" placeholder="Digite o CEP" required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-7">
                                                    <div class="form-group">
                                                        <label>Endereço</label>
                                                        <input class="form-control valid" name="address" id="address" type="text" value="{{ old('address') }}" placeholder="Digite o endereço" required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <label>Número</label>
                                                        <input class="form-control valid" name="number" id="number" type="text" value="{{ old('number') }}" placeholder="Número" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Complemento</label>
                                                        <input class="form-control valid" name="complement" id="complement" type="text" value="{{ old('complement') }}" placeholder="Digite o complemento">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Ponto de Referência</label>
                                                        <input class="form-control valid" name="reference" id="reference" type="text" value="{{ old('reference') }}" placeholder="Digite um ponto de referência">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>Bairro</label>
                                                        <input class="form-control valid" name="neighborhood" id="neighborhood" type="text" value="{{ old('neighborhood') }}" placeholder="Digite o bairro">
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>Cidade</label>
                                                        <input class="form-control valid" name="city" id="city" type="text" value="{{ old('city') }}" placeholder="Digite a cidade">
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label>Estado</label>
                                                        <input class="form-control valid" name="state" id="state" type="text" value="{{ old('state') }}" placeholder="Digite o estado">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary py-2 col-md-3">Cadastrar</button>
                                            </div>
                                            {!! csrf_field() !!}
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="update-form-address" tabindex="-1" role="dialog" aria-labelledby="update-form-address" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form class="generalForm" action="{{ route('user.account.address.edit') }}" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="update-form-address">Alterar Endereço</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-4 col-md-4 form-group">
                                <label>CEP</label>
                                <input name="cep_update" placeholder="CEP" class="form-control">
                            </div>
                            <div class="col-xl-8 col-md-8 form-group">
                                <label>Endereço</label>
                                <input name="address_update" placeholder="Endereço" class="form-control">
                            </div>
                            <div class="col-xl-4 col-md-4 form-group">
                                <label>Número</label>
                                <input name="number_update" placeholder="Número" class="form-control">
                            </div>
                            <div class="col-xl-8 col-md-8 form-group">
                                <label>Complemento</label>
                                <input name="complement_update" placeholder="Complemento" class="form-control">
                            </div>
                            <div class="col-xl-6 col-md-6 form-group">
                                <label>Referência</label>
                                <input name="reference_update" placeholder="Referência" class="form-control">
                            </div>
                            <div class="col-xl-6 col-md-6 form-group">
                                <label>Bairro</label>
                                <input name="neighborhood_update" placeholder="Bairro" class="form-control">
                            </div>
                            <div class="col-xl-6 col-md-6 form-group">
                                <label>Cidade</label>
                                <input name="city_update" placeholder="Cidade" class="form-control">
                            </div>
                            <div class="col-xl-6 col-md-6 form-group">
                                <label>Estado</label>
                                <input name="state_update" placeholder="Estado" class="form-control">
                            </div>
                            <input name="address_id" type="hidden">
                            {!! csrf_field() !!}
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-primary py-2 col-md-3" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-success py-2 col-md-3">Alterar Endereço</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteAddress" tabindex="-1" role="dialog" aria-labelledby="deleteAddress" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form class="generalForm" action="{{ route('user.account.address.delete') }}" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Excluir Endereço</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 form-group">
                                <label>Endereço</label>
                                <input name="address_delete" class="form-control" disabled>
                            </div>
                            <input name="address_id" type="hidden">
                            {!! csrf_field() !!}
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-primary py-2 col-md-3" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-danger py-2 col-md-3">Excluir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
