<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Mail\SendMailController;

class ContactController extends Controller
{
    private $mail;

    public function __construct(SendMailController $mail)
    {
        $this->mail = $mail;
    }

    public function contact(Request $request)
    {
        $validator = validator(
            $request->all(),
            [
                'name'      => 'required|min:3',
                'email'     => 'required|email',
                'message'   => 'required|min:3',
                'subject'   => 'required|min:3'
            ],
            [
                'name.required'     => 'O nome é um campo obrigatório!',
                'name.min'          => 'O nome precisa de no mínimo 3 caracteres!',
                'email.required'    => 'O email é um campo obrigatório!',
                'email.email'       => 'O email está em um formato inválido!',
                'message.required'  => 'A mensagem é um campo obrigatório!',
                'message.min'       => 'A mensagem precisa de no mínimo 3 caracteres!',
                'subject.required'  => 'O assunto é um campo obrigatório!',
                'subject.min'       => 'O assunto precisa de no mínimo 3 caracteres!',
            ]
        );

        if($validator->fails())
            return redirect()->route('user.mail.contact')->withErrors($validator)->withInput();

        $send = $this->mail->contact($request);

        if($send[0])
            return redirect()->route('user.contact')
                ->with('success', 'Mensagem enviada com sucesso!');

        return redirect()->route('user.contact')
            ->with('warning', $send[1]);
    }
}
