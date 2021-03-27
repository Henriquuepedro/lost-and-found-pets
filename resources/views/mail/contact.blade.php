@extends('mail.template.index')

@section('body')
<tr>
    <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:25px;color:#202427;text-align:center;line-height:34px;font-weight:bold;padding-top:30px">Contato {{ $company }}</td>
</tr>
<tr>
    <td style="font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:14px;color:#000000;text-align:center;line-height:16px;padding-top:3px;padding-bottom:30px;">Novo contato pelo formulário do site</td>
</tr>
<tr>
    <td style="padding-bottom:40px; padding-top: 25px">
        <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:535px;">
            <tbody>
            <tr>
                <td style="width:25%;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427;line-height:20px;font-weight:bold;padding-top:10px;padding-bottom:10px">Nome:</td>
                <td style="width:75%;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427;line-height:20px;padding-top:10px;padding-bottom:10px">{{ $contact->name }}</td>
            </tr>
            <tr>
                <td style="width:25%;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427;line-height:20px;font-weight:bold;padding-top:10px;padding-bottom:10px">E-mail:</td>
                <td style="width:75%;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427;line-height:20px;padding-top:10px;padding-bottom:10px">{{ $contact->email }}</td>
            </tr>
            <tr>
                <td style="width:25%;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427;line-height:20px;font-weight:bold;padding-top:10px;padding-bottom:10px">Assunto:</td>
                <td style="width:75%;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427;line-height:20px;padding-top:10px;padding-bottom:10px">{{ $contact->subject }}</td>
            </tr>
            <tr>
                <td style="width:25%;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427;line-height:20px;font-weight:bold;padding-top:10px;padding-bottom:10px">Mensagem:</td>
                <td style="width:75%;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:16px;color:#202427;line-height:20px;padding-top:10px;padding-bottom:10px">{{ $contact->message }}</td>
            </tr>
            </tbody>
        </table>
    </td>
</tr>
@endsection
