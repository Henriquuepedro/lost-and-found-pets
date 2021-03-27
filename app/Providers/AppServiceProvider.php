<?php

namespace App\Providers;

use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Contracts\View\View;
use App\Admin;
use App\Cart;
use App\Models\Product;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Variaveis para serem usadas em todas as views
        // Definir um array chamado settings contendo suas
        // respectivas possições para variaveis
        view()->composer('*',function( View $view ) {

            $settings = array();

            $admin  = new Admin();
            $cart   = $this->getCart();

            $dataAdmin = $admin->getAdminMain();

            // Imagem de banner usada em todas as páginas
            $settings['banner'] = asset("user/img/title/" . $dataAdmin->image_title);
            $settings['title_banner'] = $dataAdmin->message_title;
            $settings['email'] = $dataAdmin->email_contact;
            $settings['tel'] = strlen($dataAdmin->tel) == 10 ? preg_replace("/([0-9]{2})([0-9]{4})([0-9]{4})/", "($1) $2-$3", $dataAdmin->tel) : preg_replace("/([0-9]{2})([0-9]{4})([0-9]{5})/", "($1) $2-$3", $dataAdmin->tel);
            $settings['address'] = "{$dataAdmin->address}, {$dataAdmin->number} <br> {$dataAdmin->neighborhood} - {$dataAdmin->city} / {$dataAdmin->state}";
            $settings['logo'] = asset("user/img/admin/{$dataAdmin->picture}");
            $settings['ext_logo'] = explode(".", $dataAdmin->picture)[1];
            $settings['name_store'] = $dataAdmin->name;
            $settings['image_about'] = asset("user/img/about/{$dataAdmin->image_about}");
            $settings['title_about'] = $dataAdmin->title_about;
            $settings['description_about'] = $dataAdmin->description_about;
            // Fim imagem banner
            // Inicio Carrinho
            $settings['cart'] = $cart;
            // Fim carrinho

            $view->with('settings', $settings);
        });
    }

    public function getCart()
    {
        if (!isset($_SESSION))
            session_start();

        $arrItems = array();
        $totalItems = 0;
        $qtyItems = 0;

        $cart    = new Cart();
        $product = new Product();

        if(auth()->guard('client')->user()) {

            $userId = auth()->guard('client')->user()->id;

            $dataCart = $cart
                ->join('products', 'products.id', '=', 'carts.product_id')
                ->join('images', 'images.product_id', '=', 'carts.product_id')
                ->where(['carts.user_id' => $userId, 'images.primary' => 1, 'products.active' => 1])
                ->get();

            foreach ($dataCart as $iten){

                $value = $iten['value'];

                if ($iten['use_price_promo'] == 1 && $iten['qty'] >= $iten['qty_price_promo']) {
                    $value = $iten['price_promo'];
                }

                $stockProduct = $this->verifyStockProduct($iten->product_id);
                if ($iten['qty'] > $stockProduct) {
                    $cart->where(['product_id' => $iten->product_id, 'user_id' => $userId])->update(['qty' => $stockProduct]);
                    $iten['qty'] = $stockProduct;
                }

                $totalIten = (float)$iten['qty'] * (float)$value;
                $totalItems += $totalIten;
                $qtyItems += $iten['qty'];

                $arrIten = array(
                    'id' => $iten->product_id,
                    'name' => $iten->name,
                    'value' => number_format($value, 2, ',', '.'),
                    'path_image' => "{$iten->product_id}/thumbnail_{$iten->path}",
                    'qty' => (int)$iten['qty'],
                    'total' => number_format($totalIten, 2, ',', '.')
                );

                array_push($arrItems, $arrIten);
            }

        }
        else {
            if (!isset($_SESSION['cart']))
                $_SESSION['cart'] = array();

            foreach ($_SESSION['cart'] as $key => $iten) {
                $code_session = (int)$iten['code'];
                $qty_session = (int)$iten['qty'];

                $cart = $product
                        ->join('images', 'images.product_id', '=', 'products.id')
                        ->where(['images.primary' => 1, 'products.id' => $code_session, 'products.active' => 1])
                        ->first();

                if (!$cart) continue;

                $value = $cart->value;

                if ($cart->use_price_promo == 1 && $qty_session >= $cart->qty_price_promo) {
                    $value = $cart->price_promo;
                }

                $stockProduct = $this->verifyStockProduct($cart->product_id);
                if ($qty_session > $stockProduct) {
                    $qty_session = $stockProduct;
                    $_SESSION['cart'][$key]['qty'] = $stockProduct;
                }

                $totalIten = (int)$qty_session * (float)$value;
                $totalItems += $totalIten;
                $qtyItems += (int)$qty_session;

                $arrIten = array(
                    'id' => $cart->product_id,
                    'name' => $cart->name,
                    'value' => number_format($value, 2, ',', '.'),
                    'path_image' => "{$cart->product_id}/thumbnail_{$cart->path}",
                    'qty' => (int)$qty_session,
                    'total' => number_format($totalIten, 2, ',', '.')
                );

                array_push($arrItems, $arrIten);
            }
        }
        return ['items' => $arrItems, 'qty_items' => $qtyItems];
    }

    private function verifyStockProduct($product_id)
    {
        $product = new Product();

        $prd = $product->where(['id' => $product_id, 'active' => 1]);
        if($prd->count() == 0) return 0;

        return $prd->first()->stock;
    }
}
