<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Mail\SendMailController;

class RegisterController extends Controller
{
    private $user;
    private $mail;

    public function __construct(User $user, SendMailController $mail)
    {
        $this->user = $user;
        $this->mail = $mail;
    }

    public function register()
    {
        return view('user.login.register');
    }

    public function registerPost(Request $request)
    {
        $validator = validator(
            $request->all(),
            [
                'name'                  => 'required|min:3',
                'email'                 => 'required|unique:users,email',
                'password'              => 'required|confirmed|min:6'
            ],
            [
                'name.required'     => 'O nome é um campo obrigatório!',
                'name.min'          => 'O nome precisa de no mínimo 3 caracteres!',
                'email.required'    => 'O email é um campo obrigatório!',
                'email.unique'      => 'O email já está em uso!',
                'password.required' => 'A senha é um campo obrigatório',
                'password.confirmed'=> 'As senhas não correspondem!',
                'password.min'      => 'A senha precisa de no mínimo 6 caracteres!'
            ]
        );

        if($validator->fails())
            return redirect()->route('user.login')->withErrors($validator)->withInput();

        $dataUserCreate = [
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password)
        ];

        $create = $this->user->create($dataUserCreate);
        if(!$create)
            return redirect()->route('user.login')->withErrors(['Não foi possível se registrar!'])->withInput();

        $dataUserAuth = [
            'email'     => $request->email,
            'password'  => $request->password
        ];

        if ( auth()->guard('client')->attempt($dataUserAuth) && $create ) {

            if(env('APP_ENV') == "production" || env('APP_ENV') == "test")
                $this->mail->newUser();

            return redirect()->route('user.account');
        }

        return redirect()->route('user.login')->withErrors(['Não foi possível autenciar após a criação!'])->withInput();
    }
}
