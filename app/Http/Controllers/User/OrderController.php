<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use FlyingLuscas\Correios\Service;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Http\Controllers\User\AddressController;
use App\Models\Product;
use App\Cart;
use FlyingLuscas\Correios\Services\Freight;
use GuzzleHttp\Client as HttpClient;
use App\Models\Coupon;
use MercadoPago\Payment;
use MercadoPago\SDK;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItems;
use App\Models\OrderPayment;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\DB;
use App\Models\Image;
use FlyingLuscas\Correios\Client;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Rate;
use App\Admin;
use App\Models\LogHistory;
use App\Http\Controllers\Mail\SendMailController;
use App\Http\Controllers\User\ExceptionMercadoPagoController;
use MercadoPago\Item as ItemMercadoPago;
use MercadoPago\Payer;
use App\Models\Promotion;

class OrderController extends Controller
{
    private $address;
    private $addressController;
    private $product;
    private $cart;
    private $http;
    private $freight;
    private $coupon;
    private $order;
    private $order_address;
    private $order_items;
    private $order_payment;
    private $order_status;
    private $image;
    private $correios;
    private $translate;
    private $rate;
    private $admin;
    private $log;
    private $sendMail;
    private $expMp;
    private $company_name;
    private $promotion;
    private $cross_docking;

    public function __construct(
        Address $address,
        AddressController $addressController,
        Product $product,
        Cart $cart,
        HttpClient $http,
        Coupon $coupon,
        Order $order,
        OrderAddress $order_address,
        OrderItems $order_items,
        OrderPayment $order_payment,
        OrderStatus $order_status,
        Image $image,
        Client $correios,
        Rate $rate,
        Admin $admin,
        LogHistory $log,
        SendMailController $sendMail,
        ExceptionMercadoPagoController $expMp,
        Promotion $promotion
    )
    {
        if (!isset($_SESSION))
            session_start();

        $this->address          = $address;
        $this->addressController= $addressController;
        $this->product          = $product;
        $this->cart             = $cart;
        $this->http             = $http;
        $this->freight          = new Freight($this->http);
        $this->coupon           = $coupon;
        $this->order            = $order;
        $this->order_address    = $order_address;
        $this->order_items      = $order_items;
        $this->order_payment    = $order_payment;
        $this->order_status     = $order_status;
        $this->image            = $image;
        $this->correios         = $correios;
        $this->translate        = new GoogleTranslate('en', 'pt');
        $this->rate             = $rate;
        $this->admin            = $admin;
        $this->log              = $log;
        $this->sendMail         = $sendMail;
        $this->expMp            = $expMp;
        $this->promotion        = $promotion;

        $dataAdmin              = $admin->getAdminMain();
        $this->company_name     = $dataAdmin->name;
        $this->cross_docking    = (int)$dataAdmin->order_submission_limit;
    }

    public function order($id)
    {
        $amount_products    = 0;
        $statusRecebido     = false;
        $statusPgtoAprovado = false;
        $statusEmTransporte = false;
        $statusEntregue     = false;
        $statusCancelado    = false;
        $userId             = auth()->guard('client')->user()->id;

        $dataOrder = $this->order
            ->select(['*', 'orders.created_at as order_created_at'])
            ->join('order_addresses', 'orders.id', '=', 'order_addresses.order_id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('order_payments', 'orders.id', '=', 'order_payments.order_id')
            ->where(['orders.id' => $id, 'orders.user_id' => $userId, 'order_addresses.user_id' => $userId, 'order_items.user_id' => $userId, 'order_payments.user_id' => $userId])
            ->get();

        if(count($dataOrder) == 0){
            return redirect()->route('user.account.orders');
        }


        foreach ($dataOrder as $key => $iten){
            $prd = $this->image->where(['product_id' => $iten['product_id'], 'primary' => 1])->first();
            $amount_products += (float)$iten['total_iten'];
            $dataOrder[$key]['image_prd'] = $iten['product_id'].'/'.$prd->path;
        }

        // pedido recebido - sempre será um pedido recebido
        $statusRecebido = true;

        // pagamento aprovado - verificar se existe o status 3 ou 4
        $statusPago = $this->order_status
            ->where(['order_id' => $id, 'status' => 3]);

        // pagamento cancelado - verificar se existe o status 7
        $statusCanceladoDb = $this->order_status
            ->where('order_id', $id)
            ->whereIn('status', [7, 55, 99]);

        // pedido enviado - verificar se existe o status 51
        $statusEmTransporteDb = $this->order_status
            ->where(['order_id' => $id, 'status' => 51]);

        // pedido entregue - verificar se existe o status 54
        $statusEntregueDb = $this->order_status
            ->where(['order_id' => $id, 'status' => 54]);

        // avaliado
        $statusRated = $this->rate->existRateOrder($id);

        if ($statusPago->count() > 0) $statusPgtoAprovado = true;
        if ($statusCanceladoDb->count() > 0) $statusCancelado = true;
        if ($statusEmTransporteDb->count() > 0) $statusEmTransporte = true;
        if ($statusEntregueDb->count() > 0) $statusEntregue = true;

        // entregue - liberar para o usuário ou admin marcar como entrgue
        //montar lógica ainda

        $statusInProgress = array(
            'recebido'      => $statusRecebido && !$statusPgtoAprovado && !$statusEmTransporte && !$statusEntregue && !$statusCancelado,
            'aprovado'      => $statusPgtoAprovado && !$statusEmTransporte && !$statusEntregue && !$statusCancelado,
            'em_transporte' => $statusEmTransporte && !$statusEntregue && !$statusCancelado,
            'entregue'      => $statusEntregue && !$statusCancelado,
            'cancelado'     => $statusCancelado
        );
        $statusDone = array(
            'recebido'      => $statusRecebido && (!$statusInProgress['recebido'] || $statusCancelado),
            'aprovado'      => $statusPgtoAprovado && !$statusInProgress['aprovado'] && !$statusCancelado,
            'em_transporte' => $statusEmTransporte && !$statusInProgress['em_transporte'] && !$statusCancelado
        );
        $statusDate = array(
            'recebido'      => $statusRecebido ? date('d/m H:i', strtotime($dataOrder[0]['order_created_at'])) : "",
            'aprovado'      => $statusPgtoAprovado && !$statusCancelado ? date('d/m H:i', strtotime($statusPago->first()->date)) : "",
            'em_transporte' => $statusEmTransporte && !$statusCancelado ? date('d/m H:i', strtotime($statusEmTransporteDb->first()->date)) : "",
            'entregue'      => $statusEntregue && !$statusCancelado ? date('d/m H:i', strtotime($statusEntregueDb->first()->date)) : "",
            'cancelado'     => $statusCancelado ? date('d/m H:i', strtotime($statusCanceladoDb->first()->date)) : "",
        );

        $status = array(
            'progress'   => $statusInProgress,
            'done'       => $statusDone,
            'dateStatus' => $statusDate,
            'rated'      => $statusRated
        );

        $trackings = [];
        if($statusEmTransporte){
            $codes_tracking = json_decode($dataOrder[0]['codes_tracking']);
            $dates_tracking = json_decode($dataOrder[0]['dates_tracking']);

            $trackings = [
                'codes' => $codes_tracking,
                'dates' => $dates_tracking
            ];

        }

        return view('user.order.detail', compact('dataOrder', 'amount_products', 'status', 'trackings'));
    }

    public function checkout()
    {
        $erros = array();
        // Percorre carrinho e criar array com os itens
        $items = $this->getItemsCart();
        if(count($items) == 0)
            return redirect()->route('user.cart')
                ->withErrors($erros);
        $arrProducts = array();
        foreach ($items as $iten){
            $id = $iten['id'];
            $qty = (int)$iten['qty'];

            $product = $this->product->where(['id' => $id, 'active' => 1])->first();
            if (!$product) continue;

            $stock = $this->verifyStockProduct($id);

            if($stock < $qty)
                array_push($erros, "O produto <strong>{$product->name}</strong> está com uma quantidade indisponível. Estoque: {$stock}.");
        }
        if(count($erros) > 0)
            return redirect()->route('user.cart')
                ->withErrors($erros);

        $cepCliente = null;
        unset($_SESSION['cupom']);

        $userId      = auth()->guard('client')->user()->id;
        $dataAddress = $this->addressController->getArrayAddress($userId);
        foreach ($dataAddress as $address) if($address['default'] == 1) $cepCliente = $address['cep'];
//        $dataFrete = $this->getValueFrete($cepCliente);

        $products = $this->getProductsCartCheckout();

        if ($products['qty_total_cart'] == 0)
            return redirect()->route('user.products');

        return view('user.order.checkout', compact('dataAddress', 'products'));
    }

    private function getItemsCart()
    {
        $arrItems = array();

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

        return $arrItems;
    }

    private function getValueFrete($cepCliente, $total_order)
    {
        // comprimento 16 - 105
        // largura 11 - 105
        // altura 2 - 105
        // comprimento + largura + altura menor que 200

        $prds_id = $this->getItemsCart();
        $cepCliente = $cepCliente === null ? 0 : filter_var(preg_replace('~[.-]~', '', $cepCliente), FILTER_SANITIZE_NUMBER_INT);

        if(count($prds_id) === 0 || strlen($cepCliente) != 8) return false;

        $admin = $this->admin->getAdminMain();

        $cepEmpresa = $admin->cep;
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

    public function getProductsCartCheckout()
    {
        $totalItems = 0;
        $qtyItems   = 0;
        $arrItems   = array();
        $userId     = auth()->guard('client')->user()->id;

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

        return array(
            'value_total_cart'  => number_format($totalItems, 2, ',', '.'),
            'qty_total_cart'    => $qtyItems,
            'arr_tems'          => $arrItems
        );
    }

    private function getCepForAddressId(int $address_id)
    {
        $userId = auth()->guard('client')->user()->id;

        return $this->address
            ->where(['user_id' => $userId, 'id' => $address_id])
            ->first()->cep;

    }

    private function getAddressForAddressId(int $address_id)
    {
        $userId = auth()->guard('client')->user()->id;

        return $this->address
            ->where(['user_id' => $userId, 'id' => $address_id])
            ->first();

    }

    private function getValueTotalOrderCheckout($cepClient, $typeFrete)
    {

        $v_total = $this->getProductsCartCheckout()['value_total_cart'];
        $v_total_cal = $this->formataValor($this->getProductsCartCheckout()['value_total_cart'], 'en');
        $dataFrete = $this->getValueFrete($cepClient, $v_total_cal);

        if(isset($dataFrete['errors'])) return false;

        foreach($dataFrete as $frete){
            if($typeFrete != $frete['name']) continue;

            $v_frete_cal = $this->formataValor($frete['price'], 'en');

            $cupom = isset($_SESSION['cupom']) ? $_SESSION['cupom'] : false;
            $v_cupom = $this->getCupomIten($cupom);
            if($v_cupom == false) $v_cupom = 0;
            $v_cupom_format = $this->formataValor($v_cupom);
            $value_total_order = number_format($v_frete_cal,2, '.','') + number_format($v_total_cal,2, '.','') - number_format($v_cupom,2, '.','');
            $value_total_order_format = $this->formataValor($value_total_order);

            return array('price' => $frete['price'], 'date' => $frete['date'], 'total_products' => $v_total, 'total_order' => $value_total_order_format, 'value_cupom' => array('status' => $v_cupom == 0 ? false : true, 'value' => $v_cupom_format, 'cupom' => $cupom));
        }
    }

    public function getValueParcelsQtyParcel(Request $request)
    {
        $type       = $request->type_frete;
        $address_id = $request->cep_frete;
        $qntPacels  = (int)$request->qty_parcel;

        if($qntPacels <= 0 || $qntPacels > 12){
            echo json_encode(false);
            exit();
        }

        $cepClient = $this->getCepForAddressId($address_id);
        $valueTotais = $this->getValueTotalOrderCheckout($cepClient, $type);
        $value_total_order = $this->formataValor($valueTotais['total_order'], 'en');

        $parcel = $this->calcParcelaJuros($value_total_order, $qntPacels);

        echo json_encode($parcel);
    }

    public function insertCupom(Request $request)
    {
        $cupom = filter_var($request->cupom, FILTER_SANITIZE_STRING);
        $response = $this->setCupomCheckout($cupom);
        $response = is_numeric($response) ? true : $response;
        echo json_encode($response);
    }

    public function setCupomCheckout($cupom)
    {

        $_SESSION['cupom'] = $cupom;
        $queryCupom = $this->coupon->where('name_coupon', $cupom)->whereDate('date_expired', '>', date('Y-m-d'));
        if($queryCupom->count() == 0) return false;

        return $this->getCupomIten($cupom);
    }

    private function getCupomIten($cupom)
    {
        if($cupom == false) return false;

        $items = $this->getItemsCart();

        $queryCupom = $this->coupon->where('name_coupon', $cupom)->whereDate('date_expired', '>', date('Y-m-d'));
        if($queryCupom->count() == 0) return false;

        $rsProducts = $queryCupom->first();
        $prdsCupom  = json_decode($rsProducts->products_id);
        $percentage = (float)$rsProducts->percentage;
        $valueCupom = 0;
        foreach ($items as $iten){
            $id     = $iten['id'];
            $qty    = $iten['qty'];

            $product = $this->product->where(['id' => $id, 'active' => 1])->first();
            if (!$product) continue;

            if(in_array($id, $prdsCupom)){

                $value = $product->value;

                if ($product->use_price_promo == 1 && $qty >= $product->qty_price_promo) {
                    $value = $product->price_promo;
                }

                $newValue = $value * ($percentage/100);
                $valueCupom += $newValue * $qty;
            }
        }

        return $valueCupom;
    }

    public function getValueFreteUnitario(Request $request)
    {
        $type = $request->type_frete;
        $address_id = $request->cep_frete;

        $cepClient = $this->getCepForAddressId($address_id);

        echo json_encode($this->getValueTotalOrderCheckout($cepClient, $type));
    }

    /**
     * Pagamento via boleto
     *
     * @param $dataForm
     * @param $order_id
     * @return array
     */
    private function paymentBillet($dataForm, $order_id)
    {
        // Data de vencimento
        $date_due = date('Y-m-d', strtotime("+3 days",strtotime(date('Y-m-d'))));

        // Dados pagamento
        $payment = $this->getDataPayment($dataForm, $order_id);

        // payment
        $payment["date_of_expiration"] = $date_due . "T23:59:59.000-04:00";
        $payment["payment_method_id"] = "bolbradesco";

        return $payment;
    }

    /**
     * Pagamento via cartão de crédito
     *
     * @param $dataForm
     * @param $order_id
     * @return array
     */
    private function paymentCard($dataForm, $order_id)
    {
        // Dados pagamento
        $payment = $this->getDataPayment($dataForm, $order_id);

        $payment["token"] = $dataForm['card']['token'];
        $payment["installments"] = (int)$dataForm['card']['installment'];
        $payment["payment_method_id"] = $dataForm['card']['paymentMethodId'];

        return $payment;
    }

    private function getDataPayment($dataForm, $order_id)
    {
        $payment = array();
        $items = array();

        $expName = explode(" ", $dataForm['dataClient']['senderName']);

        $fName = "";
        $lNameArr = [];

        foreach ($expName as $key => $name){
            if($key == 0){ $fName = $name; continue; }
            array_push($lNameArr, $name);
        }
        $lName = implode(" ", $lNameArr);


        foreach ($dataForm['dataProducts'] as $iten) {
            array_push($items, array(
                "id" => (int)$iten['itemId'],
                "title" => $iten['itemTitle'],
                "description" => $iten['itemDescription'],
                "picture_url" => $iten['itemImage'],
                "category_id" => "others", //CATEGORIA A QUAL O ITEM PERTENCE, LISTAGEM DISPONÍVEL EM: https://api.mercadopago.com/item_categories
                "quantity" => (int)$iten['itemQuantity'],
                "unit_price" => number_format($iten['itemAmount'],2, '.','')
            ));
        }

//        $payment["token"] = $dataForm['card']['token'];
//        $payment["installments"] = (int)$dataForm['card']['installment'];
        $payment["transaction_amount"] = (float)number_format($dataForm['total_order'],2, '.','');
        $payment["description"] = "{$this->company_name} - Pedido: {$order_id}";
        $payment["binary_mode"] = false; // SE DEFINIDO true DESLIGA PROCESSO DE ANÁLISE MANUAL DE RISCO, PODE REDUZIR APROVAÇÃO DAS VENDAS SE NÃO CALIBRADO PREVIAMENTE.
//        $payment["payment_method_id"] = $dataForm['card']['paymentMethodId'];
        $payment["payer"] = array(
            "type" => "customer",
            "first_name" => $fName,
            "last_name" => $lName,
            "email" => $dataForm['dataClient']['senderEmailSave'],
            "identification" => array(
                "number" => $dataForm['dataClient']['senderCPF'],
                "type" => "CPF"
            )
        );
        $payment["notification_url"] = route('mercadopago_notification');
        $payment["sponsor_id"] = null;
        $payment["binary_mode"] = false;
        $payment["external_reference"] = $order_id;
        $payment["statement_descriptor"] = $this->company_name;
        $payment["additional_info"] = array( // DADOS ESSENCIAIS PARA ANÁLISE ANTI-FRAUDE
            "items" => $items,
            "payer" => array(
                //"type" => "customer",
                "first_name" => $fName,
                "last_name" => $lName,
                "registration_date" => date('Y-m-d H:i:sP', strtotime($dataForm['dataClient']['senderCreatedDate'])),
                "phone" => array(
                    "area_code" => substr($dataForm['dataClient']['senderPhone'], 0, 2),
                    "number" => substr($dataForm['dataClient']['senderPhone'], 2)
                ),
                "address"  => array(
                    "zip_code" => $dataForm['dataAddress']->cep,
                    "street_name" => $dataForm['dataAddress']->address,
                    "street_number" => $dataForm['dataAddress']->number
                )
            ),
            "shipments" => array(
                "receiver_address" => array(
                    "street_name" => $dataForm['dataAddress']->address,
                    "street_number" => $dataForm['dataAddress']->number,
                    "zip_code" => $dataForm['dataAddress']->cep,
                    "city_name" => $dataForm['dataAddress']->city,
                    "state_name" => $dataForm['dataAddress']->state
                )
            )
        );

        if ($_SERVER['HTTP_HOST'] == "teste.pedrohenrique.net")
            $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];

        $payment["additional_info"]["ip_address"] = $_SERVER['REMOTE_ADDR']; //IP DE ONDE PARTIU A REQUISIÇÃO DE PAGAMENTO

//        $payment["coupon_code"] = $dataForm['cupom']['name'] == "" ? null : $dataForm['name'];
//        $payment["coupon_amount"] = $dataForm['cupom']['value'] == 0 ? null : $dataForm['value'];

//        $payment["order"] = array(
//            "type"  => "mercadopago",
//            "id"    => $order_id
//        );

        return $payment;
    }

    /**
     * Requisição para criar o pedido
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkoutSend(Request $request)
    {
        $installments=filter_input(INPUT_POST,'installments',FILTER_DEFAULT);
        $paymentMethodId=filter_input(INPUT_POST,'paymentMethodId',FILTER_DEFAULT);
        $issuer_id=filter_input(INPUT_POST,'issuer_id',FILTER_DEFAULT);
        $token=filter_input(INPUT_POST,'token',FILTER_DEFAULT);
        $totalOrderPost=filter_input(INPUT_POST,'totalValueOrder',FILTER_DEFAULT);

        /** Cria log */
        $log = [
            'name' => 'INICIOU COMPRA', 'description' => json_encode($request->all()), 'type' => 'proccess', 'class' => __CLASS__, 'method' => __FUNCTION__, 'user_id' => auth()->guard('client')->user()->id
        ];
        $this->log->createLog($log);

        $erros = array();

        // Verifica dados gerais (sem pagamento)
        if((int)$request->address == 0) array_push($erros, "Não foi encontrado um endereço para envio!");
        if($request->frete_envio != "PAC" && $request->frete_envio != "Sedex") array_push($erros, "Não foi encontrado uma forma de envio, selecione entre Sedex ou PAC");
        if($request->methodPayment != "card" && $request->methodPayment != "billet") array_push($erros, "Seleciona pelo menos uma forma de pagamento, selecione entre Cartão ou Boleto");

        // Verifica pagamentos
        if($request->methodPayment == "card"){
            if((int)$request->installments <= 0) array_push($erros, "Dados de pagamento está inválido, recarregue a página e tente novamente");
        }
        else if($request->methodPayment == "billet"){
            if(!$this->validateCPF($request->billetCpf)) array_push($erros, "CPF informado no boleto é inválido!");
        }

        // Se existir algum erro vai voltar pro checkout e mostrar para o usuário
        if(count($erros) > 0)
            return redirect()->route('user.order.checkout')
                             ->withErrors($erros);

        $totalOrder = 0;
        $totalItemsOrder = 0;
        $retornoMercadoPago = array();
        // Dados de endereço
        $cepClient = $this->getCepForAddressId((int)$request->address);

        $dataAddress = $this->getAddressForAddressId((int)$request->address);

        // Percorre carrinho e criar array com os itens
        $items = $this->getItemsCart();
        $arrProducts = array();
        foreach ($items as $iten){
            $id = $iten['id'];
            $qty = (int)$iten['qty'];

            $stock = $this->verifyStockProduct($id);
            $product = $this->product
                            ->leftJoin('images', 'products.id', '=', 'images.product_id')
                            ->where(
                                array(
                                    'images.primary' => 1,
                                    'products.id' => $id,
                                    'products.active' => 1
                                )
                            )
                            ->first();

            if (!$product) continue;

            if($stock < $qty){
                return redirect()->route('user.order.checkout')
                        ->withErrors(["O produto {$product->name} está com o estoque menor que o solicitado. Disponível: {$stock}."]);
            }

            $value = $product->value;

            if ($product->use_price_promo == 1 && $qty >= $product->qty_price_promo) {
                $value = $product->price_promo;
            }

            array_push($arrProducts, array(
                'itemId' => $id,
                'itemDescription' => $this->removeAcento($product->name),
                'itemAmount' => $value, //Valor unitário
                'itemQuantity' => $qty, // Quantidade de itens
                'itemTitle' => $this->removeAcento($product->name), // Título do item
                'itemImage' => $product->path ? asset("user/img/products/{$product['product_id']}/thumbnail_{$product['path']}") : "", // Arquivo da imagem
            ));
            $totalOrder += (float)$value * $qty;
            $totalItemsOrder += $qty;
        }

        // Dados do frete
        $dataFrete = $this->getValueFrete($cepClient, $totalOrder);
        $dataFreteSelected = array();
        foreach($dataFrete as $frete) {
            if ($request->frete_envio != $frete['name']) continue;
            $frete['price'] = (float)$this->formataValor($frete['price'], 'en');
            $frete['date']  = $this->formataData($frete['date'], 'en');
            $dataFreteSelected = $frete;
            $totalOrder += $frete['price'];
        }

        // Valor do cupom, caso se aplique nos itens do carrinho
        $valueCupom = $this->getCupomIten(isset($_SESSION['cupom']) ? $_SESSION['cupom'] : $request->cupom);
        $nameCupom = isset($_SESSION['cupom']) ? $_SESSION['cupom'] : $request->cupom;
        if($nameCupom == null) $nameCupom = "";

        if(!$valueCupom) $valueCupom = 0;
        else $valueCupom = number_format((float)$valueCupom * (-1), 2, ',', '.');
        $valueCupom = (float)$this->formataValor($valueCupom, 'en');
        $totalOrder += $valueCupom;

        if (number_format($totalOrderPost,2, '.','') != number_format($totalOrder,2, '.',''))
            return redirect()->route('user.order.checkout')
                ->withErrors(["Seu carrinho de compras sofreu alguma alteração, tente realizar a compra novamente!"]);

        $cpfSander = $request->methodPayment == "card" ? "" : $request->billetCpf;
        // Cria array com os dados do pagamento
        $dataClient = array(
            'senderName'        => auth()->guard('client')->user()->name, //Deve conter nome e sobrenome
            'senderPhone'       => auth()->guard('client')->user()->tel, //Código de área enviado junto com o telefone
//            'senderEmail'       => "clientes@website.com.br",
            'senderEmailSave'   => auth()->guard('client')->user()->email,
//            'senderHash'        => $request->senderHash,
            'senderCPF'         => filter_var(preg_replace('~[\\\\/.-]~', '', $cpfSander), FILTER_SANITIZE_NUMBER_INT), //Ou CNPJ se for Pessoa Júridica
            'senderCreatedDate' => auth()->guard('client')->user()->created_at
        );
        // Criar array para enviar
        $arrSend = array(
            'methodPayment'     => $request->methodPayment,
            'dataClient'        => $dataClient,
            'dataAddress'       => $dataAddress,
            'dataFreteSelected' => $dataFreteSelected,
            'dataProducts'      => $arrProducts,
            'cupom'             => ['value' => $valueCupom, 'name' => $nameCupom],
            'totalItems'        => $totalItemsOrder,
            'total_order'       => $totalOrder,
        );

        if($request->methodPayment == "card") {
            $arrSend['card'] = array(
                'token'             =>  $token,
                'paymentMethodId'   =>  $paymentMethodId,
                'installment'       =>  $installments,
                'issuer_id'         =>  $issuer_id,
            );
        }

        DB::beginTransaction();// Iniciando transação manual para evitar updates/inserts não desejáveis

        // Cria pedido
        $order_id = $this->order->createNewOrder($arrSend);
        // Cria item
        $this->order_items->createNewIten($arrSend, $order_id);
        // Cria forma de pagamento
        $this->order_payment->createNewPayment($arrSend, $order_id);
        // Cria dados endereço
        $this->order_address->createNewAddress($arrSend, $order_id);

        /** Cria log */
        $log = [
            'name' => 'VALIDOU COMPRA, ID: '.$order_id, 'description' => json_encode($arrSend), 'type' => 'proccess', 'class' => __CLASS__, 'method' => __FUNCTION__, 'user_id' => auth()->guard('client')->user()->id
        ];
        $this->log->createLog($log);

        try {
            if($request->methodPayment == "card")
                $payment = $this->paymentCard($arrSend, $order_id);
            else if($request->methodPayment == "billet")
                $payment = $this->paymentBillet($arrSend, $order_id);

            /** Cria log */
            $log = [
                'name' => 'PAYLOAD MERCADOPAGO ID: '.$order_id,
                'description' => json_encode($payment),
                'type' => 'process',
                'class' => __CLASS__,
                'method' => __FUNCTION__,
                'user_id' => auth()->guard('client')->user()->id
            ];
            $this->log->createLog($log);

            // define para valor float, quando usado json_encode, não ficar como 99.90000000000000568434188608080
            ini_set( 'serialize_precision', -1 );
            //dd(json_encode($payment));

            $urlPayment = "https://api.mercadopago.com/v1/payments?access_token=" . PROD_TOKEN;
            $returnPaymentAll = $this->restApiMercadoPago($urlPayment, $payment);

            if ($returnPaymentAll['httpcode'] != 201 || !isset($returnPaymentAll['content'])) {
                $retornoMercadoPago = false;
            } else {
                $retornoMercadoPago = json_decode($returnPaymentAll['content']);
            }

        } catch (\Exception $e) {

            DB::rollback();
            /** Cria log */
            $log = [
                'name' => 'ERRO NA REQUISIÇÃO AO MERCADO PAGO: '.$order_id, 'description' => $e->getMessage(), 'type' => 'error', 'class' => __CLASS__, 'method' => __FUNCTION__, 'user_id' => auth()->guard('client')->user()->id
            ];
            $this->log->createLog($log);

            $msgError = $e->getMessage();

            return redirect()->route('user.order.checkout')
                ->withErrors(["Code: {$e->getCode()} - {$msgError}"]);
        }

        if (!$retornoMercadoPago) {
            DB::rollback();
            /** Cria log */
            $log = [
                'name' => 'Erro interno, ID: '.$order_id, 'description' => "Ocorreu um problema interno: " . json_encode($returnPaymentAll). ". Enviado=". json_encode($payment), 'type' => 'error', 'class' => __CLASS__, 'method' => __FUNCTION__, 'user_id' => auth()->guard('client')->user()->id
            ];
            $this->log->createLog($log);

            return redirect()->route('user.order.checkout')
                ->withErrors(["Ocorreu um problema interno, tente realizar o pagamento novamente em alguns minutos!"]);
        }

        $this->expMp->setPayment($retornoMercadoPago);
        $verify = $this->expMp->verifyTransaction();
        if($verify['class'] == 'error'){

            DB::rollback();
            /** Cria log */
            $log = [
                'name' => 'RETORNO MERCADOPAGO, ID: '.$order_id, 'description' => "Pagamento com erro: " . $verify['message'] . " - retorno=" . json_encode($returnPaymentAll) . 'enviado='.json_encode($payment), 'type' => 'error', 'class' => __CLASS__, 'method' => __FUNCTION__, 'user_id' => auth()->guard('client')->user()->id
            ];
            $this->log->createLog($log);

            return redirect()->route('user.order.checkout')
                ->withErrors([$verify['message']]);
        }

        $_SESSION['message_checkout_user'] = $verify['message'];

        /** Cria log */
        $log = [
            'name' => 'COMPRA REALIZADA: '.$order_id, 'description' => 'recebido='.json_encode($returnPaymentAll) . ' enviado='.json_encode($payment), 'type' => 'success', 'class' => __CLASS__, 'method' => __FUNCTION__, 'user_id' => auth()->guard('client')->user()->id
        ];
        $this->log->createLog($log);

        $this->order->updateNewOrder($retornoMercadoPago, $order_id);
        $this->order_payment->updateNewPayment($retornoMercadoPago, $order_id);
        $this->order_status->createNewStatus($retornoMercadoPago, $order_id);

        DB::commit();
        $this->removeStockAfterOrder();
        $this->clearCart();
        $this->sendMail->newOrder($order_id);

        return redirect()->route('user.order.checkout.created', [ 'id' => $order_id ]);
    }

    /**
     * Visualiza pos-checkout com detalhes do pedido realizado
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function orderCreated(int $id)
    {
        $amount_products = 0;

        $userId = auth()->guard('client')->user()->id;
        $dataOrder = $this->order
                        ->join('order_addresses', 'orders.id', '=', 'order_addresses.order_id')
                        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                        ->join('order_payments', 'orders.id', '=', 'order_payments.order_id')
                        ->where(['orders.id' => $id, 'orders.user_id' => $userId, 'order_addresses.user_id' => $userId, 'order_items.user_id' => $userId, 'order_payments.user_id' => $userId])
                        ->get();
        if(count($dataOrder) == 0)
            return redirect()->route('user.account.orders');

        if(strtotime('-1 hours', strtotime(date('Y-m-d H:i:s'))) > strtotime($dataOrder[0]->created_at))
            return redirect()->route('user.account.orders');

        foreach ($dataOrder as $key => $iten){
            $prd = $this->image->where(['product_id' => $iten['product_id'], 'primary' => 1])->first();
            $amount_products += (float)$iten['total_iten'];
            $dataOrder[$key]['image_prd'] = $iten['product_id'].'/'.$prd->path;
        }

        if(!isset($_SESSION['message_checkout_user'])) $_SESSION['message_checkout_user'] = "";
        $message_checkout_user = $_SESSION['message_checkout_user'];
        unset($_SESSION['message_checkout_user']);

        return view('user.order.order_created', compact('dataOrder', 'amount_products', 'message_checkout_user'));
    }

    /**
     * Limpa carrinho de compras
     * @return NULL
     */
    private function clearCart()
    {
        $userId = auth()->guard('client')->user()->id;
        $this->cart->where('user_id', $userId)->delete();
    }

    /**
     * Remove estoque dos produtos após a compra
     * @return NULL
     */
    private function removeStockAfterOrder()
    {
        // Percorre carrinho e criar array com os itens
        $items = $this->getItemsCart();
        foreach ($items as $iten){
            $id = $iten['id'];
            $qty = (int)$iten['qty'];

            // get data product
            $dataProduct = $this->product->where(['id' => $id, 'active' => 1])->first();
            if (!$dataProduct) continue;

            $qtyStock    = (int)$dataProduct->stock;
            $stockUpdate = $qtyStock - $qty;

            // update stock
            $this->product->where('id', $id)->update(['stock' => $stockUpdate]);
        }
    }

    /**
     * Consulta o estoque real do produto
     *
     * @param $product_id
     * @return int
     */
    private function verifyStockProduct($product_id)
    {
        $product = $this->product->where(['id' => $product_id, 'active' => 1]);
        if($product->count() == 0) return 0;

        return $product->first()->stock;
    }

    /**
     * Marca pedido como recebido
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function received(Request $request)
    {
        $userId = auth()->guard('client')->user()->id;
        $order_id = (int)$request->order_id;
        $order = $this->order->where(['id' => $order_id, 'user_id' => $userId]);

        if($order->count() == 0) return redirect()->route('user.account.orders');

        if ($this->order_status->getStatusByOrder($order_id, [54]) != 0) return redirect()->back();

        $order->update(['date_received' => date('Y-m-d H:i:s')]);
        $this->order_status->updateStatus([
            'order_id'          => $order_id,
            'code'              => "",
            'status'            => 54,
            'reference_order_id'=> $order_id,
            'date'              => date('Y-m-d H:i:s')
        ]);
        $this->sendMail->updateOrder((int)$order_id, 54);

        return redirect()->route('user.account.order', ['id' => $order_id]);
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

    private function restApiMercadoPago($url, $payload)
    {
        $paymentJson = json_encode($payload);

        $options = array(
            CURLOPT_RETURNTRANSFER  => true,     // return web page
            CURLOPT_HEADER          => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION  => true,     // follow redirects
            CURLOPT_ENCODING        => '',       // handle all encodings
            CURLOPT_POST		    => true,
            CURLOPT_POSTFIELDS	    => $paymentJson,
            CURLOPT_SSL_VERIFYPEER  => false     // Disabled SSL Cert checks
        );
        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );
        $header['httpcode']   = $httpcode;
        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }

    public function searchAdrress(Request $request)
    {
        $address = (int)$request->address;

        if($address == 0 || !auth()->guard('client')->user()){
            echo json_encode(false);
            exit();
        }

        $userId   = auth()->guard('client')->user()->id;

        $dataAddress = $this->address->where(['user_id' => $userId, 'id' => $address]);

        if($dataAddress->count() == 0){
            echo json_encode(false);
            exit();
        }

        $arrAddress = $dataAddress->first();

        $totalOrder = 0;
        foreach ($this->getItemsCart() as $iten){
            $id = $iten['id'];
            $qty = (int)$iten['qty'];

            $product = $this->product
                ->leftJoin('images', 'products.id', '=', 'images.product_id')
                ->where(
                    array(
                        'images.primary' => 1,
                        'products.id' => $id,
                        'products.active' => 1
                    )
                )
                ->first();

            if (!$product) continue;

            $value = $product->value;
            if ($product->use_price_promo == 1 && $qty >= $product->qty_price_promo)
                $value = $product->price_promo;

            $totalOrder += (float)$value * $qty;
        }

        echo json_encode(
            array(
                'data_address' =>
                    array(
                        'address'       => $arrAddress->address,
                        'number'        => $arrAddress->number,
                        'cep'           => $this->formataCep($arrAddress->cep),
                        'complement'    => $arrAddress->complement,
                        'reference'     => $arrAddress->reference,
                        'neighborhood'  => $arrAddress->neighborhood,
                        'city'          => $arrAddress->city,
                        'state'         => $arrAddress->state
                    ),
                'fretes' => $this->getValueFrete($arrAddress->cep, $totalOrder)
            )
        );
    }

}
