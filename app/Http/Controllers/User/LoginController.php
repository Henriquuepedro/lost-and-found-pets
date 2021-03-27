<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Cart;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    private $cart;
    private $user;
    private $product;

    public function __construct(Cart $cart, User $user)
    {
        $this->cart = $cart;
        $this->user = $user;

    }

    public function logout()
    {
        auth()->guard('client')->logout();

        return redirect()->route('user.login');
    }

    public function login()
    {
        if(auth()->guard('client')->user())
            return redirect()->route('user.account');

        return view('user.login.login');
    }

    public function loginPost(Request $request)
    {
        if (!isset($_SESSION))
            session_start();

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


}
