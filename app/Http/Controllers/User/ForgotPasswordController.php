<?php


namespace App\Http\Controllers\User;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Mail\SendMailController;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController
{
    private $user;
    private $mail;
    private $key;

    public function __construct(User $user, SendMailController $mail)
    {
        $this->user = $user;
        $this->mail = $mail;
        $this->key  = env('APP_KEY');
    }

    public function forgotPassword()
    {
        return view('user.login.forgot_password');
    }

    public function forgotPasswordPost(Request $request)
    {
        // Não foi informado o campo e-mail
        if(!isset($request->email))
            return redirect()->back()
                ->with('warning', 'Não foi possível reconhecer sua solicitação, tente novamente!');

        $user = $this->user->getClientForEmail($request->email);

        // Não encontrou cliente
        if(!$user)
            return redirect()->back()
                ->with('warning', 'E-mail não corresponde a uma conta de usuário!');

        // Tudo certo, enviar link com um hash para o e-mail do cliente
        $payload = array(
            "email" => $request->email,
            'exp' => strtotime('+12 hours')
//            'exp' => strtotime('+2 minutes')
        );

        $jwt = JWT::encode($payload, $this->key, 'HS384');

        $sendMail = $this->mail->forgotPassword($request->email, $user->name, $jwt);

        // Não foi possível enviar o e-mail
        if(!$sendMail[0])
            return redirect()->back()
                ->with('warning', 'Não foi possível enviar um e-mail para o usuário informado!');

        return redirect()->back()
            ->with('success', 'Foi enviado por e-mail um link para redefinir a senha, redefina em até 6 horas!');
    }

    public function resetPassword($hash)
    {
        try {
            $decoded = JWT::decode($hash, $this->key, array('HS384'));
        } catch (\Exception $e) {
            return redirect()->route('user.login')
                ->with('warning', $this->translateMessageJwt($e->getMessage()));
        }

        $email = $decoded->email;

        if (!$email)
            return redirect()->route('user.login')
                ->with('warning', "Não foi encontrado um e-mail para redefinir, tente novamente!");

        return view('user.login.reset_password', compact('email', 'hash'));

    }

    public function resetPasswordPost(Request $request)
    {
        $validator = validator(
            $request->all(),
            [
                'email'                 => 'required',
                'password'              => 'required|confirmed|min:6',
                'hash'                  => 'required'
            ],
            [
                'email.required'    => 'O email é um campo obrigatório!',
                'password.required' => 'A senha é um campo obrigatório',
                'password.confirmed'=> 'As senhas não correspondem!',
                'password.min'      => 'A senha precisa de no mínimo 6 caracteres!',
                'hash.required'     => 'Não foi reconhecido a chave de redefinição!'
            ]
        );

        if($validator->fails())
            return redirect()->back()->withErrors($validator)->withInput();


        $user = $this->user->getClientForEmail($request->email);

        // Não encontrou cliente
        if(!$user)
            return redirect()->back()
                ->with('warning', 'E-mail não corresponde a uma conta de usuário!');

        $email  = $request->email;
        $hash   = $request->hash;

        try {
            $decoded = JWT::decode($hash, $this->key, array('HS384'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('warning', $this->translateMessageJwt($e->getMessage()));
        }

        $emailDecode = $decoded->email;

        // E-mail não corresponde
        if ($email != $emailDecode)
            return redirect()->back()
                ->with('warning', 'E-mail não corresponde com a solicitação, faça uma nova solicitação!');

        $data['password'] = Hash::make($request->password);

        $update = $this->user
                    ->where('email', $email)
                    ->update($data);

        if($update)
            return redirect()->route('user.login')
                ->with('success', 'Senha redefinida com sucesso!');

        return redirect()->route('user.login')
            ->withErrors(['Não foi possível redefinir a senha, tente novamente!']);
    }

    private function translateMessageJwt($message)
    {
        switch ($message) {
            case "Expired token":
                return "Solicitação expirada, solicite novamente!";
                break;
            case "Syntax error, malformed JSON":
                return "Solicitação mal informada, solicite novamente!";
                break;
            case "Signature verification failed":
                return "Solicitação mal informada, solicite novamente!";
                break;
            default:
                return $message;
                break;
        }
    }
}
