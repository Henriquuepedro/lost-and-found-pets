<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rate;
use App\Models\OrderItems;
use App\Models\Order;
use App\Models\Testimony;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image as ImageUpload;

class RateController extends Controller
{
    private $rate;
    private $order_items;
    private $order;
    private $testimony;

    public function __construct(Rate $rate, OrderItems $order_items, Order $order, Testimony $testimony)
    {
        $this->rate         = $rate;
        $this->order_items  = $order_items;
        $this->order        = $order;
        $this->testimony    = $testimony;
    }

    public function rate(Request $request)
    {
        $order_id = $request->order_id;
        $userId = auth()->guard('client')->user()->id;

        if(count($this->order->getOrders($userId, $order_id)) == 0)
            return redirect()->route('user.account.order', ['id' => $order_id])
                             ->with('warning', 'Pedido não encontrado para avaliar!');;

        foreach($this->order_items->getItemsOfOrder($order_id) as $product) {
            $dataRate = [
                'title'         => $request["title_{$product['product_id']}"],
                'rate'          => (int)$request["rate_{$product['product_id']}"],
                'description'   => $request["description_{$product['product_id']}"],
                'user_id'       => $userId,
                'order_id'      => (int)$order_id,
                'product_id'    => $product['product_id']
            ];
            $this->rate->insert($dataRate);
        }

        return redirect()->route('user.account.order', ['id' => $order_id])
                         ->with('success', 'Pedido avaliado com sucesso!');

    }

    public function newForUserTestimony(Request $request)
    {
        $user = auth()->guard('client')->user();

        $user_id    = $user->id;
        $name       = $user->name;
        $testimony  = filter_var($request->testimony, FILTER_SANITIZE_STRING);
        $rate       = filter_var($request->rate, FILTER_VALIDATE_INT);

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        $dataForm = [
            'name'      => $name,
            'testimony' => $testimony,
            'rate'      => $rate,
            'user_id'   => $user_id
        ];

        $testimony_id   = $this->testimony->insert($dataForm);
        $picture        = $this->upload($request->picture, $testimony_id);
        if (!$picture) {
            DB::rollBack();
            return redirect()->route('user.account')
                ->withErrors(['São permitidas imagens com extensões em png, jpg ou jpeg.']);
        }

        $update = $this->testimony->edit(['picture' => $picture], $testimony_id);

        if ($testimony_id && $update) {
            DB::commit();
            return redirect()->route('user.account')
                ->with('success', 'Depoimento cadastrado com sucesso!');
        }

        DB::rollBack();
        return redirect()->route('user.account')
            ->withErrors(['Não foi possível cadastrar o depoimento, tente novamente']);
    }

    public function upload($file, $id)
    {
        $extension = $file->getClientOriginalExtension(); // Recupera extensão da imagem

        // Verifica extensões
        if($extension != "png" && $extension != "jpeg" && $extension != "jpg" && $extension != "gif") return false;

        $imageName  = "{$id}.{$extension}"; // Pega apenas o 15 primeiros e adiciona a extensão
        $uploadPath = "user/img/testimony/{$imageName}";
        $realPath   = $file->getRealPath();

        ImageUpload::make($realPath)->resize(70,70)->save($uploadPath);

        return $imageName;

    }
}
