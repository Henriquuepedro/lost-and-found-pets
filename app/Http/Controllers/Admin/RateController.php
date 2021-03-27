<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use http\Client;
use Illuminate\Http\Request;
use App\Models\Rate;
use App\Models\Product;
use App\User;
use Illuminate\View\View;

class RateController extends Controller
{
    private $rate;
    private $product;
    private $client;

    public function __construct(Rate $rate, Product $product, User $client)
    {
        $this->rate     = $rate;
        $this->product  = $product;
        $this->client   = $client;
    }

    public function list()
    {
        $rates = $this->rate->getRates();
        $arrRates = array();

        foreach ($rates as $rate) {
            $nameProduct = $this->product->getNameProducts($rate["product_id"])->name;
            $status = "";

            if($rate["approved"] == 0 && strtotime($rate['updated_at']) == strtotime($rate['created_at']) && $rate['user_id'] != 0)
                $status = '<span class="badge badge-warning col-md-12 text-white" style="padding: 5px 0px;">Pendente</span>';
            elseif($rate["approved"] == 0)
                $status = '<span class="badge badge-danger col-md-12 text-white" style="padding: 5px 0px;">Inativo</span>';
            elseif($rate["approved"] == 1)
                $status = '<span class="badge badge-success col-md-12" style="padding: 5px 0px;">Ativo</span>';

            array_push($arrRates, [
                "id"            => $rate["id"],
                "title"         => $rate["title"],
                'rate'          => (int)$rate["rate"],
                "order"         => $rate["order_id"] == 0 ? "ADM" : $rate["order_id"],
                "status"        => $status,
                "status_order"  => $rate["approved"],
                'product'       => $nameProduct,
                'created_at'    => $rate["created_at"] ? date('d/m/Y H:i', strtotime($rate["created_at"])) : 'Não Informado',
                'datetime_order'=> $rate["created_at"] ? strtotime($rate["created_at"]) : 0,
            ]);
        }

        return view('admin.rate.index', compact('arrRates'));
    }

    public function viewRate(Request $request)
    {
        $rate_id = $request->rate_id;

        if(!$this->rate->getRates($rate_id)){
            echo json_encode(false);
            exit();
        }

        $rate = $this->rate->getRates($rate_id);

        $status = "";

        if($rate["approved"] == 0 && strtotime($rate['updated_at']) == strtotime($rate['created_at']))
            $status = 'Pendente';
        elseif($rate["approved"] == 0)
            $status = 'Inativo';
        elseif($rate["approved"] == 1)
            $status = 'Aprovado';

        echo json_encode(array(
            'title'         => $rate["title"],
            'description'   => $rate["description"],
            'rate'          => $rate["rate"],
            'user_url'      => $rate["user_id"] == 0 ? "#" : route('admin.client.view', ['id' => $rate["user_id"]]),
            'product_url'   => route('admin.products.edit', ['id' => $rate["product_id"]]),
            'order_url'     => $rate["order_id"] == 0 ? "#" : route('admin.orders.view', ['id' => $rate["order_id"]]),
            'order_id'      => $rate["order_id"] == 0 ? "ADM" : $rate["order_id"],
            'product'       => $this->product->getNameProducts($rate["product_id"])->name,
            'user'          => $rate["user_id"] == 0 ? "ADM" : $this->client->getNameClient($rate["user_id"])->name,
            'created_at'    => $rate["created_at"] ? date('d/m/Y H:i', strtotime($rate["created_at"])) : 'Não Informado',
            'status'        => $status
        ));
    }

    public function change(Request $request)
    {
        $rate_id = $request->rate_id;

        if(!$this->rate->getRates($rate_id))
            return redirect()->route('admin.rate')
                    ->with('warning', 'Avaliação não pode ser alterada, tente novamente!');

        $rate = $this->rate->getRates($rate_id);

        if($rate['approved'] == 1) $approved = 0;
        if($rate['approved'] == 0) $approved = 1;
        else  $approved = 0;

        $update = $this->rate->edit(['approved' => $approved], $rate_id);

        if($update)
            return redirect()->route('admin.rate')
                    ->with('success', 'Avaliação alterada com sucesso!');

        return redirect()->route('admin.rate')
                ->with('warning', 'Não foi possível alterar a avaliação, tente novamente');
    }

    public function new()
    {
        $products = $this->product->getProducts();
        return view('admin.rate.new', compact('products'));
    }

    public function insert(Request $request)
    {
        $name_user   = filter_var($request->name_user, FILTER_SANITIZE_STRING);
        $title       = filter_var($request->title, FILTER_SANITIZE_STRING);
        $rate        = filter_var($request->rate, FILTER_VALIDATE_INT);
        $active      = isset($request->active) ? 1 : 0;
        $product     = filter_var($request->product, FILTER_VALIDATE_INT);
        $description = filter_var($request->description, FILTER_SANITIZE_STRING);

        $arrCreate = array(
            'title'         => $title,
            'name_user'     => $name_user,
            'description'   => $description,
            'picture'       => '',
            'rate'          => $rate,
            'user_id'       => 0,
            'order_id'      => 0,
            'product_id'    => $product,
            'approved'      => $active,
        );

        $insert = $this->rate->insert($arrCreate);

        if($insert)
            return redirect()->route('admin.rate')
                    ->with('success', 'Avaliação cadastrada com sucesso!');

        return redirect()->back()
                    ->withErrors(['Não foi possível cadastrar a avaliação, tente novamente'])
                    ->withInput();

    }

    public function edit(int $id)
    {
        $rate = $this->rate->getRateAdmin($id);
        if(!$rate) return redirect()->route('admin.rate');

        $products = $this->product->getProducts();
        return view('admin.rate.edit', compact('products', 'rate'));
    }

    public function update(Request $request)
    {
        $rate_id     = filter_var($request->rate_id, FILTER_VALIDATE_INT);
        $name_user   = filter_var($request->name_user, FILTER_SANITIZE_STRING);
        $title       = filter_var($request->title, FILTER_SANITIZE_STRING);
        $rate        = filter_var($request->rate, FILTER_VALIDATE_INT);
        $active      = isset($request->active) ? 1 : 0;
        $product     = filter_var($request->product, FILTER_VALIDATE_INT);
        $description = filter_var($request->description, FILTER_SANITIZE_STRING);

        if(!$this->rate->getRateAdmin($rate_id)) return redirect()->route('admin.rate');

        $arrCreate = array(
            'title'         => $title,
            'name_user'     => $name_user,
            'description'   => $description,
            'picture'       => '',
            'rate'          => $rate,
            'user_id'       => 0,
            'order_id'      => 0,
            'product_id'    => $product,
            'approved'      => $active,
        );

        $update = $this->rate->edit($arrCreate, $rate_id);

        if($update)
            return redirect()->route('admin.rate')
                ->with('success', 'Avaliação alterada com sucesso!');

        return redirect()->back()
            ->withErrors(['Não foi possível alterar a avaliação, tente novamente'])
            ->withInput();
    }

    public function remove(Request $request)
    {
        $rate_id = filter_var($request->rate_id, FILTER_VALIDATE_INT);

        $delete = $this->rate->remove($rate_id);

        if($delete)
            return redirect()->route('admin.rate')
                ->with('success', 'Avaliação excluída com sucesso!');

        return redirect()->route('admin.rate')
            ->with('warning', 'Não foi possível excluir a avaliação, tente novamente');
    }
}
