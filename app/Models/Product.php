<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'value', 'stock', 'active', 'alert_stock', 'variation', 'height', 'width', 'depth', 'weight'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public function getProductByName($name, $ids, $limit = 4)
    {
        return $this
                ->join('images', 'images.product_id', '=', 'products.id')
                ->where('products.name', 'like', "%{$name}%")
                ->where(['images.primary' => 1, 'products.active' => 1])
                ->whereNotIn('products.id', $ids)
                ->orderBy('products.value', 'DESC')
                ->limit($limit)
                ->get();
    }

    public function getProducts($product_id = null)
    {
        if($product_id) return $this->find($product_id);

        return $this->get();
    }

    public function getCountProducts()
    {
        return $this->count();
    }

    public function getNameProducts($cod)
    {
        return $this->where('id', $cod)->first();
    }

    public function updateStockProduct(int $product_id, int $qty)
    {
        $product = $this->select('stock')->where('id', $product_id)->first();

        $qty_product = (int)$product->stock + $qty;

        $this->where('id', $product_id)->update(['stock' => $qty_product]);
    }

    public function getProductsWithImage($product_id = null)
    {
        $product = $this
                    ->select(['products.*', 'images.path'])
                    ->join('images', 'products.id', '=', 'images.product_id')
                    ->where('images.primary', 1);

        if($product_id) return $product->where('products.id', $product_id)->first();

        return $product->orderBy('products.id', 'desc')->get();
    }

    public function deleteProduct($product_id)
    {
        $delete = $this->where('id', $product_id)->delete();
        DB::table('carts')->where('product_id', $product_id)->delete();
        DB::table('images')->where('product_id', $product_id)->delete();

        return $delete ? true : false;
    }

    public function getProductWithImages($product_id = null)
    {
        return $this
                ->select([
                    'products.*',
                    'images.id as image_id',
                    'images.path',
                    'images.primary'
                ])
                ->join('images', 'products.id', '=', 'images.product_id')
                ->where('products.id', $product_id)->get();

    }

    public function edit($dataForm)
    {
        // Cria array validado com nomes das colunas da tabela 'automoveis'
        $update = array(
            'name'          => filter_var($dataForm['name'], FILTER_SANITIZE_STRING),
            'value'         => filter_var(str_replace(',' , '.', str_replace('.', '', $dataForm['value'])), FILTER_VALIDATE_FLOAT),
            'stock'         => filter_var($dataForm['stock'], FILTER_VALIDATE_FLOAT),
            'alert_stock'   => filter_var($dataForm['alert_stock'], FILTER_VALIDATE_FLOAT),
            'width'         => filter_var($dataForm['width'], FILTER_VALIDATE_FLOAT),
            'height'        => filter_var($dataForm['height'], FILTER_VALIDATE_FLOAT),
            'depth'         => filter_var($dataForm['depth'], FILTER_VALIDATE_FLOAT),
            'weight'        => (float)(filter_var($dataForm['weight'], FILTER_VALIDATE_FLOAT)/1000),
            'active'        => isset($dataForm['active']) ? 1 : 0,
            'use_price_promo'   => isset($dataForm['use_price_promo']) ? 1 : 0,
            'qty_price_promo'   => isset($dataForm['use_price_promo']) ? filter_var($dataForm['qty_price_promo'], FILTER_VALIDATE_FLOAT) : 0,
            'price_promo'       => isset($dataForm['use_price_promo']) ? filter_var(str_replace(',' , '.', str_replace('.', '', $dataForm['price_promo'])), FILTER_VALIDATE_FLOAT) : 0,
            'description'   => $dataForm['description'],
            'value_high_discount' => filter_var(str_replace(',' , '.', str_replace('.', '', $dataForm['value_high_discount'])), FILTER_VALIDATE_FLOAT)
        );

        $product_id = filter_var($dataForm['product_id'], FILTER_VALIDATE_INT);

        // Atualiza dados na tabela 'automoveis'
        return $this->where('id', $product_id)
            ->update($update);
    }

    public function insert($dataForm)
    {
        // Cria array validado com nomes das colunas da tabela 'automoveis'
        $insert = array(
            'name'          => filter_var($dataForm['name'], FILTER_SANITIZE_STRING),
            'value'         => filter_var(str_replace(',' , '.', str_replace('.', '', $dataForm['value'])), FILTER_VALIDATE_FLOAT),
            'stock'         => filter_var($dataForm['stock'], FILTER_VALIDATE_FLOAT),
            'alert_stock'   => filter_var($dataForm['alert_stock'], FILTER_VALIDATE_FLOAT),
            'width'         => filter_var($dataForm['width'], FILTER_VALIDATE_FLOAT),
            'height'        => filter_var($dataForm['height'], FILTER_VALIDATE_FLOAT),
            'depth'         => filter_var($dataForm['depth'], FILTER_VALIDATE_FLOAT),
            'weight'        => (float)(filter_var($dataForm['weight'], FILTER_VALIDATE_FLOAT)/1000),
            'active'        => isset($dataForm['active']) ? 1 : 0,
            'description'   => $dataForm['description'],
            'value_high_discount' => filter_var(str_replace(',' , '.', str_replace('.', '', $dataForm['value_high_discount'])), FILTER_VALIDATE_FLOAT)
        );

        // Insere dados na tabela 'automoveis'
        return $this->create($insert);
    }

    public function getCountProductsStockLow()
    {
        return $this
            ->where('stock', '<>', 0)
            ->where('active', 1)
            ->whereColumn('alert_stock', '>=', 'stock')
            ->count();
    }

    public function getListProductsActive()
    {
        return $this
                ->join('images', 'products.id', '=', 'images.product_id')
                ->limit(8)
                ->where(['images.primary' => 1, 'products.active' => 1])
                ->orderBy('products.value', 'desc')
                ->get();
    }

}
