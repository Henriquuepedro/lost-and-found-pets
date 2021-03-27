@extends('adminlte::page')

@section('title', 'Informações do Pedidoo')

@section('content_header')
    <h1 class="m-0 text-dark">Informações do Pedidoo</h1>
@stop

@section('css')
    <style>
        .timeline-status {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* The actual timeline-status (the vertical ruler) */
        .timeline-status::after {
            content: '';
            position: absolute;
            width: 4px;
            background-color: #007bff;
            top: 15px;
            bottom: 0;
            left: 14.8%;
            margin-left: -3px;
        }

        /* Container around content */
        .timeline-status-right {
            padding: 10px 40px;
            position: relative;
            background-color: inherit;
            width: 50%;
            left: 15%;
        }

        /* The circles on the timeline-status */
        .timeline-status-right::after {
            content: '';
            position: absolute;
            width: 25px;
            height: 25px;
            right: -17px;
            background-color: white;
            border: 3px solid #007bff;
            top: 15px;
            border-radius: 50%;
            z-index: 1;
        }

        /* Add arrows to the right container (pointing left) */
        .timeline-status-right::before {
            content: " ";
            height: 0;
            position: absolute;
            top: 20px;
            width: 0;
            z-index: 1;
            left: 30px;
            border: medium solid white;
            border-width: 10px 10px 10px 0;
            border-color: transparent #6bb2ff  transparent transparent;
        }

        /* Fix the circle for containers on the timeline-status-right side */
        .timeline-status-right::after {
            left: -16px;
        }

        /* The actual content */
        .timeline-content {
            padding: 10px 20px;
            background-color: #007bff94;
            position: relative;
            border-radius: 6px;
        }

        .timeline-content h3{
            color: #fff;
            font-size: 23px;
            font-weight: 600;
        }
        .timeline-content h5{
            color: #fff;
            font-size: 18px;
        }

        /* Media queries - Responsive timeline-status on screens less than 600px wide */
        @media (max-width: 992px) {
            /* Place the timelime to the left */
            .timeline-status::after {
                left: 31px;
            }

            /* Full-width containers */
            .timeline-status-right {
                width: 100%;
                padding-left: 70px;
                padding-right: 25px;
                left: 0%;
            }

            /* Make sure that all arrows are pointing leftwards */
            .timeline-status::before {
                left: 60px;
                border: medium solid white;
                border-width: 10px 10px 10px 0;
                border-color: transparent white transparent transparent;
            }
            .timeline-status-right::before{
                left: 61px;
            }
            /* Fix the circle for containers on the timeline-status-right side */
            .timeline-status-right::after {
                left: 17px;
            }
        }
    </style>
@stop

@section('js')
    <script src="https://raw.githubusercontent.com/RobinHerbots/Inputmask/3.3.3/js/jquery.inputmask.js"></script>
    <script src="https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js"></script>
    <script>
        $(function () {
            $('input[name="value"]').mask('#.##0,00', {
                reverse: true
            });
            $('[name="stock"]').mask('#0', {
                reverse: true
            });
            $("[name='width']").inputmask("numeric", {
                min: 11,
                max: 104
            });
            $("[name='height']").inputmask("numeric", {
                min: 2,
                max: 104
            });
            $("[name='depth']").inputmask("numeric", {
                min: 16,
                max: 104
            });
            $("[name='weight']").inputmask("numeric", {
                min: 1
            });
        });
    </script>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Detalhes do Pedido</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-8">
                            <label class="d-flex justify-content-between">Cliente <a href="{{ route('admin.client.view', ['id' =>  $arrOrder[0]['user_id']]) }}" target="_blank">Visualizar Cliente</a></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                        <i class="fa fa-user"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" value="{{ $arrOrder[0]['name_sender'] }}" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="stock">Realizado Em</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                        <i class="fa fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" id="stock" name="stock" value="{{ date('d/m/Y H:i', strtotime($arrOrder[0]['date_order'])) }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-5">
                            <label>Código de Transição MercadoPago</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                        <i class="fas fa-code-branch"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" value="{{ $arrOrder[0]['id_transaction'] }}" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-5">
                            <label>Email</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                        <i class="fas fa-at"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" value="{{ $arrOrder[0]['email_sender'] }}" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Quantidade de Itens</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ (int)$arrOrder[0]['item_count'] }}" readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text font-weight-bold">
                                      UN
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Produtos</h3>
                </div>
                <div class="card-body">
                    @foreach( $arrOrder as $iten )
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="d-flex justify-content-between">Produto <a href="{{ route('admin.products.edit', ['id' => $iten['product_id']]) }}" target="_blank">Visualizar Produto</a></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                        <i class="fab fa-product-hunt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" value="{{ $iten['name'] }}" readonly>
                            </div>
                            <small>O nome do produto em <strong>Visualizar Produto</strong> pode ocorrer divergência em caso de alteração.</small>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Quantidade</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ (int)$iten['quantity'] }}" readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text font-weight-bold">
                                      UN
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Valor Un.</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                      R$
                                    </span>
                                </div>
                                <input type="text" class="form-control" value="{{ number_format($iten['amount'], 2, ',', '.') }}" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Valor total</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                      R$
                                    </span>
                                </div>
                                <input type="text" class="form-control" value="{{ number_format($iten['total_iten'], 2, ',', '.') }}" readonly>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Pagamento</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label>Forma de Pagamento</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                        <i class="fas fa-cash-register"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" value="{{ $arrOrder[0]['method_payment'] == "billet" ? "Boleto" : "Cartão" }}" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Produtos</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                      R$
                                    </span>
                                </div>
                                <input type="text" class="form-control" value="{{ number_format($arrOrder[0]['total_value_products'], 2, ',', '.') }}" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Desconto</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                      R$
                                    </span>
                                </div>
                                <input type="text" class="form-control" value="{{ number_format($arrOrder[0]['discount_amount'], 2, ',', '.') }}" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Frete</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                      {{$arrOrder[0]['type_ship']}}
                                    </span>
                                </div>
                                @if($arrOrder[0]['method_payment'] == "billet")
                                    <input type="text" class="form-control" value="{{ number_format(($arrOrder[0]['value_ship']), 2, ',', '.') }}" readonly>
                                @else
                                    <input type="text" class="form-control" value="{{ number_format($arrOrder[0]['value_ship'], 2, ',', '.') }}" readonly>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Valot Total</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                      R$
                                    </span>
                                </div>
                                <input type="text" class="form-control" value="{{ number_format($arrOrder[0]['net_amount'], 2, ',', '.') }}" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Taxa MercadoPago</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                      R$
                                    </span>
                                </div>
                                <input type="text" class="form-control" value="{{ number_format($arrOrder[0]['fee_amount'], 2, ',', '.') }}" readonly>
                            </div>
                        </div>
                    </div>
                    @if( $arrOrder[0]['method_payment'] == "card" )
                    <div class="row d-flex justify-content-center flex-wrap">

                        <div class="form-group col-md-2">
                            <label>Parcelas</label>
                            <input type="text" class="form-control" value="{{ (int)$arrOrder[0]['qty_parcels'] }}" readonly>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Total com taxas parcelas</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                      R$
                                    </span>
                                </div>
                                <input type="text" class="form-control" value="{{ number_format($arrOrder[0]['value_total_with_tax_card'], 2, ',', '.') }}" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Valor das Parcelas</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                      R$
                                    </span>
                                </div>
                                <input type="text" class="form-control" value="{{ number_format($arrOrder[0]['parcel_card'], 2, ',', '.') }}" readonly>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Endereço de Entrega</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Destinatário</label>
                            <input type="text" class="form-control" value="{{ $arrOrder[0]['name_sender'] }}" readonly>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Forma do envio</label>
                            <input type="text" class="form-control" value="{{ $arrOrder[0]['type_ship'] }}" readonly>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Previsão de Entrega</label>
                            <input type="text" class="form-control" value="{{ date('d/m/Y', strtotime($arrOrder[0]['delivery_date'])) }}" readonly>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Valor do frete</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">
                                      R$
                                    </span>
                                </div>
                                <input type="text" class="form-control" value="{{ number_format($arrOrder[0]['value_ship'], 2, ',', '.') }}" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Enviar Até</label>
                            <input type="text" class="form-control" value="{{ $arrOrder[0]['date_cross_docking'] }}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-9">
                            <label>Endereço</label>
                            <input type="text" class="form-control" value="{{ $arrOrder[0]['address'] }}" readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Número</label>
                            <input type="text" class="form-control" value="{{ $arrOrder[0]['number'] }}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label>CEP</label>
                            <input type="text" class="form-control" value="{{ $arrOrder[0]['cep'] }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Complemento</label>
                            <input type="text" class="form-control" value="{{ $arrOrder[0]['complement'] }}" readonly>
                        </div>
                        <div class="form-group col-md-5">
                            <label>Referência</label>
                            <input type="text" class="form-control" value="{{ $arrOrder[0]['reference'] }}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Bairro</label>
                            <input type="text" class="form-control" value="{{ $arrOrder[0]['neighborhood'] }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Cidade</label>
                            <input type="text" class="form-control" value="{{ $arrOrder[0]['city'] }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Estado</label>
                            <input type="text" class="form-control" value="{{ $arrOrder[0]['state'] }}" readonly>
                        </div>
                    </div>
                    @if(count($trackings) > 0 && $trackings['codes'])
                        @foreach($trackings['codes'] as $key => $tracking)
                            <div class="row d-flex justify-content-center flex-wrap">
                                <div class="form-group col-md-4">
                                    <label>Data da Postagem</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text font-weight-bold">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" value="{{ date('d/m/Y H:i', strtotime($trackings['dates'][$key])) }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="d-flex justify-content-between">Código de Rastreio <a href="https://linketrack.com/track?codigo={{ $tracking }}" target="_blank">Rastrear</a></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text font-weight-bold">
                                                <i class="fa fa-truck"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" value="{{ $tracking }}" readonly>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Timeline do Pedido</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="timeline-status">
                            @foreach( $arrStatus as $status )
                            <div class="timeline-status-right">
                                <div class="timeline-content">
                                    <h3>{{ $status['name'] }}</h3>
                                    <h5>{{ $status['date'] }}</h5>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-default">
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ route('admin.orders') }}" class="btn btn-danger col-md-3"><i class="fas fa-arrow-left"></i> Voltar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
