@extends('mail.template.index')

@section('body')
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:rgb(0, 0, 0);text-align:center;line-height:18px;font-weight:bold; text-transform: uppercase; padding-top:20px">Pedido Realizado com Sucesso!</td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:18px;color:#202427;text-align:center;line-height:34px;font-weight:bold;padding-bottom:15px;padding-top:15px">
            Olá, {{ $userName }}
        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-bottom: 10px;">
            Nós acabamos de receber o seu pedido #{{ str_pad($order, 5, "0", STR_PAD_LEFT) }} <img style="width:20px; padding-left: 10px" src="{{ asset("user/img/gallery/slime.png") }}">
        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-bottom: 10px;">
            Não se preocupe que agora iremos dar a maior atenção para realizar o mais rápido possível o envio do pedido.
        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-bottom: 25px;">
            Fique atento em <strong style="color: #b7472a"><u>Minha Conta</u></strong> dentro da loja, por lá é possível acompanhar as atualizações e mais detalhes referente ao seu pedido!
        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-bottom: 20px;">
            Prazo de entrega: A contagem dos dias se inicia após a confirmação de pagamento.
        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-bottom: 20px;">
            <strong style="color: #b7472a">IMPORTANTE:</strong> Os bancos demoram até três dias úteis para nos informar que um pagamento por boleto foi realizado.

        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-bottom: 20px;">
            É a partir desta confirmação, e não da data que o pagamento foi efetuado, que o prazo de entrega começa a contar.
        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-bottom: 20px;">
            Caso a data prevista para a entrega corresponda a algum feriado na região de entrega, pedimos gentilmente que acrescente 1 dia útil* ao prazo.

        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-bottom: 20px;">
            * Consideramos como dias úteis de 2ª a 6ª exceto feriados.
        </td>
    </tr>
        <td style="padding-top:30px;padding-bottom:30px">
            <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%">
                <tbody>
                <tr>
                    <td colspan="4" style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:22px;text-align:center;width:100%;padding-bottom: 20px">Produtos</td>
                </tr>
                @foreach( $arrItems as $iten )
                    <tr>
                        <td style="width:10%; padding-left: .5%;border: 1px solid #000"><img width="50px" alt="" border="0" src="{{ $iten['image'] }}" style="display:block;" /></td>
                        <td style="font-size:14px;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;width:60%;padding: 0px 10px;border: 1px solid #000;border-right: 0px;border-left: 0px;padding: 0px 10px">{{ $iten['description'] }}</td>
                        <td style="font-size:14px;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;width:10%;border: 1px solid #000;border-right: 0px;padding: 0px 10px">{{ $iten['quantity'] }}x</td>
                        <td style="font-size:14px;text-align:center;color:red;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;width:20%;border: 1px solid #000;padding: 0px 10px">R$ {{ $iten['total_iten'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding-top:30px;padding-bottom:15px">
            <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%">
                <tbody>
                <tr>
                    <td colspan="4" style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:22px;text-align:center;width:100%;padding-bottom: 20px">Endereço de Entrega</td>
                </tr>
                <tr>
                    <td style="font-size:14px;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif">{{ $arrAddress['address'] }}, {{ $arrAddress['number'] }}</td>
                </tr>
                <tr>
                    <td style="font-size:14px;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif">{{ $arrAddress['complement'] }} - {{ $arrAddress['reference'] }}</td>
                </tr>
                <tr>
                    <td style="font-size:14px;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif">CEP: {{ $arrAddress['cep'] }}</td>
                </tr>
                <tr>
                    <td style="font-size:14px;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif">{{ $arrAddress['neighborhood'] }} - {{ $arrAddress['city'] }}/{{ $arrAddress['state'] }}</td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-top: 40px; text-align: center;padding-bottom:30px">
            <a style="padding: 15px 70px; background-color: #767676; color: #fff; text-decoration: none; border: 1px solid #000" href="{{$urlOrder}}" target="_blank">Ver Pedido</a>
        </td>
    </tr>
@endsection
