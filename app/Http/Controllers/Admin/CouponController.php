<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Product;

class CouponController extends Controller
{
    private $coupon;
    private $product;

    public function __construct(Coupon $coupon, Product $product)
    {
        $this->coupon   = $coupon;
        $this->product  = $product;
    }

    public function list()
    {
        $coupons = $this->coupon->getAllCoupons();
        $arrCoupons = array();

        foreach ($coupons as $coupon) {
            array_push($arrCoupons, [
                "id"            => $coupon["id"],
                "name"          => $coupon["name_coupon"],
                'date_expired'  => $coupon["date_expired"] ? date('d/m/Y', strtotime($coupon["date_expired"])) : 'Não Informado',
                'percentage'    => number_format($coupon["percentage"], 2, ',', '.'),
                'created_at'    => $coupon["created_at"] ? date('d/m/Y H:i', strtotime($coupon["created_at"])) : 'Não Informado',
                'datetime_order'=> $coupon["created_at"] ? strtotime($coupon["created_at"]) : 0,
            ]);
        }

        return view('admin.coupon.index', compact('arrCoupons'));
    }

    public function edit(int $id)
    {
        $coupon = $this->coupon->getCoupon($id);
        if(!$coupon) return redirect()->route('admin.coupon');

        $products_coupon = json_decode($coupon['products_id']);
        $products_register = array();

        foreach ( $this->product->getProducts() as $product) {
            array_push($products_register, [
                'id'    => $product['id'],
                'name'  => $product['name'],
                'active'=> in_array($product['id'], $products_coupon)
            ]);
        }

        $dataCoupon = [
            "id"            => $coupon["id"],
            "name"          => $coupon["name_coupon"],
            'date_expired'  => $coupon["date_expired"],
            'percentage'    => $coupon["percentage"],
            'created_at'    => $coupon["created_at"] ? date('d/m/Y H:i', strtotime($coupon["created_at"])) : 'Não Informado',
        ];

        return view('admin.coupon.edit', compact('dataCoupon', 'products_register'));
    }

    public function new()
    {

        $products_register = array();

        foreach ( $this->product->getProducts() as $product) {
            array_push($products_register, [
                'id'    => $product['id'],
                'name'  => $product['name']
            ]);
        }

        return view('admin.coupon.new', compact('products_register'));
    }

    public function update(Request $request)
    {
        $id         = filter_var($request->coupon_id, FILTER_VALIDATE_INT);
        $name       = filter_var($request->name, FILTER_SANITIZE_STRING);
        $date_exp   = $request->date_exp;
        $percentage = $this->formataValor($request->percentage, 'en');
        $coupons    = (array)$request->coupon;
        $arrCoupons = array();

        foreach ($coupons as $coupon)
            if($this->product->getProducts($coupon)) array_push($arrCoupons, (int)$coupon);

        $dataUpdate = array(
            'name_coupon'   => strtoupper($name),
            'date_expired'  => $date_exp,
            'percentage'    => $percentage,
            'products_id'   => json_encode($arrCoupons)
        );

        $update = $this->coupon->edit($dataUpdate, $id);


        if($update)
            return redirect()->route('admin.coupons')
                ->with('success', 'Cupom alterado com sucesso!');

        return redirect()->route('admin.coupon.edit', ['id' => $id])
                ->withErrors(['Não foi possível alterar o cupom, tente novamente'])
                ->withInput();
    }

    public function insert(Request $request)
    {
        $name       = filter_var($request->name, FILTER_SANITIZE_STRING);
        $date_exp   = $request->date_exp;
        $percentage = $this->formataValor($request->percentage, 'en');
        $coupons    = (array)$request->coupon;
        $arrCoupons = array();

        foreach ($coupons as $coupon)
            if($this->product->getProducts($coupon)) array_push($arrCoupons, (int)$coupon);

        $dataInsert = array(
            'name_coupon'   => strtoupper($name),
            'date_expired'  => $date_exp,
            'percentage'    => $percentage,
            'products_id'   => json_encode($arrCoupons)
        );

        $insert = $this->coupon->insert($dataInsert);


        if($insert)
            return redirect()->route('admin.coupons')
                ->with('success', 'Cupom cadastrado com sucesso!');

        return redirect()->route('admin.coupon.new')
                ->with('warning', 'Não foi possível cadastrar o cupom, tente novamente');
    }

    public function remove(Request $request)
    {
        $coupon_id = $request->coupon_id;
        $coupon = $this->coupon->getCoupon($coupon_id);
        if(!$coupon)return redirect()->route('admin.coupons')
            ->with('warning', 'Cupom não encontrado!');

        $delete = $this->coupon->remove(($coupon_id));

        if($delete)
            return redirect()->route('admin.coupons')
                ->with('success', 'Cupom excluído com sucesso!');

        return redirect()->route('admin.coupon')
            ->withErrors(['Não foi possível excluir o cupom, tente novamente']);
    }
}
