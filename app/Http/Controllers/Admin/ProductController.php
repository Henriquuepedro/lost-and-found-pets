<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItems;

class ProductController extends Controller
{
    private $product;
    private $image;
    private $order_items;

    public function __construct(Product $product, Image $image, OrderItems $order_items)
    {
        $this->product  = $product;
        $this->image    = $image;
        $this->order_items  = $order_items;
    }

    public function list()
    {
        $products = $this->product->getProductsWithImage();
        $arrProducts = array();

        foreach ($products as $product) {

            $stockLow = "";
            if ($product['alert_stock'] != 0 ) {
                if($product['alert_stock'] == $product['stock']) {
                    $stockLow = ' <span class="badge badge-warning col-md-12 text-white" style="padding: 5px 5px; width: auto">Baixo</span>';
                }
                if($product['alert_stock'] > $product['stock']) {
                    $stockLow = ' <span class="badge badge-danger col-md-12 text-white" style="padding: 5px 5px; width: auto">Baixo</span>';
                }
            }
            $active = '<span class="badge badge-success col-md-12 text-white" style="width: 100%">Sim</span>';
            if ($product['active'] == 0 ) {
                $active = '<span class="badge badge-danger col-md-12 text-white" style="width: 100%">Não</span>';
            }

            array_push($arrProducts, [
                "id"            => $product["id"],
                "name"          => $product["name"],
                "value"         => number_format($product["value"], 2, ',', '.'),
                'path'          => "{$product['id']}/thumbnail_{$product['path']}",
                'stock'         => $product["stock"] . $stockLow,
                'stock_number'  => $product["stock"],
                'active'        => $active,
                'created_at'    => $product["created_at"] ? date('d/m/Y H:i:s', strtotime($product["created_at"])) : 'Não Informado',
                'datetime_order'=> $product["created_at"] ? strtotime($product["created_at"]) : 0,
                'value_order'   => $product["value"]
            ]);
        }

        return view('admin.product.index', compact('arrProducts'));
    }

    public function delete(Request $request)
    {
        $product_id = (int)$request->product_id;

        if($product_id == 0)
            return redirect()->route('admin.products')
                             ->with('warning', 'Não foi possível excluir o produto, ocorreu um problema para recuperar informações do produto!');

        if($this->order_items->getCountProductSold($product_id) != 0)
            return redirect()->route('admin.products')
                             ->with('warning', 'Não foi possível excluir o produto, esse produto já foi comprado por um cliente, existe um vínculo com o(s) pedido(s)!');

        $delete = $this->product->deleteProduct($product_id);

        if($delete)
            return redirect()->route('admin.products')
                ->with('success', 'Produto excluído com sucesso!');

        return redirect()->route('admin.products')
                         ->with('warning', 'Não foi possível excluir o produto');

    }

    public function edit(int $id)
    {
        $product = $this->product->getProductWithImages($id);
        if(count($product) == 0) return redirect()->route('admin.products');

        $imagens = [];
        $primaryKey = 1;
        foreach ($product as $imagem){
            if($imagem['primary'] == 1) $primaryKey = $imagem['image_id'];
            array_push($imagens, ['url' => $imagem['path'], 'primary' => $imagem['primary'] == 1, 'cod' => $imagem['image_id']]);
        }

        $dataProduct = array(
            'id'                => $id,
            'name'              => $product[0]['name'],
            'description'       => $product[0]['description'],
            'value'             => $product[0]['value'],
            'stock'             => (int)$product[0]['stock'],
            'alert_stock'       => (int)$product[0]['alert_stock'],
            'height'            => (int)$product[0]['height'],
            'width'             => (int)$product[0]['width'],
            'depth'             => (int)$product[0]['depth'],
            'weight'            => $product[0]['weight']*1000,
            'active'            => (int)$product[0]['active'],
            'use_price_promo'   => (int)$product[0]['use_price_promo'],
            'qty_price_promo'   => (int)$product[0]['qty_price_promo'],
            'price_promo'       => $product[0]['price_promo'],
            'imagens'           => $imagens,
            'primaryKey'        => $primaryKey,
            'value_high_discount'=> $product[0]['value_high_discount']
        );

        return view('admin.product.edit', compact('dataProduct'));
    }


    public function update(Request $request)
    {
        $product_id = (int)$request->product_id;

        $this->validate($request,
            [
                'images.*'      => 'required|mimes:png,jpeg,jpg,gif|max:2048',
                'name'          => 'required|unique:products,name,'.$product_id,
                'value'         => 'required',
                'stock'         => 'required|numeric',
                'alert_stock'   => 'required|numeric',
                'width'         => 'required|numeric|between:11,105',
                'height'        => 'required|numeric|between:2,105',
                'depth'         => 'required|numeric|between:16,105',
                'weight'        => 'required|numeric',
                'description'   => 'required'
            ],
            [
                'images.*'              => 'São permitidos imagens de até 2048kb e extensões png, jpeg e jpg.',
                'name.required'         => 'O campo nome é obrigatório',
                'name.unique'           => 'Esse nome de produto já está em uso',
                'stock.required'        => 'O campo estoque é obrigatório',
                'stock.numeric'         => 'O campo estoque precisa ser numérico',
                'alert_stock.required'  => 'O campo estoque mínimo é obrigatório',
                'alert_stock.numeric'   => 'O campo estoque mínimo precisa ser numérico',
                'width.required'        => 'O campo largura é obrigatório',
                'width.numeric'         => 'O campo largura precisa ser numérico',
                'height.required'       => 'O campo altura é obrigatório',
                'height.numeric'        => 'O campo altura precisa ser numérico',
                'depth.required'        => 'O campo comprimento é obrigatório',
                'depth.numeric'         => 'O campo comprimento precisa ser numérico',
                'weight.required'       => 'O campo peso é obrigatório',
                'weight.numeric'        => 'O campo peso precisa ser numérico',
                'description.required'  => 'O campo descrição é obrigatório',
            ]
        );

        $dataForm = $request->all(); // Dados recuperado via POST

        if (((int)$dataForm['depth'] + (int)$dataForm['height'] + (int)$dataForm['width']) > 200) {
            return redirect()->back()
                ->withErrors(['A soma da largura + altura + comprimento não pode ser maior que 200'])
                ->withInput();
        }

        if (isset($dataForm['use_price_promo'])) {
            $error_other = [];
            if ($dataForm['qty_price_promo'] == 0)
                array_push($error_other, 'O campo a partir de da promoção, precisa ser maior que zero!');

            if (str_replace(',' , '.', str_replace('.', '', $dataForm['price_promo'])) == 0)
                array_push($error_other, 'O campo preço promocional precisa ser maior que zero!');

            if (count($error_other))
                return redirect()->back()
                    ->withErrors($error_other)
                    ->withInput();
        }

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        $updateProduct  = $this->product->edit($dataForm); // Atualiza dados do automovel
        $updateImages   = $this->image->edit($request, $dataForm); // Insere imagens do automóvel

        if($updateProduct && $updateImages) {
            DB::commit();
            return redirect()->route('admin.products')
                    ->with('success', 'Produto alterado com sucesso!');
        }
        else{
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['Não foi possível alterar o produto, tente novamente'])
                ->withInput();
        }
    }

    public function new()
    {
        return view('admin.product.register');
    }

    public function insert(Request $request)
    {
        $this->validate($request,
            [
                'images.*'      => 'required|mimes:png,jpeg,jpg,gif|max:2048',
                'name'          => 'required|unique:products,name',
                'value'         => 'required',
                'stock'         => 'required|numeric',
                'alert_stock'   => 'required|numeric',
                'width'         => 'required|numeric|between:11,105',
                'height'        => 'required|numeric|between:2,105',
                'depth'         => 'required|numeric|between:16,105',
                'weight'        => 'required|numeric',
                'description'   => 'required'
            ],
            [
                'images.*'              => 'São permitidos imagens de até 2048kb e extensões png, jpeg e jpg.',
                'name.required'         => 'O campo nome é obrigatório',
                'name.unique'           => 'Esse nome de produto já está em uso',
                'stock.required'        => 'O campo estoque é obrigatório',
                'stock.numeric'         => 'O campo estoque precisa ser numérico',
                'alert_stock.required'  => 'O campo estoque mínimo é obrigatório',
                'alert_stock.numeric'   => 'O campo estoque mínimo precisa ser numérico',
                'width.required'        => 'O campo largura é obrigatório',
                'width.numeric'         => 'O campo largura precisa ser numérico',
                'height.required'       => 'O campo altura é obrigatório',
                'height.numeric'        => 'O campo altura precisa ser numérico',
                'depth.required'        => 'O campo comprimento é obrigatório',
                'depth.numeric'         => 'O campo comprimento precisa ser numérico',
                'weight.required'       => 'O campo peso é obrigatório',
                'weight.numeric'        => 'O campo peso precisa ser numérico',
                'description.required'  => 'O campo descrição é obrigatório',
            ]
        );

        $dataForm = $request->all(); // Dados recuperado via POST

        DB::beginTransaction();// Iniciando transação manual para evitar insert não desejáveis

        $insertProduct  = $this->product->insert($dataForm); // Insere dados do produto
        $codAutomovel   = $insertProduct->id; // Recupera código inserido no banco
        $insertImage    = $this->image->insert($request, $dataForm, $codAutomovel); // Insere imagens do produto

        if($insertImage && $insertProduct) {
            DB::commit();
            return redirect()->route('admin.products')
                ->with('success', 'Produto cadastrado com sucesso!');
        }
        else{
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['Não foi possível cadastrar o produto, tente novamente']);
        }
    }
}
