<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Rate;
use App\User;

class ProductController extends Controller
{
    private $product;
    private $rate;
    private $client;

    public function __construct(Product $product, Rate $rate, User $client)
    {

        $this->product  = $product;
        $this->rate     = $rate;
        $this->client   = $client;
    }

    public function product(int $id)
    {
        $dataProduct        = array('images' => [], 'product' => []);
        $arrProductRelated  = array();
        $imagePrimary       = "";

        $product = $this->product
                    ->join('images', 'products.id', '=', 'images.product_id')
                    ->where(['products.id' => $id, 'products.active' => 1])
                    ->orderBy('images.primary', 'desc');

        if($product->count() == 0) return redirect()->route('user.products');
        foreach($product->get() as $prd){
            if($prd['primary'] == 1) $imagePrimary = "{$prd['product_id']}/{$prd['path']}";
            array_push($dataProduct['images'], ['primary' => $prd['primary'], 'path' => "{$prd['product_id']}/{$prd['path']}"]);
        }

        $dataProduct['product'] = [
            "id"            => $prd["product_id"],
            "name"          => $prd["name"],
            "description"   => $prd["description"],
            "value"         => number_format($prd["value"], 2, ',', '.'),
            'parcel'        => $this->calcParcelaJuros($prd['value'], 3),
            'imagePrimary'  => $imagePrimary,
            'value_high'    => number_format($prd['value_high_discount'],2, ',', '.'),
            'use_value_high'=> $prd['value_high_discount'] != 0
        ];

        foreach($this->getProductRelated($prd["name"], $prd["product_id"]) as $prd_related){
            array_push($arrProductRelated, array(
                "id"            => $prd_related["product_id"],
                "name"          => $prd_related["name"],
                "value"         => number_format($prd_related["value"], 2, ',', '.'),
                'path'          => "{$prd_related['product_id']}/thumbnail_{$prd_related['path']}",
                'parcel'        => $this->calcParcelaJuros($prd_related['value'], 3),
                'value_high'    => number_format($prd_related['value_high_discount'],2, ',', '.'),
                'use_value_high'=> $prd_related['value_high_discount'] != 0
            ));
        }

        // Avaliações
        $arrRates = [];
        $rates = $this->rate->getRatesProduct($id);
        $totalRateValue = 0;
        $totalRatePercentage = 0;
        foreach ($rates as $rate) {
            $totalRateValue += (int)$rate['rate'];
            $nameUser = $rate['user_id'] != 0 ? $this->client->getClient($rate['user_id'])->name : $rate['name_user'];

            array_push($arrRates, [
                'title'      => $rate['title'],
                'description'=> $rate['description'],
                'rate'       => $rate['rate'],
                'nameUser'   => trim(substr($nameUser, 0, 15)),
                'created_at' => date('d/m/Y H:i', strtotime($rate['created_at']))
            ]);
        }
        if($totalRateValue != 0) {
            $totalRateValue /= count($rates);
            $totalRatePercentage = ($totalRateValue*100)/5;
        }

        $totalRate = [
            'rate_value'    => number_format($totalRateValue, 2),
            'rate_percentage'   => $totalRatePercentage,
            'rate_total'        => count($rates)
        ];

        return view('user.product.detail', compact('dataProduct', 'arrProductRelated', 'arrRates', 'totalRate'));
    }

    public function products(Request $request)
    {
        $priceStart     = null;
        $priceFinish    = null;
        $orderBy = 'products.value desc';

        if(isset($request->order)){
            switch ($request->order){
                case "price_min_max":
                    $orderBy = 'products.value asc';
                    break;
                case "name_a_z":
                    $orderBy = 'products.name asc';
                    break;
                case "name_z_a":
                    $orderBy = 'products.name desc';
                    break;
                case "price_max_min":
                default:
                    $orderBy = 'products.value desc';
                    break;
            }
        }

        $arrProduct = array();

        // Inicio da consulta
        $dataProducts = $this->product
            ->join('images', 'products.id', '=', 'images.product_id')
            ->where(['images.primary' => 1, 'products.active' => 1])
            ->orderByRaw($orderBy);

        // Filtro por nome
        if(isset($request->search)){
            $nameSearch = filter_var($request->search, FILTER_SANITIZE_STRING);
            $dataProducts = $dataProducts->where('products.name', 'like', '%' . $nameSearch . '%');
        }
        // Filtro por preço
        if(isset($request->priceStart) && isset($request->priceFinish)){
            $priceStart     = (float)$request->priceStart;
            $priceFinish    = (float)$request->priceFinish;

            $dataProducts = $dataProducts->whereBetween('products.value', array($priceStart, $priceFinish));
        }

        $dataProducts = $dataProducts->get();

        foreach($dataProducts as $product){
            array_push($arrProduct, array(
                'id'            => $product['product_id'],
                'name'          => $product['name'],
                'value'         => number_format($product['value'],2, ',', '.'),
                'value_parcel'  => $this->calcParcelaJuros($product['value'], 3),
                'path_image'    => "{$product['product_id']}/thumbnail_{$product['path']}",
                'value_high'    => number_format($product['value_high_discount'],2, ',', '.'),
                'use_value_high'=> $product['value_high_discount'] != 0
            ));
        }

        $valuesMinMax = $this->getPriceMinMaxProducts();

        $filter['price'] = array(
            'min' => $priceStart,
            'max' => $priceFinish
        );

        return view('user.product.index', compact('arrProduct', 'valuesMinMax', 'filter'));
    }

    private function getProductRelated($nameProduct, $idProduct, $limit = 4)
    {
        $countProducts  = 0;
        $arrProducts    = array();
        $arrIds         = array($idProduct);

        foreach (explode(" ", $nameProduct) as $name){
            $countDisponible = $limit - $countProducts;
            if($countDisponible <= 0) break;

            $productsGet = $this->product->getProductByName($name, $arrIds, $countDisponible);
            foreach ($productsGet as $prd) {
                array_push($arrIds, $prd['product_id']);
                array_push($arrProducts, $prd);
            }

            $countProducts += count($productsGet);
        }
        if(count($arrProducts) < $limit){
            $productsGet = $this->product->getProductByName("", $arrIds, $countDisponible);
            foreach ($productsGet as $product)
            if(count($productsGet) > 0)array_push($arrProducts, $product);
        }

        return $arrProducts;

    }

    public function getPriceMinMaxProducts()
    {
        $min = null;
        $max = null;

        foreach ($this->product->getProducts() as $product){
            if(!$min) $min = $product['value'];
            if(!$max) $max = $product['value'];

            if($min > $product['value']) $min = $product['value'];
            if($max < $product['value']) $max = $product['value'];
        }

        if(!$min) $min = 0;
        if(!$max) $max = 0;

        return array(
            'min' => $min,
            'max' => $max
        );
    }
}
