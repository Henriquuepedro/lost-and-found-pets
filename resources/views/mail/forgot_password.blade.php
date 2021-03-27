@extends('mail.template.index')

@section('body')
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:rgb(0, 0, 0);text-align:center;line-height:18px;font-weight:bold; text-transform: uppercase; padding-top:20px">Recuperar Senha</td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:18px;color:#202427;text-align:center;line-height:34px;font-weight:bold;padding-bottom:15px;padding-top:15px">
            Olá, {{ $name }}
        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-bottom: 10px; text-align: center">
            Recebemos uma solicitação para redefinir sua senha. Esse link tem expiração de 6 horas.
        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427; padding-top: 30px; text-align: center">
            <a style="padding: 15px 70px; background-color: #767676; color: #fff; text-decoration: none; border: 1px solid #000" href="{{$urlHash}}" target="_blank">Redefinir Senha</a>
        </td>
    </tr>
    <tr>
        <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:13px;color:#202427; padding-bottom: 30px; padding-top: 20px; text-align: center">
            <strong>Caso não tenha realizado uma solicitação, ignore esta mensagem.</strong>
        </td>
    </tr>
@endsection
