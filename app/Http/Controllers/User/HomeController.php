<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use App\Models\Testimony;
use App\Models\Banner;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    private $product;
    private $image;
    private $testimony;
    private $banner;

    public function __construct(Product $product, Image $image, Testimony $testimony, Banner $banner)
    {
        $this->product  = $product;
        $this->image    = $image;
        $this->testimony=$testimony;
        $this->banner   = $banner;
    }

    public function index()
    {
        $dataProducts = $this->product->getListProductsActive();
        $arrProduct = array();

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

        $testimonies = $this->testimony->where(['approved' => 1, 'primary' => 1])->orderBy('id', 'desc')->limit(10)->get();
        $qntTestimonies = $this->testimony->where('approved', 1)->orderBy('id', 'desc')->count();

        // Recupera os banner
        $banners = $this->banner->getBanners();
        $arrBanners = array();
        foreach ( $banners as $banner ) {
            array_push($arrBanners, array(
                'path' => asset("user/img/banner/{$banner['path']}")
            ));
        }

        return view('user.home.index', compact('arrProduct', 'testimonies', 'arrBanners', 'qntTestimonies'));
    }
}
