@extends('user.welcome')

@section('title', 'Carrinho')

@section('js')
    <script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
    <script>
        var showMessageWaitResult = false;
        $(function () {
            $('input[name="cep"]').mask('00000-000');
            $('[data-toggle="tooltip"]').tooltip();
            if($('#cep').val() != "") $('#button-calcula-cep').trigger('click');
        })

        $(document).on('click', '.update-qty', function () {
            const element = $(this).closest('.basket-product');
            const product_id = element.attr('product-id');
            const qty_item   = element.find('input.qty_iten').val();
            let result;
            showMessageWaitResult = true;

            if(qty_item < 1){
                element.find('input.qty_iten').val(1);
                Toast.fire({
                    icon: 'error',
                    title: 'Quantidade mínima é de 1 unidade!'
                })
                return false;
            }
            if(qty_item > 99){
                element.find('input.qty_iten').val(99);
                Toast.fire({
                    icon: 'error',
                    title: 'Quantidade máxima é de 99 unidades!'
                })
                return false;
            }

            loadPage(element);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "./queries/ajax/updateCart",
                data: { product_id, qty_item },
                dataType: 'json',
                success: response => {

                    result = response[1];
                    if(response[0] == false){
                        Toast.fire({
                            icon: 'error',
                            title: response[1]
                        })
                        // zeraLoadPage(element);
                        result = response[2];
                        if(parseInt(response[1].split(" ")[4]) != 0){
                            element.find('input.qty_iten').val(qty_item);
                        }
                    }

                    console.log(result);

                    value_iten = result.arrItems.total;
                    value_iten_un = result.arrItems.value;
                    qty_items = result.qty_items;
                    value_total = result.value_total;

                    $('#total_cart').text('R$ ' + value_total);
                    $('#total_products').text(qty_items + ' unidades');
                    element.find('.basket-productPrice').text('R$ ' + value_iten);
                    $('.qty_cart_all').text(qty_items);
                    $(`.cart-iten[product-id="${product_id}"] a.price`).text('R$ ' + value_iten_un);
                    $(`.cart-iten[product-id="${product_id}"] .quantity span`).text(qty_item);
                    $(`.basket-product[product-id="${product_id}"] td:eq(2)`).text('R$ ' + value_iten_un);

                    if(result.dataFrete != false) calculaFrete(result.dataFrete);
                    else
                        $(` .-freightNotSelected.PAC td:eq(1),
                            .-freightNotSelected.PAC td:eq(2),
                            .-freightNotSelected.Sedex td:eq(1),
                            .-freightNotSelected.Sedex td:eq(2)`
                        ).html("-");

                    if(!result.dataFrete.errors && response[0] == true) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Carrinho Atualizado!'
                        })
                    }
                    $('.update-qty').trigger('mouseleave');

                    enableButtonsCart();

                    showMessageWaitResult = false;
                }, error: () => {
                    console.log("Acorreu um problema, aguarde enquanto tentamos novamente");
                    Toast.fire({
                        icon: 'warning',
                        title: "Acorreu um problema, aguarde enquanto tentamos novamente"
                    })
                    enableButtonsCart();
                    $('.update-qty').trigger('mouseleave');
                    element.find('.update-qty').trigger('click');
                }
            });
        });

        $(document).on('click', '.delete-product', function () {
            const element = $(this).closest('.basket-product');
            const product_id = element.attr('product-id');

            loadPage(element);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "./queries/ajax/deleteCart",
                data: { product_id },
                dataType: 'json',
                success: response => {
                    value_iten  = response.arrItems.total;
                    qty_items   = response.qty_items;
                    value_total = response.value_total;

                    $(`.cart-items .cart-iten[product-id="${product_id}"]`).remove()

                    if(qty_items == 0) {
                        $('.ftco-section .container').empty();
                        $('.ftco-section .container').append(`
                            <div class="basket-couponAndProducts__wrapper">
                                <div class="basket-productsAndFreight__wrapper text-center mt-5">
                                    <section class="animate-fade">
                                        <h2>Seu carrinho está vazio</h2>
                                        <a href="./" class="link-primary cursor-pointer">Voltar para página inicial</a>
                                        <span> ou </span>
                                        <a href="./produtos" class="link-primary cursor-pointer">escolha outros produtos</a>.
                                    </section>
                                </div>
                            </div>
                        `);
                        $('.qty_cart_all').text(qty_items);

                        $('.cart-items .content-items').append(`
                            <div class="dropdown-item no-items">
                                <div class="text-center">
                                    <h5 class="no-margin">Carrinho vázio! <i class="far fa-surprise"></i></h5>
                                </div>
                            </div>`);

                        $('.btn-open-cart-index')
                            .toggleClass('btn-open-products-index btn-open-cart-index')
                            .attr('href', window.location.origin + '/produtos')
                            .html('Ver Produtos <span class="ion-ios-arrow-round-forward"></span>');
                    } else {

                        $('#total_cart').text('R$ ' + value_total);
                        $('#total_products').text(qty_items + ' produtos');
                        $('.qty_cart_all').text(qty_items);
                        element.remove();

                        console.log(response.dataFrete);

                        if (response.dataFrete != false) calculaFrete(response.dataFrete);
                        else
                            $(` .-freightNotSelected.PAC td:eq(1),
                            .-freightNotSelected.PAC td:eq(2),
                            .-freightNotSelected.Sedex td:eq(1),
                            .-freightNotSelected.Sedex td:eq(2)`
                            ).html("-");
                    }


                    if(!response.dataFrete.errors) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Produto excluído!'
                        })
                    }

                    enableButtonsCart();
                }, error: () => {
                    Toast.fire({
                        icon: 'warning',
                        title: "Acorreu um problema, aguarde enquanto tentamos novamente"
                    })
                    enableButtonsCart();
                    element.find('.delete-product').trigger('click');
                }
            });
        })

        $('#button-calcula-cep').on('click', async function () {
            const element   = $(this).closest('#formCalculaFrete');
            const cep       = element.find('input[name="cep"]').val().replace(/[^\d]+/g, '');
            let consultaCep;
            let response;

            if(cep.length != 8){
                Toast.fire({
                    icon: 'error',
                    title: 'CEP inválido, corrija e tente novamente!'
                });
                return false;
            }

            consultaCep = await $.getJSON(`https://viacep.com.br/ws/${cep}/json/`);

            if(consultaCep.erro){
                Toast.fire({
                    icon: 'error',
                    title: 'CEP inválido, corrija e tente novamente!'
                });
                return false;
            }

            loadPage(element, false);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "./queries/ajax/defineCepUser",
                data: { cep },
                dataType: 'json',
                success: result => {

                    if(result.success == false){
                        Toast.fire({
                            icon: 'error',
                            title: result.data[0]
                        });

                        enableButtonsCart();
                        return false;
                    }

                    response = result.data;

                    calculaFrete(response);

                    Toast.fire({
                        icon: 'success',
                        title: 'Frete calculado!'
                    })

                    enableButtonsCart();
                }, error: e => {
                    console.log(e);
                    Toast.fire({
                        icon: 'warning',
                        title: "Acorreu um problema, aguarde enquanto tentamos novamente"
                    })
                    enableButtonsCart();
                    $('#button-calcula-cep').trigger('click');
                }
            });
        })

        const loadPage = (element, qnt_total = true) => {

            disableButtonsCart();

            element.find('.basket-productPrice').html("<i class='fa fa-spin fa-spinner'></i>");
            element.find('td:eq(2)').html("<i class='fa fa-spin fa-spinner'></i>");
            $(` .-freightNotSelected.PAC td:eq(1),
                .-freightNotSelected.PAC td:eq(2),
                .-freightNotSelected.Sedex td:eq(1),
                .-freightNotSelected.Sedex td:eq(2)`
            ).html("<i class='fa fa-spin fa-spinner'></i>");

            if(qnt_total == true) {
                $(` #total_products,
                    #total_cart`
                ).html("<i class='fa fa-spin fa-spinner'></i>");
            }

        }

        const zeraLoadPage = (element, qnt_total = true) => {

            enableButtonsCart();

            element.find('.basket-productPrice').html("-");
            element.find('td:eq(2)').html("-");
            $(` .-freightNotSelected.PAC td:eq(1),
                .-freightNotSelected.PAC td:eq(2),
                .-freightNotSelected.Sedex td:eq(1),
                .-freightNotSelected.Sedex td:eq(2)`
            ).html("-");

            if(qnt_total == true) {
                $(` .summary-details .summary-detail:eq(0) span:eq(0),
                    .summary-details .summary-detail:eq(0) span:eq(1),
                    #total_cart`
                ).html("-");
            }

        }

        const calculaFrete = response => {

            if(response.errors){
                for (let e = 0; e < response.errors.length; e++){
                    Toast.fire({
                        icon: 'error',
                        title: response.errors[e].message
                    })
                }
                return false;
            }

            let name;
            let nameV;
            let price;
            let date;

            for(let i = 0; i < response.length; i++){

                name    = response[i].name;
                price   = response[i].price;
                date    = response[i].date;
                nameV   = name == "PAC" ? `${name}` : name;

                if($('.-freightNotSelected.Sedex').length && name == "Sedex"){
                    $('.-freightNotSelected.Sedex td:eq(1)').text(date);
                    $('.-freightNotSelected.Sedex td:eq(2)').text('R$ ' + price);
                }
                else if($('.-freightNotSelected.PAC').length && name == "PAC"){
                    $('.-freightNotSelected.PAC td:eq(1)').text(date);
                    $('.-freightNotSelected.PAC td:eq(2)').text('R$ ' + price);
                }else{

                    $('.summary-details').append(`
                            <tr class="summary-detail -freightNotSelected ${name}">
                                <td>${nameV}</td>
                                <td>${date}</td>
                                <td class="text-right">R$ ${price}</td>
                            </tr>`
                    );
                }
            }
        }

        const disableButtonsCart = () => {
            $('.update-qty, .delete-product, #button-calcula-cep, input.qty_iten').attr('disabled', true)
        }

        const enableButtonsCart = () => {
            $('.update-qty, .delete-product, #button-calcula-cep, input.qty_iten').attr('disabled', false)
        }
    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('user/css/cart/style.css')}}">
@endsection

@section('body')

    <section class="hero-wrap hero-wrap-2" style="background-image: url({{ $settings['banner'] }});" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text align-items-end justify-content-center">
                <div class="col-md-9 ftco-animate mb-5 text-center">
                    <p class="breadcrumbs mb-0">
                        <span class="mr-2"><a href="{{ route('user.home') }}">Início <i class="fa fa-chevron-right"></i></a></span>
                        <span>Carrinho <i class="fa fa-chevron-right"></i></span></p>
                    <h2 class="mb-0 bread">Carrinho</h2>
                </div>
            </div>
        </div>
    </section>


    <section class="ftco-section">
        <div class="container">
            @if(count($dataCart['arrItems']) > 0)
            <div class="row">
                @if(isset($errors) && count($errors) > 0)
                    <div class="alert alert-danger col-md-12">
                        <h4>Sua solicitação falhou, problemas:</h4>
                        <ol>
                            @foreach($errors->all() as $error)
                                <li>{!! $error !!}</li>
                            @endforeach
                        </ol>
                    </div>
                @endif
                <div class="table-wrap col-md-12">
                    <table class="table">
                        <thead class="thead-primary">
                            <tr>
                                <th class="text-center"><i class="fa fa-picture-o"></i></th>
                                <th>Produto</th>
                                <th>Preço Un.</th>
                                <th>Quantidade</th>
                                <th>Preço Total</th>
                                <th class="text-center"><i class="fa fa-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($dataCart['arrItems'] as $iten)

                            <tr class="alert basket-product" role="alert" product-id="{{$iten['id']}}">
                                <td>
                                    <div class="d-flex justify-content-center no-padding">
                                        <a href="{{route('user.product', ['id' => $iten['id']])}}" target="_blank">
                                            <div class="img" style="background-image: url({{ asset("user/img/products/{$iten['path_image']}") }});"></div>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="email">
                                        <a href="{{route('user.product', ['id' => $iten['id']])}}" target="_blank">
                                            <span>{{$iten['name']}}</span>
                                        </a>
                                    </div>
                                </td>
                                <td>R$ {{$iten['value']}}</td>
                                <td class="quantity generalForm">
                                    <div class="input-group">
                                        <input type="number" class="quantity form-control input-number qty_iten" onkeyup="somenteNumeros(this);" min="1" max="99" value="{{$iten['qty']}}">
                                        <button class="btn btn-success update-qty" title="Atualizar" data-toggle="tooltip"><i class="fas fa-sync-alt"></i></button>
                                    </div>
                                </td>
                                <td class="basket-productPrice">R$ {{$iten['total']}}</td>
                                <td>
                                    <button type="button" class="close delete-product col-md-12" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row justify-content-end">
                <div class="col col-lg-5 col-md-6 mt-5 cart-wrap ftco-animate">
                    <div class="cart-total mb-3">
                        <h3>Resumo do Carrinho</h3>
                        <p class="d-flex">
                            <span>Produtos</span><span id="total_products">{{$dataCart['qty_items']}} unidades</span>
                        </p>
                        <p class="d-flex">
                            <span>Sub-Total</span>
                            <span id="total_cart">R$ {{$dataCart['value_total']}}</span>
                        </p>
                        <hr>
                        <p class="d-flex align-items-center justify-content-between flex-wrap generalForm" id="formCalculaFrete">
                            <label class="no-margin">
                                Calcule frete e prazo<br>
                                <a href="http://www.buscacep.correios.com.br/sistemas/buscacep/" target="_blank" class="link-primary">Não sei meu CEP</a></label>
                            <input class="form-control col-md-4" id="cep" name="cep" placeholder="Ex: 12345-678" type="tel" autocomplete="off" value="{{isset($_SESSION['cep']) ? $_SESSION['cep'] : ""}}">
                            <button class="btn btn-primary col-md-3 py-2" id="button-calcula-cep">Calcular</button>
                        </p>
                        <table class="col-md-10 offset-md-1">
                            <thead>
                                <tr>
                                    <th style="width: 30%">Frete</th>
                                    <th style="width: 40%">Entrega</th>
                                    <th class="text-right" style="width: 30%">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="summary-detail -freightNotSelected PAC">
                                    <td>PAC</td>
                                    <td>-</td>
                                    <td class="text-right">-</td>
                                </tr>
                                <tr class="summary-detail -freightNotSelected Sedex">
                                    <td>Sedex</td>
                                    <td>-</td>
                                    <td class="text-right">-</td>
                                </tr>
                            </tbody>
                        </table>
{{--                        <li class="summary-detail">--}}
{{--                            <span class="font-weight-bold">Frete</span>--}}
{{--                            <span class="font-weight-bold">Entrega</span>--}}
{{--                            <span class="font-weight-bold">Valor</span>--}}
{{--                        </li>--}}
{{--                        <li class="summary-detail -freightNotSelected PAC">--}}
{{--                            <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>--}}
{{--                            <span>-</span>--}}
{{--                            <span class="text-right">-</span>--}}
{{--                        </li>--}}
{{--                        <li class="summary-detail -freightNotSelected Sedex">--}}
{{--                            <span>Sedex</span>--}}
{{--                            <span>-</span>--}}
{{--                            <span class="text-right">-</span>--}}
{{--                        </li>--}}
                        <p class="text-center mt-3">
                            Cupom de desconto poderá ser inserido na etapa de pagamento.
                        </p>
                        <p class="text-center">
                            Pague em até 18x com juros
                        </p>
                    </div>
                    @if(auth()->guard('client')->user())
                        <p class="text-center">
                            <a href="{{route('user.animal.checkout')}}" class="btn btn-primary py-3 px-4">
                                Continuar Pagamento
                            </a>
                        </p>
                    @else
                        <p class="text-center">
                            <a href="{{ route('user.register') }}" class="btn btn-primary py-3 px-4">
                                Finalizar Compra
                            </a>
                        </p>
                    @endif
                </div>
            </div>
            @else
                <div class="basket-couponAndProducts__wrapper">
                    <div class="basket-productsAndFreight__wrapper text-center mt-5">
                        <section class="animate-fade">
                            <h2>Seu carrinho está vazio</h2>
                            <a href="{{ route('user.home') }}" class="link-primary cursor-pointer">Voltar para página inicial</a>
                            <span> ou </span>
                            <a href="{{ route('user.products') }}" class="link-primary cursor-pointer">escolha outros produtos</a>.
                        </section>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
