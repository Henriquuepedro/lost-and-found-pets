@extends('user.welcome')

@section('title', 'Finalizar Pedido')

@section('js')
    <script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
    <script src="{{ asset('vendor/icheck/icheck.js') }}"></script>
    <script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
    <script>
        (function(win,doc){
            "use strict";

            //Public Key
            window.Mercadopago.setPublishableKey("{{ PROD_KEY }}");

            //Docs Type
            window.Mercadopago.getIdentificationTypes();

            function cardBin(event) {
                let textLength = event.target.value.length;
                if(textLength >= 7){
                    let cardRepalce = event.target.value.replace(/[^\d]+/g, '');
                    let bin=cardRepalce.substring(0,6);
                    window.Mercadopago.getPaymentMethod({
                        "bin": bin
                    }, setPaymentMethodInfo);

                    Mercadopago.getInstallments({
                        "bin": bin,
                        "amount": parseFloat(document.querySelector('#totalValueOrder').value),
                    }, setInstallmentInfo);
                } else {
                    $('#cardBrandImg img').remove();
                }
            }

            if(doc.querySelector('#cardNumber')){
                let cardNumber=doc.querySelector('#cardNumber');
                cardNumber.addEventListener('keyup',cardBin,false);
            }

            //Set Installments
            function setInstallmentInfo(status, response) {
                let label=response[0].payer_costs;
                let installmentsSel=doc.querySelector('#installments');
                installmentsSel.options.length=0;

                let issuer = response[0].issuer.id;
                $('#issuer_id').val(issuer);

                label.map(function(elem,ind,obj){
                    let txtOpt=elem.recommended_message;
                    let valOpt=elem.installments;
                    installmentsSel.options[installmentsSel.options.length]=new Option(txtOpt,valOpt);
                });

            };

            //Set Payment
            function setPaymentMethodInfo(status, response) {
                if (status == 200) {
                    const paymentMethodElement = doc.querySelector('input[name=paymentMethodId]');
                    paymentMethodElement.value=response[0].id;
                    console.log(response);
                    doc.querySelector('#cardBrandImg').innerHTML="<img src='"+response[0].thumbnail+"' alt='Bandeira do Cartão'>";
                } else {
                    alert(`payment method info error: ${response}`);
                }
            }

            //Create Token
            function sendPayment(event) {
                event.preventDefault();

                let messageError = "";

                const freteSelected = $('input[name="frete_envio"]:checked').length;
                const addressSelect = $('select[name="address"]').val();

                if (addressSelect == 0 || addressSelect == "" || addressSelect == null) messageError = "Cadastre ou selecione um endereço de envio para finalizar a compra.";
                else if (freteSelected == 0) messageError = "Selecione uma forma de envio para finalizar a compra.";
                else {
                    if ($('.payment-card.active').length == 1) {

                        let cardNumber = $('#cardNumber').val();
                        const docNumber = $('#docNumber').val().replace(/[^\d]+/g, '');
                        const cvv = $('#securityCode').val();
                        const installments = $('select[name="installments"]').val();
                        const cardholderName = $('#cardholderName').val();
                        const withLastName = $('#cardholderName').val().split(" ");
                        let expirationMonth = $('#cardExpirationMonth').val();
                        let expirationYear = $('#cardExpirationYear').val();
                        cardNumber = cardNumber.replace(/ /g, "");

                        if (cardNumber.length != 16) messageError = "Número do cartão inválido.";
                        else if (withLastName.length <= 1) messageError = "Nome impresso no cartão inválido, informe o nome completo.";
                        else if (cardholderName.length <= 3) messageError = "Nome impresso no cartão inválido.";
                        else if (!validCpf(docNumber)) messageError = "CPF do proprietário do cartão inválido, informe um válido.";
                        // else if(!validDate(birth)) messageError = "Data de nascimento do titular inválido.";
                        else if (cvv.length != 3) messageError = "Código de segurança do cartão inválido.";
                        else if (installments == 0 || installments == "") messageError = "Parcela do cartão inválido.";
                        else if (expirationMonth < 0 || expirationMonth > 12) messageError = "Mês de validade do cartão inválido.";
                        else if (expirationYear < new Date().getFullYear() || expirationYear.length != 4) messageError = "Ano de validade do cartão inválido.";
                    } else if ($('.payment-billet.active').length == 1) {

                        const docNumber = $('#billetCpf').val().replace(/[^\d]+/g, '');
                        if (!validCpf(docNumber)) messageError = "CPF está inválido, informe um válido.";
                    }
                }

                if (messageError != "") {
                    Toast.fire({
                        icon: 'error',
                        title: messageError
                    });
                    $('#cardNumber').mask('0000 0000 0000 0000');
                    $('#billetCpf, #docNumber').mask('000.000.000-00');
                    return false;
                }

                $('#cardNumber').unmask();
                $('#billetCpf, #docNumber').unmask();

                window.Mercadopago.createToken(event.target, sdkResponseHandler);
            }

            function sdkResponseHandler(status, response) {
                let form = doc.querySelector('#checkout-send');

                if ((status == 200 || status == 201) && $('.payment-card.active').length == 1) {
                    let card = doc.createElement('input');
                    card.setAttribute('name', 'token');
                    card.setAttribute('type', 'hidden');
                    card.setAttribute('value', response.id);
                    form.appendChild(card);
                    form.submit();
                } else if($('.payment-card.active').length == 1) {

                    Toast.fire({
                        icon: 'error',
                        title: 'Não foi possível reconhecer os dados para pagamento, corrija e tente novamente!'
                    });
                    $('#cardNumber').mask('0000 0000 0000 0000');
                    $('#billetCpf, #docNumber').mask('000.000.000-00');

                } else if($('.payment-billet.active').length == 1) {
                    form.submit();
                }
            }

            if(doc.querySelector('#checkout-send')){
                let formPay=doc.querySelector('#checkout-send');
                formPay.addEventListener('submit',sendPayment,false);
            }
        })(window,document);

        $(function () {
            $('select[name="address"]').trigger('change');
            $('input[name="cep"], input[name="cep_update"]').mask('00000-000');

            $('input[type="radio"].icheck, input[type="checkout"].icheck').iCheck({
                checkboxClass: `icheckbox_square-orange`,
                radioClass: `iradio_square-orange`
            });
            $('#cardNumber').mask('0000 0000 0000 0000');
            $('#cardExpirationMonth').mask('00');
            $('#cardExpirationYear').mask('0000');
            $('#billetCpf, #docNumber').mask('000.000.000-00');

            $('[data-toggle="tooltip"]').tooltip()
        });

        $(document).on('click', '.editAddress', function () {
            const elementPai = $(this).closest('.address-ship');
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
        });

        $('select[name="address"]').change(function () {
            const address = $(this).val();

            loadDataAddress();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "../../queries/ajax/searchAdrress",
                data: { address },
                dataType: 'json',
                success: response => {
                    if(response == false){
                        zeraDataAddress();
                        if(!$('select[name="address"]').val())
                            $('.data-address').empty();
                        return false;
                    }

                    dataAddress = response.data_address;

                    $('.data-address').empty().append(`
                    <div class="address-ship mt-3" address-id="${address}">
                        <div class="address-ship-btns mb-3">
                            <button type="button" class="btn col-md-6 btn-primary editAddress" data-toggle="modal" data-target="#update-form-address">Alterar endereço</button>
                        </div>
                        <table class="table-address">
                            <tbody>
                                <tr class="address-number" address="${dataAddress.address}" number="${dataAddress.number}">
                                    <td>Endereço: </td>
                                    <td>${dataAddress.address}, ${dataAddress.number}</td>
                                </tr>
                                <tr class="complement" complement="${dataAddress.complement}">
                                    <td>Complemento: </td>
                                    <td>${dataAddress.complement}</td>
                                </tr>
                                <tr class="reference" reference="${dataAddress.reference}">
                                    <td>Referência: </td>
                                    <td>${dataAddress.reference}</td>
                                </tr>
                                <tr class="cep" cep="${dataAddress.cep}">
                                    <td>CEP: </td>
                                    <td>${dataAddress.cep}</td>
                                </tr>
                                <tr class="neighborhood" neighborhood="${dataAddress.neighborhood}">
                                    <td>Bairro: </td>
                                    <td>${dataAddress.neighborhood}</td>
                                </tr>
                                <tr class="city-state" city="${dataAddress.city}" state="${dataAddress.state}">
                                    <td>Cidade/UF: </td>
                                    <td>${dataAddress.city} / ${dataAddress.state}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    `);
                    $('.popup-with-form').magnificPopup({
                        type: 'inline',
                        preloader: false,
                        focus: '#name',
                        callbacks: {
                            beforeOpen: function() {
                                if($(window).width() < 700) {
                                    this.st.focus = false;
                                } else {
                                    this.st.focus = '#name';
                                }
                            }
                        }
                    });
                    if($('.h4.sem-frete').length == 0){
                        $('.fretes-select').empty();
                        fretes = response.fretes;
                        for(let i = 0; i < fretes.length; i++) {
                            frete = fretes[i];
                            $('.fretes-select').append(`
                            <div class="frete-item col-md-3">
                                <input type="radio" class="icheck" value="${frete.name}" id="frete-${frete.name}" name="frete_envio">
                                <label for="frete-${frete.name}">
                                    <strong>Envio via <span>${frete.name}</span></strong> <br>
                                    <strong>Entrega:</strong> <span>${frete.date}</span><br>
                                    <strong>Valor:</strong> R$ <span>${frete.price}</span>
                                </label>
                            </div>`);
                        }

                        $('input[type="radio"].icheck, input[type="checkout"].icheck')
                        .iCheck('destroy')
                        .iCheck({
                            checkboxClass: `icheckbox_square-orange`,
                            radioClass: `iradio_square-orange`
                        });
                    }else{
                        fretes = response.fretes;
                        for(let i = 0; i < fretes.length; i++) {
                            frete = fretes[i];
                            elFrete = $(`.frete-item label[for="frete-${frete.name}"]`);
                            elFrete.find('span:eq(0)').text(frete.name);
                            elFrete.find('span:eq(1)').text(frete.date);
                            elFrete.find('span:eq(2)').text(frete.price);
                        }
                    }

                }, error: e => {
                    console.log(e);
                    Toast.fire({
                        icon: 'warning',
                        title: "Acorreu um problema, aguarde enquanto tentamos novamente"
                    });
                    loadDataAddress();
                    $('select[name="address"]').trigger('change')
                }
                // , error: e => {
                //     console.log(e);
                //     Toast.fire({
                //         icon: 'error',
                //         title: "Acorreu um problema, caso o problema persistir contate o suporte"
                //     })
                // }
            });
        });

        $('input[name="cep"]').blur(function () {
            const cep = $(this).val().replace(/[^\d]+/g, '');

            if(cep.length != 8) return false;

            $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, resultado => {
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
                    Toast.fire({
                        icon: 'error',
                        title: 'CEP inválido, corrija e tente novamente!'
                    });
                    return false;
                }
            });
        });

        $('input[name="cep_update"]').blur(function () {
            const cep = $(this).val().replace(/[^\d]+/g, '');

            if(cep.length != 8) return false;

            $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, resultado => {
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
                    Toast.fire({
                        icon: 'error',
                        title: 'CEP inválido, corrija e tente novamente!'
                    });
                    return false;
                }
            });
        });

        $('#add-address').on('click', async function () {

            const btn = $(this);

            btn.attr('disabled', true);

            const element = $(this).closest('form');

            const cep           = element.find('[name="cep"]').val().replace(/[^\d]+/g, '');
            const address       = element.find('[name="address"]').val();
            const number        = element.find('[name="number"]').val();
            const complement    = element.find('[name="complement"]').val();
            const reference     = element.find('[name="reference"]').val();
            const neighborhood  = element.find('[name="neighborhood"]').val();
            const city          = element.find('[name="city"]').val();
            const state         = element.find('[name="state"]').val();

            let consultaCep;

            if(cep.length != 8){
                Toast.fire({
                    icon: 'error',
                    title: 'CEP inválido, corrija e tente novamente!'
                });
                btn.attr('disabled', false);
                return false;
            }

            consultaCep = await $.getJSON(`https://viacep.com.br/ws/${cep}/json/`);

            if(consultaCep.erro){
                Toast.fire({
                    icon: 'error',
                    title: 'CEP inválido, corrija e tente novamente!'
                });
                btn.attr('disabled', false);
                return false;
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "../../queries/ajax/insertAdrress",
                data: { cep, address, number, complement, reference, neighborhood, city, state },
                dataType: 'json',
                success: result => {

                    if(result.success == false){
                        msgError = "";

                        if(result.data.cep) msgError = result.data.cep[0];
                        else if(result.data.number) msgError = result.data.number[0];
                        else if(result.data.address) msgError = result.data.address[0];
                        else if(result.data.neighborhood) msgError = result.data.neighborhood[0];
                        else if(result.data.city) msgError = result.data.city[0];
                        else if(result.data.state) msgError = result.data.state[0];
                        else if(typeof result.data === 'string' || result.data instanceof String) msgError = result.data;
                        else msgError = "Erro desconhecido, tente novamente mais tarde!";

                        Toast.fire({
                            icon: 'error',
                            title: msgError
                        });
                        btn.attr('disabled', false);

                        return false;
                    }

                    reloadAddressSelect();
                    zeraDataFrete('total_prd');

                    Toast.fire({
                        icon: 'success',
                        title: result.data
                    });

                    $('#insert-form-address').modal('hide');
                    $('#insert-form-address input').val('');
                    btn.attr('disabled', false);
                }, error: () => {
                    Toast.fire({
                        icon: 'error',
                        title: "Acorreu um problema, caso o problema persistir contate o suporte"
                    })
                    btn.attr('disabled', false);
                }
            });
            return false;
        });

        $('#update-address').on('click', async function () {

            const element = $(this).closest('form');

            const cep_update            = element.find('[name="cep_update"]').val().replace(/[^\d]+/g, '');
            const address_update        = element.find('[name="address_update"]').val();
            const number_update         = element.find('[name="number_update"]').val();
            const complement_update     = element.find('[name="complement_update"]').val();
            const reference_update      = element.find('[name="reference_update"]').val();
            const neighborhood_update   = element.find('[name="neighborhood_update"]').val();
            const city_update           = element.find('[name="city_update"]').val();
            const state_update          = element.find('[name="state_update"]').val();
            const address_id            = element.find('[name="address_id"]').val();
            const address_selected      = $('[name="address"]').val();

            let consultaCep;

            if(cep_update.length != 8){
                Toast.fire({
                    icon: 'error',
                    title: 'CEP inválido, corrija e tente novamente!'
                });
                return false;
            }

            consultaCep = await $.getJSON(`https://viacep.com.br/ws/${cep_update}/json/`);

            if(consultaCep.erro){
                Toast.fire({
                    icon: 'error',
                    title: 'CEP inválido, corrija e tente novamente!'
                });
                return false;
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "../../queries/ajax/updateAdrress",
                data: { cep_update, address_update, number_update, complement_update, reference_update, neighborhood_update, city_update, state_update, address_id },
                dataType: 'json',
                success: result => {

                    if(result.success == false){
                        msgError = "";

                        if(result.data.cep_update) msgError = result.data.cep_update[0];
                        else if(result.data.number_update) msgError = result.data.number_update[0];
                        else if(result.data.address_update) msgError = result.data.address_update[0];
                        else if(result.data.neighborhood_update) msgError = result.data.neighborhood_update[0];
                        else if(result.data.city_update) msgError = result.data.city_update[0];
                        else if(result.data.state_update) msgError = result.data.state_update[0];
                        else msgError = "Erro desconhecido, tente novamente mais tarde!";

                        Toast.fire({
                            icon: 'error',
                            title: msgError
                        });

                        return false;
                    }

                    reloadAddressSelect(address_selected);
                    zeraDataFrete('total_prd');

                    Toast.fire({
                        icon: 'success',
                        title: result.data
                    });

                    $('#update-form-address').modal('hide');
                }, error: () => {
                    Toast.fire({
                        icon: 'error',
                        title: "Acorreu um problema, caso o problema persistir contate o suporte"
                    })
                }
            });
            return false;
        });

        $(document).on('ifChecked', 'input[name="frete_envio"]', function(){
            const type_frete = $(this).val();
            const cep_frete = $('[name="address"]').val();
            let verifyParcels = false;

            loadDataFrete();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "../../queries/ajax/getValueFreteUnitario",
                dataType: 'json',
                data: { type_frete, cep_frete },
                success: result => {
                    if(result == false){
                        Toast.fire({
                            icon: 'error',
                            title: 'Não foi possível recuperar o frete, recarregue a página'
                        });
                        zeraDataFrete();
                        return false;
                    }

                    $('.value-frete-selected').text('R$ ' + result.price);
                    $('.date-frete-selected').text(result.date);
                    $('.total-order').text('R$ ' + result.total_order);
                    $('.total-products').text('R$ ' + result.total_products);
                    if (result.value_cupom.status == true) {
                        $('.data-cupom').removeClass('d-none');
                        $('.data-cupom .name-cupom-total').text(result.value_cupom.cupom.toUpperCase());
                        $('.data-cupom .value-cupom-total').text('R$ ' + result.value_cupom.value);
                    } else {
                        $('.data-cupom').addClass('d-none');
                    }
                    $('#totalValueOrder').val(realToNumber(result.total_order));
                    $('button').attr('disabled', false);
                    document.querySelector('#cardNumber').dispatchEvent(new KeyboardEvent('keyup', {'key':' '}));
                    $('#installments').empty().append(`<option>Informe um cartão para visualizar as parcelas</option>`);
                }, error: e => {
                    console.log(e);
                    Toast.fire({
                        icon: 'warning',
                        title: "Acorreu um problema, aguarde enquanto tentamos novamente"
                    });
                    $('input[name="frete_envio"]:checked').trigger('ifChecked');
                }
            });
        });

        $('#addCupom').click(function(){
            const element = $(this).closest('#insert-cupom');
            const cupom = element.find('input[name="cupom"]').val();
            let message_success = "";

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "../../queries/ajax/insertCupom",
                dataType: 'json',
                data: { cupom },
                success: result => {
                    if(result == true){
                        message_success = 'Cupom aplicado!';
                        if($('input[name="frete_envio"]:checked').length == 0)
                            message_success += ' Selecione um frete para visualizar o cupom aplicado!'

                        Toast.fire({
                            icon: 'success',
                            title: message_success
                        });
                        $('input[name="frete_envio"]:checked').trigger('ifChecked')
                        return false;
                    }

                    Toast.fire({
                        icon: 'error',
                        title: 'Cupom não aplicável para seus produtos ou inexistente!'
                    });
                    $('input[name="frete_envio"]:checked').trigger('ifChecked');
                }, error: () => {
                    Toast.fire({
                        icon: 'error',
                        title: "Acorreu um problema, caso o problema persistir contate o suporte"
                    })
                }
            });
        });

        const loadDataFrete = () => {
            $('button').attr('disabled', true);
            $('.value-frete-selected').html("<i class='fa fa-spin fa-spinner'></i>");
            $('.date-frete-selected').html("<i class='fa fa-spin fa-spinner'></i>");
            $('.total-order').html("<i class='fa fa-spin fa-spinner'></i>");
            $('.total-products').html("<i class='fa fa-spin fa-spinner'></i>");
            $('.value-cupom-total').html("<i class='fa fa-spin fa-spinner'></i>");
        }

        const loadDataParcels = () => {
            $('.payment .payment-card-selected .qty-parcel').html("<i class='fa fa-spin fa-spinner'></i>");
            $('.payment .payment-card-selected .value-parcel').html("<i class='fa fa-spin fa-spinner'></i>");
            $('.payment .payment-card-selected .total-parcel').html("<i class='fa fa-spin fa-spinner'></i>");
        }

        const zeraDataParcels = () => {
            $('.payment .payment-card-selected .qty-parcel').html("-");
            $('.payment .payment-card-selected .value-parcel').html("-");
            $('.payment .payment-card-selected .total-parcel').html("-");
        }

        const zeraDataFrete = (nao_zerar = null) => {
            $('button').attr('disabled', false);
            if(nao_zerar != 'v_frete') $('.value-frete-selected').html("-");
            if(nao_zerar != 'd_frete') $('.date-frete-selected').html("-");
            if(nao_zerar != 'total_ord') $('.total-order').html("-");
            if(nao_zerar != 'total_prd') $('.total-products').html("-");
            if(nao_zerar != 'v_cupom') $('.value-cupom-total').html("-");
        }

        const loadDataAddress = () => {
            if($('.table-address tr').length > 0) {
                $('.table-address tr').each(function () {
                    $('td:eq(1)', this).html("<i class='fa fa-spin fa-spinner'></i>");
                })
            } else {
                $('.data-address').html("<i class='fa fa-spin fa-spinner'></i>");
            }
        }

        const zeraDataAddress = () => {
            $('.table-address tr').each(function () {
                $('td:eq(1)', this).html("-");
            })
        }

        const reloadAddressSelect = (address_selected = false) => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "../../queries/ajax/getAddress",
                dataType: 'json',
                success: result => {
                    $('[name="address"]').empty();

                    for(let i = 0; i < result.length; i++) {
                        if(address_selected === false)
                            selected = result[i].default == 1 ? 'selected' : '';
                        else
                            selected = result[i].id == address_selected ? 'selected' : '';

                        $('[name="address"]').append(`
                            <option value="${result[i].id}" ${selected}>${result[i].address}, ${result[i].number}</option>
                        `);
                    }
                    $('select[name="address"]').trigger('change');

                }, error: () => {
                    Toast.fire({
                        icon: 'error',
                        title: "Acorreu um problema, caso o problema persistir contate o suporte"
                    })
                }
            });
        }

        $('.payment-card, .payment-billet').on('click', function () {
            $('.payment-card, .payment-billet').removeClass('active');
            $(this).addClass('active');
            if($(this).hasClass('payment-card')){
                $('.payment-billet-selected').slideUp('slow');
                $('.payment-card-selected').slideDown('slow');
                $('.btn-payment button').text('REALIZAR PAGAMENTO');
                $('#methodPayment').val('card');
                $('#billetCpf').val('');
            }
            if($(this).hasClass('payment-billet')){
                $('.payment-billet-selected').slideDown('slow');
                $('.payment-card-selected').slideUp('slow');
                $('.btn-payment button').text('GERAR BOLETO');
                $('#methodPayment').val('billet');
                $('#cardNumber').val('');
                $('#cardholderName').val('');
                $('#securityCode').val('');
                $('select[name="installments"]').val('');
                $('.payment .view-parcels').hide('slow')
            }
            $('.btn-payment').slideDown('slow');
        });
    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('user/css/checkout/style.css')}}">
    <link rel="stylesheet" href="{{ asset('vendor/icheck/skins/all.css')}}">
@endsection

@section('body')

    <section class="hero-wrap hero-wrap-2" style="background-image: url({{ $settings['banner'] }});" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text align-items-end justify-content-center">
                <div class="col-md-9 ftco-animate mb-5 text-center">
                    <p class="breadcrumbs mb-0">
                        <span class="mr-2"><a href="{{ route('user.home') }}">Início <i class="fa fa-chevron-right"></i></a></span>
                        <span class="mr-2"><a href="{{ route('user.cart') }}">Carrinho <i class="fa fa-chevron-right"></i></a></span>
                        <span>Finalizar Pedido<i class="fa fa-chevron-right"></i></span></p>
                    <h2 class="mb-0 bread">Finalizar Pedido</h2>
                </div>
            </div>
        </div>
    </section>


    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="checkout col-md-12">
                    @if(isset($errors) && count($errors) > 0)
                        <div class="alert alert-danger col-md-12">
                            <h6>Não foi possível finalizar sua compra!</h6>
                            <ol>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ol>
                        </div>
                    @endif
                    <div class="checkout-header">
                        <h2 class="checkout-header">Datalhes Do Pedido</h2>
                    </div>
                    <form action="{{route('user.order.checkout.send')}}" enctype="multipart/form-data" method="POST" name="checkout-send" id="checkout-send">
                        <div class="checkout-info">
                            <div class="info-value">
                                <h2>Resumo do pedido</h2>
                                <div>
                                    <span>@if($products['qty_total_cart'] > 1){{$products['qty_total_cart']}} Produtos @else {{$products['qty_total_cart']}} Produto @endif <a href="#" style="padding-left: 10px" data-toggle="modal" data-target="#view-products"><u>visualizar</u></a></span>
                                    <span class="total-products">R$ {{$products['value_total_cart']}}</span>
                                </div>
                                <div>
                                    <span>Frete</span>
                                    <span><span class="value-frete-selected">-</span></span>
                                </div>
                                <div>
                                    <span>Data Entrega</span>
                                    <span><span class="date-frete-selected">-</span></span>
                                </div>
                                <div class="data-cupom d-none">
                                    <span>Cupom (<span class="name-cupom-total"></span>)</span>
                                    <span><span class="value-cupom-total">-</span></span>
                                </div>
                                <div>
                                    <span>Total</span>
                                    <span><span class="total-order">-</span></span>
                                </div>
                            </div>
                            <div class="info-frete">
                                <h2>Endereços</h2>
                                <div class="checkout-address generalForm">
                                    <div class="header-address">
                                        <label>Endereço de envio</label>
                                        <button type="button" class="btn col-md-6 btn-success mb-1" data-toggle="modal" data-target="#insert-form-address">Cadastrar endereço</button>
                                    </div>
                                    <select class="form-control wide" name="address">
                                        @foreach($dataAddress as $address)
                                            <option value="{{$address['id']}}" @if($address['default'] == 1) selected @endif>{{$address['address']}}, {{$address['number']}}</option>
                                        @endforeach
                                    </select>
                                    <div class="data-address">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <hr>
                        </div>
                        <div class="checkout-frete mt-3">
                            <div class="row">
                                <h2>Escolha a forma de envio</h2>
                            </div>
                            <div class="row d-flex justify-content-around mt-3 fretes-select">
                                <h4 class="sem-frete">Cadastre um endereço de envio para continuar</h4>
                            </div>
                        </div>
                        <div class="row">
                            <hr>
                        </div>
                        <div class="row">
                            <div class="checkout-frete mt-3 d-flex flex-wrap w-100">
                                <h2>Insira seu cupom, caso tenha</h2>

                                <div class="accordion mt-3 w-100" id="accordionExample">
                                    <div class="card" style="border-bottom: 1px solid #ddd;">
                                        <div class="card-header d-flex justify-content-between">
                                            <h5 class="mb-0">
                                                <button type="button" data-toggle="collapse" data-target="#insert-cupom" aria-expanded="true" style="border: 0px;background: transparent;cursor: pointer;outline: 0px !important;">
                                                    Insira seu cupom, caso tenha
                                                </button>
                                            </h5>
                                        </div>

                                        <div id="insert-cupom" class="collapse" data-parent="#accordionExample">
                                            <div class="col-md-12 d-flex justify-content-start generalForm">
                                                <div class="form-group col-md-5">
                                                    <label>Código do cupom</label>
                                                    <input type="text" class="form-control" name="cupom" autocomplete="off" value="@if(isset($_SESSION['cupom'])){{$_SESSION['cupom']}}@endif">
                                                </div>
                                                <div class="form-group col-md-3 mt-4">
                                                    <button type="button" class="btn btn-primary col-md-12" id="addCupom">Aplicar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <hr>
                        </div>
                        <div class="payment mt-3">
                            <div class="row">
                                <h2>Formas de pagamento</h2>
                            </div>
                            <div class="row d-flex justify-content-center payment-method mt-5">
                                <div class="payment-card">
                                    <img src="{{ asset('user/img/icon/cartao.png') }}" width="90px" style="margin-top: 6px">
                                    <img src="{{ asset('user/img/icon/cartao-orange.png') }}" class="payment-hover-img" width="90px" style="margin-top: 6px">
                                </div>
                                <div class="payment-billet">
                                    <img src="{{ asset('user/img/icon/boleto.png') }}" width="100px">
                                    <img src="{{ asset('user/img/icon/boleto-orange.png') }}" class="payment-hover-img" width="100px">
                                </div>
                            </div>
                            <div class="payment-card-selected mt-5 generalForm">
                                <div class="row">
                                    <div class="form-group col-md-6 offset-md-3 text-center payment-title">
                                        <h3>Pagamento via Cartão de Crédito</h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 offset-md-3">
                                        <label class="d-flex">Número do cartão <div id="cardBrandImg" style="padding-left: 10px;"></div></label>
                                        <input type="tel" autocomplete="off" class="form-control" id="cardNumber" maxlength="19" data-checkout="cardNumber" onselectstart="return false" onpaste="return false" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 offset-md-3">
                                        <label>Nome impresso no cartão</label>
                                        <input type="text" autocomplete="off" class="form-control" id="cardholderName" data-checkout="cardholderName">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-3 offset-md-3">
                                        <label for="docType">Tipo de documento</label>
                                        <select id="docType" autocomplete="off" class="form-control" data-checkout="docType" disabled></select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Número do Documento</label>
                                        <input type="text" autocomplete="off" class="form-control" id="docNumber" data-checkout="docNumber">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-2 offset-md-3">
                                        <label>Mês Validade (<i>12</i>)</label>
                                        <input type="tel" autocomplete="off" class="form-control" id="cardExpirationMonth" maxlength="7" data-checkout="cardExpirationMonth" onselectstart="return false" onpaste="return false" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off maxlength="2" >
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Ano Validade (<i>2030</i>)</label>
                                        <input type="tel" autocomplete="off" class="form-control" id="cardExpirationYear" maxlength="7" data-checkout="cardExpirationYear" onselectstart="return false" onpaste="return false" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off maxlength="4" >
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Código de Segurança</label>
                                        <input type="password" autocomplete="off" class="form-control" id="securityCode" maxlength="3" data-checkout="securityCode" onselectstart="return false" onpaste="return false" onCopy="return false" onCut="return false" onDrag="return false" onDrop="return false" autocomplete=off>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 offset-md-3">
                                        <label>Parcelas</label>
                                        <select class="form-control" name="installments" id="installments">
                                            <option value="1">Selecione um Frete</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="payment-billet-selected mt-5 generalForm">
                                <div class="row">
                                    <div class="form-group col-md-6 offset-md-3 text-center payment-title">
                                        <h3>Pagamento via Boleto Bancário</h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 offset-md-3">
                                        <label>CPF</label>
                                        <input type="tel" class="form-control" name="billetCpf" id="billetCpf">
                                    </div>
                                </div>
                            </div>
                            <div class="row btn-payment">
                                <div class="form-group text-center d-flex justify-content-center col-md-12 mt-3">
                                    <button type="input" class="col-md-4 btn btn-primary" id="paymentCard">Realizar Pagamento</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 offset-md-3 mt-3 mb-3 text-center">
                                <label for="terms">
                                    <input type="checkbox" name="terms" id="terms" required> Estou ciente que meus dados estão protegidos e que a {{ $settings['name_store'] }} não reterá nenhum dado digitado!
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 offset-md-3 mt-3 mb-3">
                                <img src="https://imgmp.mlstatic.com/org-img/MLB/MP/BANNERS/tipo2_575X40.jpg?v=1"
                                     alt="Mercado Pago - Meios de pagamento" title="Mercado Pago - Meios de pagamento"
                                     style="width: 100%; height: 35px"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 offset-md-3 mt-3 mb-3 text-center">
                                <a href="https://www.mercadopago.com.br/ajuda/Custos-de-parcelamento_322" target="_blank">Veja os juros de parcelamentos!</a>
                            </div>
                        </div>

                        <input type="hidden" name="creditCardToken" id="creditCardToken" value="">
                        <input type="hidden" name="senderHash" id="senderHash" value="">
                        <input type="hidden" name="methodPayment" id="methodPayment" value="">
                        <input type="hidden" name="totalValueOrder" id="totalValueOrder" value="">
                        <input type="hidden" name="valueParcel" id="valueParcel" value="">
                        <input type="hidden" name="paymentMethodId" id="paymentMethodId"/>
                        <input type="hidden" name="payment_method_id" id="payment_method_id"/>
                        <input type="hidden" name="issuer_id" id="issuer_id"/>
                        {!! csrf_field() !!}
                    </form>


                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="update-form-address" tabindex="-1" role="dialog" aria-labelledby="update-form-address" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="#" id="update-form-address-post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="update-form-address">Alterar Endereço</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body generalForm">
                        <div class="row">
                            <div class="col-xl-4 col-md-4 form-group">
{{--                                <label>CEP</label>--}}
                                <input name="cep_update" placeholder="CEP" class="form-control">
                            </div>
                            <div class="col-xl-8 col-md-8 form-group">
{{--                                <label>Endereço</label>--}}
                                <input name="address_update" placeholder="Endereço" class="form-control">
                            </div>
                            <div class="col-xl-4 col-md-4 form-group">
{{--                                <label>Número</label>--}}
                                <input name="number_update" placeholder="Número" class="form-control">
                            </div>
                            <div class="col-xl-8 col-md-8 form-group">
{{--                                <label>Complemento</label>--}}
                                <input name="complement_update" placeholder="Complemento" class="form-control">
                            </div>
                            <div class="col-xl-6 col-md-6 form-group">
{{--                                <label>Referência</label>--}}
                                <input name="reference_update" placeholder="Referência" class="form-control">
                            </div>
                            <div class="col-xl-6 col-md-6 form-group">
{{--                                <label>Bairro</label>--}}
                                <input name="neighborhood_update" placeholder="Bairro" class="form-control">
                            </div>
                            <div class="col-xl-6 col-md-6 form-group">
{{--                                <label>Cidade</label>--}}
                                <input name="city_update" placeholder="Cidade" class="form-control">
                            </div>
                            <div class="col-xl-6 col-md-6 form-group">
{{--                                <label>Estado</label>--}}
                                <input name="state_update" placeholder="Estado" class="form-control">
                            </div>
                            <input name="address_id" type="hidden">
                            {!! csrf_field() !!}
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-primary py-2 col-md-3" data-dismiss="modal">Fechar</button>
                        <button type="button" id="update-address" class="btn btn-success py-2 col-md-3">Alterar Endereço</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <div class="modal fade" id="insert-form-address" tabindex="-1" role="dialog" aria-labelledby="insert-form-address" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="#" id="insert-form-address-post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="insert-form-address">Cadastrar Endereço</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body generalForm">
                        <div class="row">
                            <div class="col-xl-4 col-md-4 form-group">
{{--                                <label>CEP</label>--}}
                                <input name="cep" placeholder="CEP" class="form-control">
                            </div>
                            <div class="col-xl-8 col-md-8 form-group">
{{--                                <label>Endereço</label>--}}
                                <input name="address" placeholder="Endereço" class="form-control">
                            </div>
                            <div class="col-xl-4 col-md-4 form-group">
{{--                                <label>Número</label>--}}
                                <input name="number" placeholder="Número" class="form-control">
                            </div>
                            <div class="col-xl-8 col-md-8 form-group">
{{--                                <label>Complemento</label>--}}
                                <input name="complement" placeholder="Complemento" class="form-control">
                            </div>
                            <div class="col-xl-6 col-md-6 form-group">
{{--                                <label>Referência</label>--}}
                                <input name="reference" placeholder="Referência" class="form-control">
                            </div>
                            <div class="col-xl-6 col-md-6 form-group">
{{--                                <label>Bairro</label>--}}
                                <input name="neighborhood" placeholder="Bairro" class="form-control">
                            </div>
                            <div class="col-xl-6 col-md-6 form-group">
{{--                                <label>Cidade</label>--}}
                                <input name="city" placeholder="Cidade" class="form-control">
                            </div>
                            <div class="col-xl-6 col-md-6 form-group">
{{--                                <label>Estado</label>--}}
                                <input name="state" placeholder="Estado" class="form-control">
                            </div>
                            {!! csrf_field() !!}
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-primary py-2 col-md-3" data-dismiss="modal">Fechar</button>
                        <button type="button" id="add-address" class="btn btn-success py-2 col-md-3">Cadastrar Endereço</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="view-products" tabindex="-1" role="dialog" aria-labelledby="view-products" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insert-form-address">Productos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table-responsive table">
                                <thead class="thead-primary">
                                <tr>
                                    <th width="10%" class="text-center"><i class="fa fa-picture-o"></i></th>
                                    <th width="65%">Produto</th>
                                    <th width="10%" class="text-center">Quantidade</th>
                                    <th width="15%" class="text-right">Valor</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($products['arr_tems'] as $product)
                                        <tr>
                                            <td class="text-center"><img width="60%" src="{{ asset("user/img/products/{$product['path_image']}") }}"></td>
                                            <td>{{$product['name']}}</td>
                                            <td class="text-center">{{$product['qty']}}</td>
                                            <td class="text-right">R$ {{$product['value']}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-primary py-2 col-md-3" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

@endsection
