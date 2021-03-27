<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use FlyingLuscas\Correios\Service;
use Illuminate\Http\Request;
use App\Models\Address;
use FlyingLuscas\Correios\Services\Freight;
use GuzzleHttp\Client as HttpClient;
use App\Cart;
use App\Models\Product;
use App\Admin;
use App\Models\LogHistory;
use App\Models\Promotion;

class AddressController extends Controller
{
    private $address;
    private $http;
    private $freight;
    private $cart;
    private $product;
    private $admin;
    private $log;
    private $promotion;
    private $cross_docking;

    public function __construct(Address $address, HttpClient $http, Cart $cart, Product $product, Admin $admin, LogHistory $log, Promotion $promotion)
    {
        $this->address  = $address;
        $this->http     = $http;
        $this->freight  = new Freight($this->http);
        $this->cart     = $cart;
        $this->product  = $product;
        $this->admin    = $admin;
        $this->log      = $log;
        $this->promotion= $promotion;

        $dataAdmin              = $admin->getAdminMain();
        $this->cross_docking    = (int)$dataAdmin->order_submission_limit;
    }
//
//    public function searchAdrress(Request $request)
//    {
//        $address = (int)$request->address;
//
//        if($address == 0 || !auth()->guard('client')->user()){
//            echo json_encode(false);
//            exit();
//        }
//
//        $userId   = auth()->guard('client')->user()->id;
//
//        $dataAddress = $this->address->where(['user_id' => $userId, 'id' => $address]);
//
//        if($dataAddress->count() == 0){
//            echo json_encode(false);
//            exit();
//        }
//
//        $arrAddress = $dataAddress->first();
//
//        echo json_encode(
//            array(
//                'data_address' =>
//                    array(
//                        'address'       => $arrAddress->address,
//                        'number'        => $arrAddress->number,
//                        'cep'           => $this->formataCep($arrAddress->cep),
//                        'complement'    => $arrAddress->complement,
//                        'reference'     => $arrAddress->reference,
//                        'neighborhood'  => $arrAddress->neighborhood,
//                        'city'          => $arrAddress->city,
//                        'state'         => $arrAddress->state
//                    ),
//                'fretes' => $this->getValueFrete($arrAddress->cep)
//            )
//        );
//    }

    public function updateAjax(Request $request)
    {
        $userId   = auth()->guard('client')->user()->id;

        $validator = validator(
            $request->all(),
            [
                'cep_update'            => 'required|min:8',
                'address_update'        => 'required',
                'neighborhood_update'   => 'required',
                'city_update'           => 'required',
                'state_update'          => 'required',
                'number_update'         => 'required',
                'address_id'            => 'required'
            ],
            [
                'cep_update.required'          => 'O CEP é um campo obrigatório!',
                'cep_update.min'               => 'O CEP precisa estar digitada corretamente!',
                'address_update.required'      => 'O endereço é um campo obrigatório!',
                'neighborhood_update.required' => 'O bairro é um campo obrigatório!',
                'city_update.required'         => 'A cidade é um campo obrigatório!',
                'state_update.required'        => 'O estado é um campo obrigatório!',
                'number_update.required'       => 'O número é um campo obrigatório!',
                'address_id.required'           => 'Ocorreu um problema, feche a alteração e tente novamente!'
            ]
        );

        if($validator->fails()) {
            echo json_encode(array('success' => false, 'data' => $validator->errors()));
            exit();
        }

        $dataAddressUpdate = [
            'address'       => $request->address_update,
            'cep'           => filter_var(preg_replace('~[.-]~', '', $request->cep_update), FILTER_SANITIZE_NUMBER_INT),
            'number'        => $request->number_update,
            'complement'    => $request->complement_update ?? '',
            'reference'     => $request->reference_update ?? '',
            'neighborhood'  => $request->neighborhood_update,
            'city'          => $request->city_update,
            'state'         => $request->state_update
        ];

        $update = $this->address->where('id', $request->address_id)->update($dataAddressUpdate);

        if($update){
            echo json_encode(array('success' => true, 'data' => 'Endereço alterado com sucesso!'));
            exit();
        }

        echo json_encode(array('success' => false, 'data' => 'Não foi possível alterar o endereço!'));
    }

    public function insertAjax(Request $request)
    {
        $userId   = auth()->guard('client')->user()->id;

        $validator = validator(
            $request->all(),
            [
                'cep'           => 'required|min:8',
                'number'        => 'required',
                'address'       => 'required',
                'neighborhood'  => 'required',
                'city'          => 'required',
                'state'         => 'required',
            ],
            [
                'cep.required'          => 'O CEP é um campo obrigatório!',
                'cep.min'               => 'O CEP precisa estar digitada corretamente!',
                'address.required'      => 'O endereço é um campo obrigatório!',
                'neighborhood.required' => 'O bairro é um campo obrigatório!',
                'city.required'         => 'A cidade é um campo obrigatório!',
                'state.required'        => 'O estado é um campo obrigatório!',
                'number.required'       => 'O número é um campo obrigatório!'
            ]
        );

        if($validator->fails()) {
            echo json_encode(array('success' => false, 'data' => $validator->errors()));
            exit();
        }

        $dataAddressCreate = [
            'address'       => $request->address,
            'cep'           => filter_var(preg_replace('~[.-]~', '', $request->cep), FILTER_SANITIZE_NUMBER_INT),
            'number'        => $request->number,
            'complement'    => $request->complement ?? '',
            'reference'     => $request->reference ?? '',
            'neighborhood'  => $request->neighborhood,
            'city'          => $request->city,
            'state'         => $request->state,
            'user_id'       => $userId,
        ];

        if ($this->address->checkAddressExist($dataAddressCreate)) {
            echo json_encode(array('success' => false, 'data' => 'Este endereço já está cadastrado!'));
            exit();
        }

        $this->address->where('user_id', $userId)->update(['default' => 0]);

        $insert = $this->address->create($dataAddressCreate);

        if($insert) {
            echo json_encode(array('success' => true, 'data' => 'Endereço cadastrado com sucesso!'));
            exit();
        }


        echo json_encode(array('success' => false, 'data' => array('Não foi possível cadastrar o endereço')));
    }

    public function getArrayAddress($userId)
    {
        $dataAddress = array();
        $arrAddress = $this->address->where('user_id', $userId)->get();

        foreach ($arrAddress as $address){
            array_push($dataAddress, array(
                'id'            => $address['id'],
                'address'       => $address['address'],
                'number'        => $address['number'],
                'cep'           => $address['cep'],
                'complement'    => $address['complement'],
                'reference'     => $address['reference'],
                'neighborhood'  => $address['neighborhood'],
                'city'          => $address['city'],
                'state'         => $address['state'],
                'default'       => $address['default']
            ));
        }
        return $dataAddress;
    }

    public function getAddressAjax()
    {
        $userId      = auth()->guard('client')->user()->id;
        $dataAddress = $this->getArrayAddress($userId);

        echo json_encode($dataAddress);
    }


    private function getValueFrete($cepCliente, $totalOrder)
    {
        // comprimento 15 - 104
        // largura 10 - 104
        // altura 1 - 104
        // comprimento + largura + altura menor que 199
        // diametro 4 - 90
        // soma comprimento + (2 * diametro) menos que 199

        $prds_id = $this->getItemsCart();
        $cepCliente = $cepCliente === null ? 0 : filter_var(preg_replace('~[.-]~', '', $cepCliente), FILTER_SANITIZE_NUMBER_INT);

        if(count($prds_id) === 0 || strlen($cepCliente) != 8) return false;

        $admin = $this->admin->getAdminMain();

        $cepEmpresa = $admin->cep;
        $results    = array();

        $regLog = [];

        foreach ($prds_id as $iten){

            $code           = (int)$iten['id'];
            $qty_cart       = (int)$iten['qty'];

            $product        = $this->product->where(['id' => $code, 'active' => 1])->first();
            if (!$product) continue;

            $width_cart     = (float)$product->width;
            $height_cart    = (float)$product->height;
            $depth_cart     = (float)$product->depth;
            $weight_cart    = (float)number_format((float)$product->weight, 2);

            array_push($regLog, array(
                $width_cart,
                $height_cart,
                $depth_cart,
                $weight_cart,
                $qty_cart
            ));
        }

        $width_sum  = 0;
        $height_sum = 0;
        $depth_sum  = 0;
        $weight_sum = 0;
        $qty_sum    = 0;
        $send       = false;
        $arrKit     = array();
        $arrValores = array();
        $countKit   = 0;


        foreach ($regLog as $key => $iten) {

            $qty_sum    += $iten[4];
            $width_sum  += $iten[0]*$qty_sum;
            $height_sum += $iten[1]*$qty_sum;
            $depth_sum  += $iten[2]*$qty_sum;
            $weight_sum += $iten[3]*$qty_sum;

            if($width_sum > 105) $send = true;
            if($depth_sum > 105) $send = true;
            if($height_sum > 105) $send = true;
            if(($width_sum + $depth_sum) > 120) $send = true;
            if(($depth_sum + $width_sum + $height_sum) > 200) $send = true;

            if($send) {

                $qty_sum    = $iten[4];
                $width_sum  = $iten[0]*$qty_sum;
                $height_sum = $iten[1]*$qty_sum;
                $depth_sum  = $iten[2]*$qty_sum;
                $weight_sum = $iten[3]*$qty_sum;

                $countKit++;

                if(!key_exists($countKit, $arrKit)) $arrKit[$countKit] = array();

                array_push($arrKit[$countKit], [
                    $iten[0],
                    $iten[1],
                    $iten[2],
                    $iten[3],
                    $iten[4]
                ]);

                $send = false;

            } else {
                if(!key_exists($countKit, $arrKit)) $arrKit[$countKit] = array();

                array_push($arrKit[$countKit], [
                    $iten[0],
                    $iten[1],
                    $iten[2],
                    $iten[3],
                    $iten[4]
                ]);
            }
        }

        foreach ($arrKit as $kit) {

            $freight = new Freight($this->http);

            $freight->origin($cepEmpresa)
                ->destination($cepCliente)
                ->services(Service::SEDEX, Service::PAC);

            foreach ($kit as $iten ) {
                $qty    = $iten[4];
                $height = $iten[1]*$qty;
//                $weight = $iten[3]*$qty;
//                $width  = $iten[0]*$qty;
//                $depth  = $iten[2]*$qty;

                $width_send  = $iten[0];
                $height_send = $iten[1];
                $depth_send  = $iten[2];
                $weight_send = $iten[3];

                if ($width_send > 105) $width_send = 105;
                if ($depth_send > 105) $depth_send = 105;
                if ($height > 105) $height_send = 105;
                if ($width_send + $depth_send > 120) {
//                    $depth_send = 60;
//                    $width_send = 60;
                }
                if ($depth_send + $width_send + $height > 200) {
//                    $depth_send = 65;
//                    $width_send = 65;
//                    $height_send = 70;
                }
//                if ($depth_send + $width_send + $height < 29) {
//                    $depth_send = 16;
//                    $width_send = 11;
//                    $height_send = 2;
//                }
//                if ($height < 2) $height_send = 2;
//                if ($depth_send < 16) $depth_send = 16;
//                if ($width_send < 11) $width_send = 11;
//
//                if ($height_send < 2) $height_send = 2;
//                if ($depth_send < 16) $depth_send = 16;
//                if ($width_send < 11) $width_send = 11;

                $freight->item($width_send, $height_send, $depth_send, $weight_send, $qty); // largura, altura, comprimento, peso e quantidade
            }
            array_push($arrValores, $freight->calculate());
        }

        $results[0]['name']  = "";
        $results[0]['price'] = 0;
        $results[0]['date']  = "";
        $results[1]['name']  = "";
        $results[1]['price'] = 0;
        $results[1]['date']  = "";

        foreach ($arrValores as $fretes) {

//            if (isset($fretes['message'])) {
//                return array('errors' => array($fretes['message']));
//            }
//
//            if (isset($fretes[0]['error']) && isset($fretes[1]['error'])) {
//                if (count($fretes[0]['error']) != 0 || count($fretes[1]['error']) != 0) {
//                    $results = [
//                        'errors' => [
//                            $fretes[0]['error'],
//                            $fretes[1]['error']
//                        ]
//                    ];
//                    return $results;
//                }
//            }

            foreach ($fretes as $key => $frete) {

                $dias = $frete['deadline'] + $this->cross_docking;

                $results[$key]['name'] = $frete['name'];
                $results[$key]['price'] += $frete['price'];
                $results[$key]['date'] = date('d/m/Y', strtotime("+{$dias} days", strtotime(date('Y-m-d'))));
            }

        }
        $promotionFrete_FreteMenorXReais    = $this->getValuePromotionFrete_FreteMenorXReais();
        $promotionFrete_PedidoMaiorXReais   = $this->getValuePromotionFrete_PedidoMaiorXReais();
        foreach ($results as $key => $result){

            $result['price'] = $promotionFrete_FreteMenorXReais ? $promotionFrete_FreteMenorXReais >= $result['price'] ? 0 : $result['price'] : $result['price'];
            $result['price'] = $promotionFrete_PedidoMaiorXReais && $result['name'] == 'PAC' ? $totalOrder >= $promotionFrete_PedidoMaiorXReais ? 0 : $result['price'] : $result['price'];

            $results[$key]['price'] = number_format($result['price'], 2, ',', '.');
        }
        return $results;
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

    public function address()
    {
        $userId   = auth()->guard('client')->user()->id;
        $arrAddress = array();

        foreach($this->address->where('user_id', $userId)->get() as $address){
            array_push($arrAddress, array(
                'id'            => $address['id'],
                'address'       => $address['address'],
                'cep'           => preg_replace("/([0-9]{2})([0-9]{3})([0-9]{3})/", "$1.$2-$3", $address['cep']),
                'number'        => $address['number'],
                'complement'    => $address['complement'],
                'reference'     => $address['reference'],
                'neighborhood'  => $address['neighborhood'],
                'city'          => $address['city'],
                'state'         => $address['state'],
                'default'       => $address['default']
            ));
        }


        return view('user.account.address', compact('arrAddress'));
    }

    public function addressPost(Request $request)
    {
        $userId   = auth()->guard('client')->user()->id;

        $validator = validator(
            $request->all(),
            [
                'cep'           => 'required|min:9',
                'address'       => 'required',
                'neighborhood'  => 'required',
                'city'          => 'required',
                'state'         => 'required',
                'number'        => 'required'
            ],
            [
                'cep.required'          => 'O CEP é um campo obrigatório!',
                'cep.min'               => 'O CEP precisa estar digitada corretamente!',
                'address.required'      => 'O endereço é um campo obrigatório!',
                'neighborhood.required' => 'O bairro é um campo obrigatório!',
                'city.required'         => 'A cidade é um campo obrigatório!',
                'state.required'        => 'O estado é um campo obrigatório!',
                'number.required'       => 'O número é um campo obrigatório!'
            ]
        );

        if($validator->fails())
            return redirect()->route('user.account.address')->withErrors($validator)->withInput();

        $dataAddressCreate = [
            'address'       => $request->address,
            'cep'           => filter_var(preg_replace('~[.-]~', '', $request->cep), FILTER_SANITIZE_NUMBER_INT),
            'number'        => $request->number,
            'complement'    => $request->complement ?? '',
            'reference'     => $request->reference ?? '',
            'neighborhood'  => $request->neighborhood,
            'city'          => $request->city,
            'state'         => $request->state,
            'user_id'       => $userId,
        ];

        if ($this->address->checkAddressExist($dataAddressCreate))
            return redirect()->route('user.account.address')->withErrors(['Este endereço já está cadastrado!'])->withInput();

        $update = $this->address->where('user_id', $userId)->update(['default' => 0]);

        $insert = $this->address->create($dataAddressCreate);

        if($insert)
            return redirect()->route('user.account.address')
                ->with('success', 'Endereço cadastrado com sucesso!');


        return redirect()->route('user.account.address')
            ->withErrors(['Não foi possível cadastrar o endereço']);
    }

    public function addressEdit(Request $request)
    {
        $userId   = auth()->guard('client')->user()->id;

        $validator = validator(
            $request->all(),
            [
                'cep_update'           => 'required|min:9',
                'address_update'       => 'required',
                'neighborhood_update'  => 'required',
                'city_update'          => 'required',
                'state_update'         => 'required',
                'number_update'        => 'required',
                'address_id'            => 'required'
            ],
            [
                'cep_update.required'          => 'O CEP é um campo obrigatório!',
                'cep_update.min'               => 'O CEP precisa estar digitada corretamente!',
                'address_update.required'      => 'O endereço é um campo obrigatório!',
                'neighborhood_update.required' => 'O bairro é um campo obrigatório!',
                'city_update.required'         => 'A cidade é um campo obrigatório!',
                'state_update.required'        => 'O estado é um campo obrigatório!',
                'number_update.required'       => 'O número é um campo obrigatório!',
                'address_id.required'           => 'Ocorreu um problema, recarregue a página e tente novamente!'
            ]
        );

        if($validator->fails())
            return redirect()->route('user.account.address')->withErrors($validator)->withInput();

        $dataAddressUpdate = [
            'address'       => $request->address_update,
            'cep'           => filter_var(preg_replace('~[.-]~', '', $request->cep_update), FILTER_SANITIZE_NUMBER_INT),
            'number'        => $request->number_update,
            'complement'    => $request->complement_update ?? '',
            'reference'     => $request->reference_update ?? '',
            'neighborhood'  => $request->neighborhood_update,
            'city'          => $request->city_update,
            'state'         => $request->state_update
        ];

        $update = $this->address->where('id', $request->address_id)->update($dataAddressUpdate);

        if($update)
            return redirect()->route('user.account.address')
                ->with('success', 'Endereço alterado com sucesso!');


        return redirect()->route('user.account.address')
            ->with('warning', 'Não foi possível alterar o endereço');
    }

    public function addressDelete(Request $request)
    {

        $userId   = auth()->guard('client')->user()->id;
        $addressId = $request->address_id;

        $dataAddress = $this->address->where('id', $addressId)->get();

        if($dataAddress[0]['default'] == 1)
            return redirect()->route('user.account.address')
                ->with('warning', 'Não é possível excluir o endereço padrão!');

        $delete = $this->address->where('id', $addressId)->delete();
        if($delete)
            return redirect()->route('user.account.address')
                ->with('success', 'Endereço excluído com sucesso!');


        return redirect()->route('user.account.address')
            ->with('warning', 'Não foi possível excluir o endereço');
    }

    public function addressDefault(Request $request)
    {
        $userId   = auth()->guard('client')->user()->id;
        $addressId = $request->address_id;

        $updateAllZero = $this->address->where('user_id', $userId)->update(['default' => 0]);
        $updateNewDefault = $this->address->where('id', $addressId)->update(['default' => 1]);

        if($updateAllZero && $updateNewDefault)
            return redirect()->route('user.account.address')
                ->with('success', 'Endereço definido como padrão com sucesso!');


        return redirect()->route('user.account.address')
            ->with('warning', 'Não foi possível alterar definição do endereço');
    }
}
