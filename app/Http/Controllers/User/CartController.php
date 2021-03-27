<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use FlyingLuscas\Correios\Client;
use Illuminate\Http\Request;
use App\Cart;
use App\Models\Product;
use App\Models\Image;
use FlyingLuscas\Correios\Service;
use GuzzleHttp\Client as HttpClient;
use FlyingLuscas\Correios\Services\Freight;
use App\Admin;
use App\Models\LogHistory;
use Illuminate\Support\Facades\DB;
use App\Models\Promotion;

class CartController extends Controller
{
    private $cart;
    private $product;
    private $image;
    private $http;
    private $correios;
    private $admin;
    private $log;
    private $promotion;
    private $cross_docking;

    public function __construct(Cart $cart, Product $product, Image $image, HttpClient $http, Client $correios, Admin $admin, LogHistory $log, Promotion $promotion)
    {
        if (!isset($_SESSION))
            session_start();

        $this->cart     = $cart;
        $this->product  = $product;
        $this->image    = $image;
        $this->http     = $http;
//        $this->freight  = new Freight($this->http);
        $this->correios = $correios;
        $this->admin    = $admin;
        $this->log      = $log;
        $this->promotion= $promotion;

        $dataAdmin              = $admin->getAdminMain();
        $this->cross_docking    = (int)$dataAdmin->order_submission_limit;
    }

    public function add(Request $request)
    {
        $qty        = 1;
        $prd_id     = (int)$request->product_id;
        $newProduct = true;
        $itemsCart  = array();

        if($request->qty) $qty = (float)$request->qty;

        $qtyVerify  = $qty;

        if($prd_id === 0 || $qty === 0){
            echo json_encode(array(false, 'Não foi encontrado parâmetros!', array()));
            exit();
        }
        $stock  = $this->verifyStockProduct($prd_id);
        $cart   = $this->getItemsCart();
        foreach ($cart as $prd){
            if($prd['id'] == $prd_id){
                $qtyVerify += (int)$prd['qty'];
                break;
            }
        }

        if($stock >= $qtyVerify) {
            if (auth()->guard('client')->user()) {
                $userId = auth()->guard('client')->user()->id;

                $prdExist = $this->cart->where(['product_id' => $prd_id, 'user_id' => $userId]);

                if ($prdExist->count() == 0) {
                    $this->cart->create(['product_id' => $prd_id, 'qty' => $qty, 'user_id' => $userId]);
                } else {
                    $qty += (float)$prdExist->first()->qty;
                    $this->cart->where(['product_id' => $prd_id, 'user_id' => $userId])->update(['qty' => $qty]);
                }
            } else {

                if (!isset($_SESSION['cart']))
                    $_SESSION['cart'] = array();

                foreach ($_SESSION['cart'] as $iten) {
                    if ($iten['code'] == $prd_id) {
                        $iten['qty'] += $qty;
                        $newProduct = false;
                    }
                    array_push($itemsCart, $iten);
                }

                if ($newProduct)
                    array_push($itemsCart, array(
                        'code' => $prd_id,
                        'qty' => $qty
                    ));

                //Atualizar sessão
                $_SESSION['cart'] = $itemsCart;
            }
        }

        if($stock < $qtyVerify) {
            echo json_encode(array(false, "Esse produto possuí apenas {$stock} unidade(s) em estoque!", $this->getDataAfterUpdate($prd_id)));
            exit();
        }
        echo json_encode(array(true, $this->getDataAfterUpdate($prd_id), array()));
    }

    private function verifyStockProduct($product_id)
    {
        $product = $this->product->where(['id' => $product_id, 'active' => 1]);
        if($product->count() == 0) return 0;

        return $product->first()->stock;
    }

    public function update(Request $request)
    {
        $prd_id = (int)$request->product_id;
        $qty    = (int)$request->qty_item;
        $stock  = $this->verifyStockProduct($prd_id);

        if($prd_id === 0 || $qty === 0){
            echo json_encode(array(false, 'Não foi encontrado parâmetros!', array()));
            exit();
        }

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        if($stock >= $qty) {
            if (auth()->guard('client')->user()) {
                $userId = auth()->guard('client')->user()->id;

                $prdExist = $this->cart->where(['product_id' => $prd_id, 'user_id' => $userId]);

                if ($prdExist->count() == 0) {
                    echo json_encode(array(false, "Não foi encontrado parâmetros!", array()));
                } else {
                    $this->cart->where(['product_id' => $prd_id, 'user_id' => $userId])->update(['qty' => $qty]);
                }
            } else {
                $itemsCart = array();
                if (isset($_SESSION['cart'])) {

                    foreach ($_SESSION['cart'] as $iten) {
                        if ($iten['code'] == $prd_id) {
                            $iten['qty'] = $qty;
                        }
                        array_push($itemsCart, $iten);
                    }

                    $_SESSION['cart'] = $itemsCart;

                }
            }
        }

        $result = $this->getDataAfterUpdate($prd_id);

        $prds_id = $this->getItemsCart();
        $result['dataFrete'] = $this->getValueFrete($prds_id, null, $this->formataValor($result['value_total'], 'en'));

        if($stock < $qty){
            DB::rollBack();
            echo json_encode(array(false, "Esse produto possuí apenas {$stock} unidade(s) em estoque!", $result));
            exit();
        }

        DB::commit();
        echo json_encode(array(true, $result));
    }

    public function cart()
    {
        $arrItems = array();
        $totalItems = 0;
        $qtyItems = 0;

        if(auth()->guard('client')->user()){

            $userId   = auth()->guard('client')->user()->id;

            $cart = $this->cart
                            ->join('products', 'products.id' , '=', 'carts.product_id')
                            ->join('images', 'images.product_id' , '=', 'carts.product_id')
                            ->where(['carts.user_id' => $userId, 'images.primary' => 1, 'products.active' => 1])
                            ->get();


            foreach ($cart as $iten){

                $value = $iten['value'];

                if ($iten['use_price_promo'] == 1 && $iten['qty'] >= $iten['qty_price_promo']) {
                    $value = $iten['price_promo'];
                }

                $stockProduct = $this->verifyStockProduct($iten['product_id']);
                if ($iten['qty'] > $stockProduct) {
                    $this->cart->where(['product_id' => $iten['product_id'], 'user_id' => $userId])->update(['qty' => $stockProduct]);
                    $iten['qty'] = $stockProduct;
                }

                $totalIten = (float)$iten['qty'] * (float)$value;
                array_push($arrItems, array(
                    'id'            => $iten['product_id'],
                    'name'          => $iten['name'],
                    'value'         => number_format($value,2, ',', '.'),
                    'path_image'    => "{$iten['product_id']}/{$iten['path']}",
                    'qty'           => (int)$iten['qty'],
                    'total'         => number_format($totalIten,2,',','.')
                ));
                $totalItems += $totalIten;
                $qtyItems += $iten['qty'];
            }

        } else {

            if (isset($_SESSION['cart'])) {

                foreach($_SESSION['cart'] as $key => $iten){
                    $code_session   = (int)$iten['code'];
                    $qty_session    = (int)$iten['qty'];

                    $cart = $this->product
                        ->join('images', 'images.product_id' , '=', 'products.id')
                        ->where(['images.primary' => 1, 'products.id' => $code_session, 'products.active' => 1])
                        ->first();

                    if (!$cart) continue;

                    if($cart == null){
                        unset($_SESSION['cart'][$key]);
                        continue;
                    }

                    $value = $cart->value;

                    if ($cart['use_price_promo'] == 1 && $qty_session >= $cart['qty_price_promo']) {
                        $value = $cart['price_promo'];
                    }

                    $stockProduct = $this->verifyStockProduct($cart->product_id);
                    if ($qty_session > $stockProduct) {
                        $qty_session = $stockProduct;
                        $_SESSION['cart'][$key]['qty'] = $stockProduct;
                    }

                    $totalIten = (int)$qty_session * (float)$value;

                    array_push($arrItems, array(
                        'id'            => $cart->product_id,
                        'name'          => $cart->name,
                        'value'         => number_format($value,2, ',', '.'),
                        'path_image'    => "{$cart->product_id}/{$cart->path}",
                        'qty'           => (int)$qty_session,
                        'total'         => number_format($totalIten,2,',','.')
                    ));
                    $totalItems += $totalIten;
                    $qtyItems += (int)$qty_session;

                }
            }
            // RECUPERAR DA SESSÃO
        }

        $dataCart = array(
            'value_total'   => number_format($totalItems,2,',','.'),
            'qty_items'     => $qtyItems,
            'arrItems'      => $arrItems,
//            'valueFrete'    => $valueFrete
        );

        return view('user.order.cart', compact('dataCart'));
    }

    private function getItemsCart()
    {
        $arrItems = array();

        if(auth()->guard('client')->user()) {

            $userId = auth()->guard('client')->user()->id;

            $cart = $this->cart
                ->where('user_id', $userId)
                ->get();

            foreach ($cart as $iten){

                $qtyn   = (float)$iten['qty'];
                $prd_id = $iten['product_id'];

                array_push($arrItems, array(
                    'id'    => $prd_id,
                    'qty'   => $qtyn
                ));
            }

        }
        else {
            if(isset($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $iten) {
                    $code_session = (int)$iten['code'];
                    $qty_session = (int)$iten['qty'];

                    array_push($arrItems, array(
                        'id' => $code_session,
                        'qty' => $qty_session
                    ));
                }
            }
        }

        return $arrItems;
    }

    public function getDataAfterUpdate($prd_id)
    {
        $arrItems = array();
        $totalItems = 0;
        $qtyItems = 0;

        if(auth()->guard('client')->user()) {

            $userId = auth()->guard('client')->user()->id;

            $cart = $this->cart
                ->join('products', 'products.id', '=', 'carts.product_id')
                ->join('images', 'images.product_id', '=', 'carts.product_id')
                ->where(['carts.user_id' => $userId, 'images.primary' => 1, 'products.active' => 1])
                ->get();

            foreach ($cart as $iten){

                $value = $iten['value'];

                if ($iten['use_price_promo'] == 1 && $iten['qty'] >= $iten['qty_price_promo']) {
                    $value = $iten['price_promo'];
                }

                $totalIten = (float)$iten['qty'] * (float)$value;
                $totalItems += $totalIten;
                $qtyItems += $iten['qty'];

                if($iten['product_id'] != $prd_id) continue;

                $arrItems = array(
                    'id' => $iten->product_id,
                    'name' => $iten->name,
                    'value' => number_format($value, 2, ',', '.'),
                    'path_image' => "{$iten->product_id}/{$iten->path}",
                    'qty' => (int)$iten['qty'],
                    'total' => number_format($totalIten, 2, ',', '.')
                );
            }

        }
        else {
            if (!isset($_SESSION['cart']))
                $_SESSION['cart'] = array();

            foreach ($_SESSION['cart'] as $iten) {
                $code_session = (int)$iten['code'];
                $qty_session = (int)$iten['qty'];

                $cart = $this->product
                    ->join('images', 'images.product_id', '=', 'products.id')
                    ->where(['images.primary' => 1, 'products.id' => $code_session])
                    ->first();

                $value = $cart->value;

                if ($cart->use_price_promo == 1 && $qty_session >= $cart->qty_price_promo) {
                    $value = $cart->price_promo;
                }

                $totalIten = (int)$qty_session * (float)$value;
                $totalItems += $totalIten;
                $qtyItems += (int)$qty_session;

                if ($cart->product_id != $prd_id) continue;

                $arrItems = array(
                    'id' => $cart->product_id,
                    'name' => $cart->name,
                    'value' => number_format($value, 2, ',', '.'),
                    'path_image' => "{$cart->product_id}/{$cart->path}",
                    'qty' => (int)$qty_session,
                    'total' => number_format($totalIten, 2, ',', '.')
                );

            }
        }

        $itemsCart = array(
            'value_total'   => number_format($totalItems,2,',','.'),
            'qty_items'     => $qtyItems,
            'arrItems'      => $arrItems
        );

        return $itemsCart;
    }

    public function delete(Request $request)
    {
        $arrItems = array();
        $prd_id = (int)$request->product_id;

        if(auth()->guard('client')->user()) {

            $userId = auth()->guard('client')->user()->id;

            $cart = $this->cart
                ->where(['user_id' => $userId, 'product_id' => $prd_id]);

            if($cart->count() != 0){
                $cart->delete();
            }
        }
        else {
            if (!isset($_SESSION['cart']))
                $_SESSION['cart'] = array();

            foreach ($_SESSION['cart'] as $key => $iten){
                if($iten['code'] == $prd_id){
                    continue;
                }
                array_push($arrItems, $iten);
            }

            $_SESSION['cart'] = $arrItems;
        }

        $result = $this->getDataAfterUpdate($prd_id);

        $prds_id = $this->getItemsCart();
        $result['dataFrete'] = $this->getValueFrete($prds_id, null, $result['value_total']);

        echo json_encode($result);

    }

    private function getValueFrete($prds_id, $cep = null, $total_order = 0)
    {
        // comprimento 15 - 104
        // largura 10 - 104
        // altura 1 - 104
        // comprimento + largura + altura menor que 199
        // diametro 4 - 90
        // soma comprimento + (2 * diametro) menos que 199

//        Comprimento (C): 16 cm – 105 cm
//        Largura (L): 11 cm – 105 cm
//        Altura (A): 2 cm – 105 cm
//        Soma das dimensões (C+L+A): 29 cm – 200 cm

        if(count($prds_id) === 0 || (!isset($_SESSION['cep']) && $cep === null)) return false;

        $admin = $this->admin->getAdminMain();

        $cepEmpresa = $admin->cep;
        $cepCliente = $cep ?? $_SESSION['cep'];
        $results    = array();
        $arrValores = array();

        $altura_correios = 0;
        $largura_correios = 0;
        $comprimento_correios = 0;
        $etiquetas_correios = array();
        $menor_medida_correios = array();
        $count_etiqueta_correios = 0;

        foreach ($prds_id as $iten){

            $code           = (int)$iten['id'];
            $qty_cart       = (int)$iten['qty'];
            $qty_etiqueta   = 1;

            $product        = $this->product->where(['id' => $code, 'active' => 1])->first();
            if (!$product) continue;

            $priceItem = $product->value;
            if ($product->use_price_promo == 1 && $qty_cart >= $product->qty_price_promo) // verifica se a qtd participa de promoção
                $priceItem = $product->price_promo;

            for ($qty_iten = 0; $qty_iten < $qty_cart; $qty_iten++) {

                $profundidade_iten = (float)$product->depth;
                $largura_iten = (float)$product->width;
                $altura_iten = (float)$product->height;
                $peso_iten = (float)number_format((float)$product->weight,2, '.','');

                $comprimento_correios += $profundidade_iten;
                $largura_correios += $largura_iten;
                $altura_correios += $altura_iten;

                if ($comprimento_correios > 105 || $largura_correios > 105 || $altura_correios > 105 || (($comprimento_correios+$largura_correios+$altura_correios) > 200)) {
                    $count_etiqueta_correios++;
                    $comprimento_correios = $profundidade_iten;
                    $largura_correios = $largura_iten;
                    $altura_correios = $altura_iten;
                }

                $menor_medida_correios[$count_etiqueta_correios] = "C";

                if ($largura_correios < $comprimento_correios) $menor_medida_correios[$count_etiqueta_correios] = "L";
                if ($altura_correios < $comprimento_correios) $menor_medida_correios[$count_etiqueta_correios] = "A";

                if ($comprimento_correios < $largura_correios && $comprimento_correios < $altura_correios) $menor_medida_correios[$count_etiqueta_correios] = "C";
                if ($largura_correios < $comprimento_correios && $largura_correios < $altura_correios) $menor_medida_correios[$count_etiqueta_correios] = "L";
                if ($altura_correios < $comprimento_correios && $altura_correios < $largura_correios) $menor_medida_correios[$count_etiqueta_correios] = "A";
                if ($altura_correios == $comprimento_correios && $altura_correios == $largura_correios) $menor_medida_correios[$count_etiqueta_correios] = "A";

                if(isset($etiquetas_correios[$count_etiqueta_correios][$code])) {
                    $qty_etiqueta++;
                    $priceItem += $etiquetas_correios[$count_etiqueta_correios][$code]['value_declared'];
                }

                $etiquetas_correios[$count_etiqueta_correios][$code] = array(
                    'altura' => $altura_iten,
                    'largura' => $largura_iten,
                    'comprimento' => $profundidade_iten,
                    'peso_bruto' => $peso_iten,
                    'qty' => $qty_etiqueta,
                    "value_declared" => $priceItem,
                );
            }
        }

        foreach ($etiquetas_correios as $id_etiqueta => $etiqueta) {

            $peso = 0;
            $comprimento = 0;
            $largura = 0;
            $altura = 0;
            $valor_declarado = 0;
            $quantidade = 0;

            $freight = new Freight($this->http);

            $freight->origin($cepEmpresa)
                ->destination($cepCliente)
                ->services(Service::SEDEX, Service::PAC);

            foreach ($etiqueta as $id_product => $product) {

                if ($menor_medida_correios[$count_etiqueta_correios] == null)
                    $menor_medida_correios[$count_etiqueta_correios] = "L";

                switch ($menor_medida_correios[$id_etiqueta]) {
                    case "L":
                        $comprimento = $comprimento < (float)$product['comprimento'] ? (float)$product['comprimento'] : $comprimento;
                        $altura = $altura < (float)$product['altura'] ? (float)$product['altura'] : $altura;

                        $largura += (float)$product['largura'] * (int)$product['qty'];
                        break;
                    case "C":
                        $altura = $altura < (float)$product['altura'] ? (float)$product['altura'] : $altura;
                        $largura = $largura < (float)$product['largura'] ? (float)$product['largura'] : $largura;

                        $comprimento += $product['comprimento'] * (int)$product['qty'];
                        break;
                    case "A":
                        $largura = $largura < (float)$product['largura'] ? (float)$product['largura'] : $largura;
                        $comprimento = $comprimento < (float)$product['comprimento'] ? (float)$product['comprimento'] : $comprimento;

                        $altura += (float)$product['altura'] * (int)$product['qty'];
                        break;
                }

                $peso += (float)$product['peso_bruto'] * (int)$product['qty'];
                $valor_declarado += (float)$product['value_declared'] * (int)$product['qty'];
                $quantidade += (int)$product['qty'];

            }
            $freight->item($largura, $altura, $comprimento, $peso, $quantidade); // largura, altura, comprimento, peso e quantidade
            array_push($arrValores, $freight->calculate());
        }

        $results[0]['name']  = "";
        $results[0]['price'] = 0;
        $results[0]['date']  = "";
        $results[1]['name']  = "";
        $results[1]['price'] = 0;
        $results[1]['date']  = "";

        foreach ($arrValores as $fretes) {

            foreach ($fretes as $key => $frete) {

                $dias = $frete['deadline'] + $this->cross_docking;

                $results[$key]['name'] = $frete['name'];
                $results[$key]['price'] += $frete['price'];
                $results[$key]['date'] = date('d/m/Y', strtotime("+{$dias} days", strtotime(date('Y-m-d'))));
            }

        }
        $promotionFrete_FreteMenorXReais = $this->getValuePromotionFrete_FreteMenorXReais();
        $promotionFrete_PedidoMaiorXReais = $this->getValuePromotionFrete_PedidoMaiorXReais();
        foreach ($results as $key => $result){

            $result['price'] = $promotionFrete_FreteMenorXReais ? number_format($promotionFrete_FreteMenorXReais,2, '.','') >= number_format($result['price'],2, '.','') ? 0 : $result['price'] : $result['price'];
            $result['price'] = $promotionFrete_PedidoMaiorXReais && $result['name'] == 'PAC' ? number_format($total_order,2, '.','') >= number_format($promotionFrete_PedidoMaiorXReais,2, '.','') ? 0 : $result['price'] : $result['price'];

            $results[$key]['price'] = number_format($result['price'], 2, ',', '.');
        }
        return $results;
    }

    /**
     * @param Cart $cart
     */
    private function setCepUserSession($cep)
    {
        $_SESSION['cep'] = $cep;
    }

    public function defineCepUser(Request $request)
    {
        $result = false;
        $cep = filter_var(preg_replace('~[.-]~', '', $request->cep), FILTER_SANITIZE_NUMBER_INT);
        $product_id = isset($request->product_id) ? $request->product_id : null;

        if(strlen($cep) !== 8){
            echo json_encode(false);
            exit();
        }
//        if(env('APP_ENV') == "production") {
//            $consultaCEP = $this->correios->zipcode()->find($cep);
//        }

//        $rsCep = $this->getDataCep($cep)['resultado'];

        if(!isset($consultaCEP['error'])) {
            $this->setCepUserSession($cep);

            $prds_id = !$product_id ? $this->getItemsCart() : array(['id' => $product_id, 'qty' => 1]);
            $totalOrder = $this->getDataAfterUpdate($prds_id);
            $totalOrder['value_total'] = $this->formataValor($totalOrder['value_total'], 'en');

            if ($product_id) {
                $productUn = $this->product->where(['id' => $product_id, 'active' => 1])->first();
                $totalOrder['value_total'] = $productUn->value;
            }


            $result = $this->getValueFrete($prds_id, null, $totalOrder['value_total']);

            if(isset($result['errors'])){
                $rsError = "";
                foreach ($result['errors'] as $error){
                    $rsError .= $error . " | ";
                }
                echo json_encode(array('success' => false, 'data' => $rsError));
                exit();
            }
        }
        else{
            echo json_encode(array('success' => false, 'data' => array('CEP inválido, corrija e tente novamente!')));
            exit();
        }

        echo json_encode(array('success' => true, 'data' => $result));
    }

    public function getDataCep($cep)
    {
        $resultado = @file_get_contents('https://republicavirtual.com.br/web_cep.php?cep='.$cep.'&formato=query_string');
        if(!$resultado){
            $resultado = "&resultado=0&resultado_txt=erro+ao+buscar+cep";
        }
        parse_str($resultado, $retorno);

        return $retorno;
    }

    private function getValuePromotionFrete_FreteMenorXReais()
    {
        $query = $this->promotion->getPromotionForType(1);

        return $query ? $query->value : false;
    }

    private function getValuePromotionFrete_PedidoMaiorXReais()
    {
        $query = $this->promotion->getPromotionForType(2);

        return $query ? $query->value : false;
    }
}
