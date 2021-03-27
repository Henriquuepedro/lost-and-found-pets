@extends('mail.template.index')

@section('body')
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:18px;color:#202427;text-align:center;line-height:34px;font-weight:bold;padding-bottom:15px;padding-top:15px">
            Olá, {{ $userName }}
        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-bottom: 10px;">
            Ainda não recebemos seu pagamento via boleto, não deixe de pagar seu boleto, o não pagamento do boleto leva o cancelamento automático do pedido.
        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-bottom: 10px;">
            Caso já tenha pago, fique tranquilo, nosso banco em breve irá reconhecer o pagamento!
        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-bottom: 10px;">
            Fique atento em <strong style="color: #b7472a"><u>Minha Conta</u></strong> dentro da loja, por lá é possível acompanhar as atualizações e mais detalhes, referente ao seu pedido.
        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-top: 30px; text-align: center">
            <a style="padding: 15px 70px; background-color: #767676; color: #fff; text-decoration: none; border: 1px solid #000" href="{{$urlBillet}}" target="_blank">Visualizar Boleto</a>
        </td>
    </tr>
    <tr>
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
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-top: 50px; padding-bottom: 30px; text-align: center">
            <a style="padding: 15px 70px; background-color: #767676; color: #fff; text-decoration: none; border: 1px solid #000" href="{{$urlOrder}}" target="_blank">Acompanhar Pedido</a>
        </td>
    </tr>
@endsection
