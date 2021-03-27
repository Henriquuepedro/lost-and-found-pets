<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px">
    <tbody>
        <tr>
            <td>
                <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px;margin-bottom: 20px;">
                    <tbody>
                    <tr>
                        @if($viewLogoHeader)
                        <td style="width:17px;" valign="top">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:17px;">
                                <tbody>
                                <tr>
                                    <td style="width:17px; height:33px; background-color:rgb(0,0,0);">&nbsp;</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                        <td style="width:183px;padding-bottom:20px" valign="top"><img width="50" height="50" alt="" border="" src="{{ $logo }}" style="display:block;" /></td>
                        @endif
                        <td style="width:100%;" valign="top">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                                <tbody>
                                <tr>
                                    <td style="width:400px; height:33px; background-color:rgb(0, 0, 0);">
                                        <table align="right" border="0" cellpadding="0" cellspacing="0" style="width:199px;">
                                            <tbody>
                                            <tr>
                                                <td align="left" style="width:70px;font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif;font-size:14px;color:#ffffff;text-align:left;line-height:16px;font-weight:bold">Siga-nos:</td>
                                                <td align="center" style="width:33px;"><a href="https://www.instagram.com/" target="_blank"><img width="21" alt="" border="0" src="{{ asset("/user/img/gallery/instagram1.png") }}" style="display:block;" /></a></td>
                                                <td align="center" style="width:33px;"><a href="https://www.youtube.com/" target="_blank"><img width="22" alt="" border="0" src="{{ asset("/user/img/gallery/youtube.png") }}" style="display:block;" /></a></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        @yield('body')
        <tr>
            <td style="padding-top:15px;padding-bottom:10px; background-color: rgb(0, 0, 0); border-top: 5px solid #fff">
                <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:176px;">
                    <tbody>
                    <tr>
                        <td align="center" style="width:33px;"><a href="https://www.instagram.com/" target="_blank"><img width="21" alt="" border="0" src="{{ asset("/user/img/gallery/instagram1.png") }}" style="display:block;" /></a></td>
                        <td align="center" style="width:33px;"><a href="https://www.youtube.com/" target="_blank"><img width="22" alt="" border="0" src="{{ asset("/user/img/gallery/youtube.png") }}" style="display:block;" /></a></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border-bottom:10px solid rgb(45, 45, 45);background-color: rgb(0, 0, 0)">
                <table align="center" border="0" cellpadding="0" cellspacing="0" style="height:35px;width:550px;">
                    <tbody>
                    <tr>
                        <td style="width:100%; height:35px; font-family: 'Microsoft YaHei','Source Sans Pro', sans-serif; font-size:12px; color:rgb(32, 36, 39); text-align:center; line-height:22px; font-weight:bold;text-transform: uppercase"><a target="_blank" href="{{ URL::to('/') }}" style="color:#fff;text-decoration:none;">{{ str_replace("https://", "www.", str_replace("https://www", "www.", URL::to('/'))) }}</a></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
