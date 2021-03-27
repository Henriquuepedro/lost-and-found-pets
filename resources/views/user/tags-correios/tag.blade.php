<html>
    <head>
        <meta charset="utf-8">
        <title>Etiquetas</title>
        <style>
            @page { margin: 1cm; }
        </style>
    </head>
    <body>
        <table cellpadding="0" cellspacing="0" style="width: 100%">
        @foreach($dataTags as $key => $data)
            {!! $key % 2 == 0 ? '<tr>' : '' !!}
            <td style="width: 50%;padding-bottom: 35px;">
                <table cellpadding="0" cellspacing="0" style="width: {{ count($dataTags) == 1 ? '40%' : '85%'}}">
                    <tr>
                        <td>
                            <table cellpadding="1" cellspacing="1" style="width: 100%">
                                <tr>
                                    <td style="width: 7%; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif; border-top: 3px solid #000 !important; border-left: 3px solid #000 !important">&nbsp;</td>
                                    <td style="width: 86%; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif">&nbsp;</td>
                                    <td style="width: 7%; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif; border-top: 3px solid #000; border-right: 3px solid #000">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif;font-size: 12px;text-align: center;padding-top: 40px">USO EXCLUSIVO DOS CORREIOS</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif;font-size: 10px;text-align: center;padding-top:5px;padding-bottom: 60px">Cole aqui a etiqueta com o código identificador da encomenda</td>
                                </tr>
                                <tr>
                                    <td style="width: 7%; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif; border-bottom: 3px solid #000; border-left: 3px solid #000">&nbsp;</td>
                                    <td style="width: 86%; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif">&nbsp;</td>
                                    <td style="width: 7%; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif; border-bottom: 3px solid #000; border-right: 3px solid #000">&nbsp;</td>
                                </tr>
                            </table>
                            <table cellpadding="0" cellspacing="0" style="width: 100%">
                                <tr>
                                    <td colspan="2" style="font-size: 10px;padding-bottom: 6px; padding-top: 5px; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif">Recebedor:______________________________________________</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10px;padding-bottom: 5px; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif">Assinatura:___________________</td>
                                    <td style="font-size: 10px;padding-bottom: 5px; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif">Documento:_________________</td>
                                </tr>
                            </table>
                            <table cellpadding="0" cellspacing="0.01" style="width: 100%;border: 1px solid #000 !important;">
                                <tr>
                                    <td style="font-size: 12px !important;padding: 2px 15px !important;background-color: #000; color: #fff;width: 40%;font-weight: bold; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif">DESTINATÁRIO</td>
                                    <td style="width: 60%">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-size: 10px;padding-top: 5px; padding-left: 5px; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif">{{ $data['client']->name_sender }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-size: 10px;padding-top: 3px; padding-left: 5px; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif">{{ $data['clientAddress']->address }}, {{$data['clientAddress']->number}}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-size: 10px;padding-top: 3px; padding-left: 5px; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif">{{ $data['clientAddress']->complement }} {{ $data['clientAddress']->neighborhood }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="font-size: 10px;padding-top: 20px; padding-left: 5px; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif"><strong style="font-size: 12px">{{ $data['clientAddress']->cep }}</strong> <span style="padding-left: 15px">{{ $data['clientAddress']->city }}-{{ $data['clientAddress']->state }}</span></td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 0px; padding-bottom: 2px; padding-left: 15px; width: 70%">{!! $data['code'] !!}</td>
                                    <td style="text-align: right;padding-right: 10px;"></td>
                                </tr>
                            </table>
                            <table border="0" cellpadding="0" cellspacing="0" style="width: 95%">
                                <tr>
                                    <td style="font-size: 10px;padding-top: 3px; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif"><strong>Remetente:</strong> {{ $data['admin']->name_user }}</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10px;padding-top: 3px; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif">{{ $data['admin']->name }}</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10px;padding-top: 3px; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif">{{ $data['admin']->address }}, {{ $data['admin']->number }}</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10px;padding-top: 3px; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif">{{ $data['admin']->complement }} {{ $data['admin']->neighborhood }}</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 10px;padding-top: 3px; font-family: 'Trebuchet MS', 'arial', 'helvetica', 'Open Sans', sans-serif"><strong>{{ $data['admin']->cep }}</strong> {{ $data['admin']->city }}-{{ $data['admin']->state }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            {!! $key % 2 != 0 ? '</tr>' : '' !!}
        @endforeach
        </table>
    </body>
</html>
