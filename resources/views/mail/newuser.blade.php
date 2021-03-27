@extends('mail.template.index')

@section('body')
<tr>
    <td>
        <table align="center" border="0" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td style="">
                    <table border="0" cellpadding="0" cellspacing="0" style="padding-bottom: 20px;">
                        <tbody>
                        <tr>
                            <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:rgb(0, 0, 0);text-align:center;line-height:18px;font-weight:bold; text-transform: uppercase">BEM VINDO(A), {{$user->name}} <img style="width:20px; padding-left: 10px" src="{{ asset("user/img/gallery/slime.png") }}"></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr>
    <td>
        <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px;">
            <tbody>
            <tr style="background: rgba(0, 0, 0, 0.6);height: 190px;">
                <td style="width:300px;padding-left: 40px">
                    <table align="left" border="0" cellpadding="0" cellspacing="0" style="width:300px;">
                        <tbody>
                        <tr>
                            <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#fff;text-align:center;line-height:18px;font-weight:bold;text-transform: uppercase">LOJA {{ $company }}</td>
                        </tr>
                        <tr>
                            <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:12px;color:#fff;text-align:left;line-height:14px;padding-top:10px;padding-bottom:10px;padding-left: 20px">
                                Seu cadastro foi concluído e agora você pode aproveitar ao máximo a nossa plataforma com o seu acesso já liberado.
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table align="left" border="0" cellpadding="0" cellspacing="0" style="height: 25px;width: 214px;padding-left: 81px;">
                                    <tbody>
                                    <tr>
                                        <td style="background-color:rgb(0, 0, 0);font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:11px;color:#FFFFFF;text-align:center;line-height:12px;"><a href="{{ URL::to('/') }}" style="color:#ffffff;text-decoration:none;">CONHEÇA MAIS +</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width:150px;padding: 5px 0px"><img width="150" height="150" alt="" border="0" src="{{ $logo }}" style="display:block;" /></td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr>
    <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:30px;color:#202427;text-align:center;line-height:34px;font-weight:bold;padding-top:30px">Fique à vontade</td>
</tr>
<tr>
    <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:14px;color:#000000;text-align:center;line-height:16px;padding-top:3px;padding-bottom:30px;">Conheça um pouco mais</td>
</tr>
<tr>
    <td style="padding-bottom:40px">
        <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:535px;">
            <tbody>
            <tr>
                <td style="width:169px;"><img width="160px" alt="" border="0" src="{{ asset("user/img/gallery/security.jpg") }}" style="display:block;" /></td>
                <td style="width:14px;">&nbsp;</td>
                <td style="width:169px;"><img width="160px" alt="" border="0" src="{{ asset("user/img/gallery/time_email.jpg") }}" style="display:block;" /></td>
                <td style="width:14px;">&nbsp;</td>
                <td style="width:169px;"><img width="160px" alt="" border="0" src="{{ asset("user/img/gallery/control.jpg") }}" style="display:block;" /></td>
            </tr>
            <tr>
                <td style="width:169px;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:20px;color:#202427;text-align:center;line-height:22px;font-weight:bold;padding-top:10px;padding-bottom:10px">Compra Segura</td>
                <td style="width:14px;">&nbsp;</td>
                <td style="width:169px;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:20px;color:#202427;text-align:center;line-height:22px;font-weight:bold;padding-top:10px;padding-bottom:10px">Facilidade</td>
                <td style="width:14px;">&nbsp;</td>
                <td style="width:169px;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:20px;color:#202427;text-align:center;line-height:22px;font-weight:bold;padding-top:10px;padding-bottom:10px">Controle</td>
            </tr>
            <tr>
                <td style="width:169px;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:14px;color:#000000;text-align:center;line-height:16px;padding-bottom:20px;">Para deixar sua compra ainda mais segura, trabalhamos com certificação SSL e criptografia total dentro do site.</td>
                <td style="width:14px;">&nbsp;</td>
                <td style="width:169px;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:14px;color:#000000;text-align:center;line-height:16px;padding-bottom:20px;">Faça sua compra com poucos cliques, não perca tempo saindo de casa para fazer sua compra.</td>
                <td style="width:14px;">&nbsp;</td>
                <td style="width:169px;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:14px;color:#000000;text-align:center;line-height:16px;padding-bottom:20px;">Controle e acompanhe seus pedidos dentro da sua conta, veja como está o andamento do pedido e o histórico de compras.</td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
@endsection
