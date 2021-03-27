<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\User;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\OrderStatus;
use App\Models\Image;
use function GuzzleHttp\Psr7\str;

class AccountController extends Controller
{
    private $user;
    private $address;
    private $order;
    private $order_items;
    private $order_status;
    private $image;

    public function __construct(User $user, Address $address, Order $order, OrderItems $order_items, OrderStatus $order_status, Image $image)
    {
        $this->user         = $user;
        $this->address      = $address;
        $this->order        = $order;
        $this->order_items  = $order_items;
        $this->order_status = $order_status;
        $this->image        = $image;
    }

    public function index()
    {
        return view('user.account.index');
    }

    public function orders()
    {
        $dataOrders         = array();

        $userId = auth()->guard('client')->user()->id;
        $dataOrder = $this->order
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($dataOrder as $order) {
            $statusRecebido     = false;
            $statusPgtoAprovado = false;
            $statusEmTransporte = false;
            $statusEntregue     = false;
            $statusCancelado    = false;

            $dataOrderItems = $this->order_items
                ->join('products', 'products.id', '=', 'order_items.product_id')
                ->join('images', 'images.product_id', '=', 'order_items.product_id')
                ->where(['order_items.order_id' => $order->id, 'images.primary' => 1])
                ->get();

            // pedido recebido - sempre será um pedido recebido
            $statusRecebido = true;

            // pagamento aprovado - verificar se existe o status 3 ou 4
            $statusPago = $this->order_status
                ->where(['order_id' => $order->id, 'status' => 3]);

            // pagamento cancelado - verificar se existe o status 7
            $statusCanceladoDb = $this->order_status
                ->where('order_id', $order->id)
                ->whereIn('status', [7, 55, 99]);

            // pedido enviado - verificar se existe o status
            $statusEmTransporteDb = $this->order_status
                ->where(['order_id' => $order->id, 'status' => 51]);

            // pedido entregue - verificar se existe o status 54
            $statusEntregueDb = $this->order_status
                ->where(['order_id' => $order->id, 'status' => 54]);

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
                'recebido'      => $statusRecebido ? date('d/m H:i', strtotime($order['created_at'])) : "",
                'aprovado'      => $statusPgtoAprovado && !$statusCancelado ? date('d/m H:i', strtotime($statusPago->first()->date)) : "",
                'em_transporte' => $statusEmTransporte && !$statusCancelado ? date('d/m H:i', strtotime($statusEmTransporteDb->first()->date)) : "",
                'entregue'      => $statusEntregue && !$statusCancelado ? date('d/m H:i', strtotime($statusEntregueDb->first()->date)) : "",
                'cancelado'     => $statusCancelado ? date('d/m H:i', strtotime($statusCanceladoDb->first()->date)) : "",
            );

            $status = array(
                'progress' => $statusInProgress,
                'done' => $statusDone,
                'dateStatus' => $statusDate
            );

            array_push($dataOrders, array(
                'dataOrder'     => $order,
                'dataOrderItems'=> $dataOrderItems,
                'status'        => $status
            ));
        }

        return view('user.order.orders', compact('dataOrders'));
    }

    public function edit()
    {
        $dataClient = auth()->guard('client')->user();
        $arrDataClient = array(
            'name' => $dataClient->name,
            'email' => $dataClient->email
        );

        return view('user.account.edit', compact('arrDataClient'));
    }

    public function editPost(Request $request)
    {
        $userId   = auth()->guard('client')->user()->id;

        $validator = validator(
            $request->all(),
            [
                'name'      => 'required|min:3',
                'email'     => ['required', Rule::unique('users')->ignore($userId)],
                'password'  => 'nullable|confirmed|min:6'
            ],
            [
                'name.required'     => 'O nome é um campo obrigatório!',
                'name.min'          => 'O nome precisa de no mínimo 3 caracteres!',
                'email.required'    => 'O email é um campo obrigatório!',
                'email.unique'      => 'O email já está em uso!',
                'password.confirmed'=> 'As senhas não correspondem!',
                'password.min'      => 'A senha precisa de no mínimo 6 caracteres!'
            ]
        );

        if($validator->fails())
            return redirect()->route('user.account.edit')->withErrors($validator)->withInput();


        // verifica senha atual
        if($request->password) {
            if(!Hash::check($request->password_current, auth()->guard('client')->user()->password)) {
                return redirect()
                    ->route('user.account.edit')
                    ->withErrors(['Senha informada não corresponde com a senha atual!'])
                    ->withInput();
            }
        }

        $dataUserUpdate = [
            'name'  => $request->name,
            'email' => $request->email
        ];

        if($request->password)
            $dataUserUpdate['password'] = Hash::make($request->password);

        $update = $this ->user
                        ->where('id', $userId)
                        ->update($dataUserUpdate);

        if($update)
            return redirect()->route('user.account.edit')
                ->with('success', 'Cadastro alterado com sucesso!');


        return redirect()->route('user.account.edit')
            ->withErrors(['Não foi possível alterar o usuário']);
    }
}
