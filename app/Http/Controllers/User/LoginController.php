<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mail\SendMailController;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\Google;

class LoginController extends Controller
{
    private $user;
    private $mail;

    public function __construct(User $user, SendMailController $mail)
    {
        $this->user = $user;
        $this->mail = $mail;

        define("FACEBOOK", [
            'APP_ID'        => '751990502165387',
            'APP_SECRET'    => '32021cb49ad6ef51fcce49636c83058d',
            'APP_REDIRECT'  => 'https://animais.pedrohenrique.net/entrar/facebook',
            'APP_VERSION'   => 'v4.0'
        ]);

        define("GOOGLE", [
            'CLIENT_ID'     => '296954161978-kk40mmeh3ohts52rmh4bis70qrekoidm.apps.googleusercontent.com',
            'SECRET_KEY'    => 'QDf5R9CAlooRcoABa-zu1tl5',
            'APP_REDIRECT'  => 'https://animais.pedrohenrique.net/entrar/google',
//            'APP_HOST'      => 'https://animais.pedrohenrique.net'
        ]);

    }

    public function logout()
    {
        auth()->guard('client')->logout();

        return redirect()->route('user.login');
    }

    public function loginFacebook()
    {
        if (count($_GET))
            return $this->loginExternal('FACEBOOK');

        return redirect()->route('user.login');
    }

    public function loginGoogle()
    {
        if (count($_GET))
            return $this->loginExternal('GOOGLE');

        return redirect()->route('user.login');
    }

    private function loginExternal($app)
    {
        $provider = $this->getProviderExternal($app);

        if (isset($_GET['error'])) {
            $message = 'Não possível realizar a autenticação. Tente novamente!';
            switch ($_GET['error_code']) {
                case 10:
                case 200:
                    $message = 'Acesso não autorizado!';
                    break;
                case 190:
                    $message = 'Sessão para conexão expirada. Tente novamente!';
                    break;
                case 341:
                    $message = 'Limite do aplicativo atingido. Faça o cadastro por e-mail!';
                    break;
                case 368:
                    $message = 'Recurso não disponível. Tente por outro meio!';
                    break;

            }

            return redirect()->route('user.login')->withErrors([$message])->withInput();
        }

        if (!isset($_GET['code'])) {

            // If we don't have an authorization code then get one
            $_SESSION['oauth2state'] = $provider->getState();
            return redirect()->route('user.login')->withErrors(['Permissão não finalizada. Tente novamente!'])->withInput();

        }
        // Check given state against previously stored one to mitigate CSRF attack
//        elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
//            return redirect()->route('user.login')->withErrors(['Permissão não autorizado pelo APP. Tente novamente!'])->withInput();
//        }

        // Try to get an access token (using the authorization code grant)
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // Optional: Now you have a token you can look up a users profile data
        try {

            // We got an access token, let's now get the user's details
            $user = $provider->getResourceOwner($token);

            if ($user) {
                $idApp  = $user->getId();
                $name   = $user->getName();
                $email  = $user->getEmail();
                $expApp = $token->getExpires();

                $userAuthApp = $this->user->getUserByApp($email, $idApp);

                if ($userAuthApp && auth()->guard('client')->loginUsingId($userAuthApp->id)) {

                    $userId = auth()->guard('client')->user()->id;
                    $this->user->updateLastLogin($userId);

                    return redirect()->route('user.account');
                } else {

                    $userExist = $this->user->getUserByEmail($email);

                    if ($userExist)
                        return redirect()->route('user.login')->withErrors(['E-mail de usuário já cadastrado dentro da plataforma. Recupere sua senha!'])->withInput();

                    $dataUserCreate = [
                        'name'      => $name,
                        'email'     => $email,
                        'id_app'    => $idApp,
                        'type_app'  => $app,
                        'exp_app'   => date('Y-m-d H:i:s', $expApp)
                    ];

                    $create = $this->user->create($dataUserCreate);
                    if(!$create)
                        return redirect()->route('user.login')->withErrors(['Não foi possível se registrar. Tente novamente!'])->withInput();

                    $userAuthApp = $this->user->getUserByApp($email, $idApp);

                    if ($userAuthApp && auth()->guard('client')->loginUsingId($userAuthApp->id)) {

                        if(env('APP_ENV') == "production" || env('APP_ENV') == "test")
                            $this->mail->newUser();

                        return redirect()->route('user.account');
                    }

                    return redirect()->route('user.login')->withErrors(['Não foi possível autenciar após a criação. Tente novamente!'])->withInput();
                }

            } else {
                return redirect()->route('user.login')->withErrors(['1 - Permissão não finalizada. Tente novamente!'])->withInput();
            }

        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->route('user.login')->withErrors(['2 - Permissão não finalizada. Tente novamente!'])->withInput();
        }
    }

    public function login()
    {
        if(auth()->guard('client')->user())
            return redirect()->route('user.account');

        $authsExternal = [
            'FACEBOOK' => [
                'url' => $this->getUrlLogin('FACEBOOK'),
                'icon' => 'fab fa-facebook-f'
            ],
            'GOOGLE' => [
                'url' => $this->getUrlLogin('GOOGLE'),
                'icon' => 'fab fa-google'
            ]
        ];


        return view('user.login.login', compact('authsExternal'));
    }

    public function loginPost(Request $request)
    {
        $dataUserAuth = [
            'email'     => $request->email,
            'password'  => $request->password
        ];

        if ( auth()->guard('client')->attempt($dataUserAuth)) {

            $userId   = auth()->guard('client')->user()->id;

            $this->user->updateLastLogin($userId);

            return redirect()->route('user.account');
        } else {
            return redirect()->route('user.login')->withErrors(['E-mail e senha não corresponde!'])->withInput();
        }
    }

    public function removeDataUser(Request $request)
    {

    }

    private function getProviderExternal(string $type)
    {
        $provider = null;

        switch ($type) {
            case 'FACEBOOK':
                $provider = new Facebook([
                    'clientId'          => FACEBOOK['APP_ID'],
                    'clientSecret'      => FACEBOOK['APP_SECRET'],
                    'redirectUri'       => FACEBOOK['APP_REDIRECT'],
                    'graphApiVersion'   => FACEBOOK['APP_VERSION']
                ]);
                break;
            case 'GOOGLE':
                $provider = new Google([
                    'clientId'      => GOOGLE['CLIENT_ID'],
                    'clientSecret'  => GOOGLE['SECRET_KEY'],
                    'redirectUri'   => GOOGLE['APP_REDIRECT'],
//                    'hostedDomain'  => GOOGLE['APP_HOST']
                ]);
                break;
        }

        return $provider;
    }

    public function getUrlLogin(string $type): string
    {
        $provider = $this->getProviderExternal($type);

        return $provider->getAuthorizationUrl([
            'scope' => ['email']
        ]);
    }


}
