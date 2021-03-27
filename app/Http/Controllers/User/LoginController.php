<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    private $cart;
    private $user;
    private $product;

    public function __construct(Cart $cart, User $user, Product $product)
    {
        $this->cart     = $cart;
        $this->user     = $user;
        $this->product  = $product;

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

            if (isset($_SESSION['cart'])) {

                foreach ($_SESSION['cart'] as $iten) {
                    $prd_id = $iten['code'];
                    $qty    = $iten['qty'];

                    $prdExist = $this->cart->where(['product_id' => $prd_id, 'user_id' => $userId]);

                    if ($prdExist->count() == 0) {
                        $this->cart->create(['product_id' => $prd_id, 'qty' => $qty, 'user_id' => $userId]);
                    } else {
                        $qty += (float)$prdExist->first()->qty;

                        $product = $this->product->where(['id' => $prd_id, 'active' => 1]);
                        if($product->count() == 0) {
                            $prdExist->delete();
                            continue;
                        }
                        $stockProduct = $product->first()->stock;

                        if ($qty > $stockProduct)
                            $qty = $stockProduct;

                        $prdExist->update(['qty' => $qty]);
                    }
                }
                unset($_SESSION['cart']);
            }

            $this->user->updateLastLogin($userId);

            return redirect()->route('user.account');
        } else {
            return redirect()->route('user.login')->withErrors(['E-mail e senha não corresponde!'])->withInput();
        }
    }


}
