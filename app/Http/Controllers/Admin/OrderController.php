<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItems;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Http\Controllers\Mail\SendMailController;
use App\Admin;
use App\Models\OrderAddress;
use Picqer\Barcode\BarcodeGeneratorHTML;

class OrderController extends Controller
{
    private $order;
    private $status;
    private $sendMail;
    private $admin;
    private $order_address;
    private $barCode;

    public function __construct(Order $order, OrderStatus $status, SendMailController $sendMail, Admin $admin, OrderAddress $order_address, BarcodeGeneratorHTML $barCode)
    {
        $this->order    = $order;
        $this->status   = $status;
        $this->sendMail = $sendMail;
        $this->admin    = $admin;
        $this->order_address = $order_address;
        $this->barCode  = $barCode;
    }

    public function list()
    {
        $orders = $this->order->getOrders();
        $arrOrders = array();

        foreach ($orders as $order) {
            $lastStatus = $this->status->getLastStatus($order["id"]);

            switch ($lastStatus['status']) {
                case 1:
                case 2:
                    $crossDocking = "Não Pago";
                    $orderCrossDocking = 0;
                    break;
                case 3:
                case 50:
                    $dataAdmin          = $this->admin->getAdminMain();
                    $limitCrossDocking  = (int)$dataAdmin->order_submission_limit;
                    $crossDocking       = strtotime(date('Y-m-d', strtotime("+{$limitCrossDocking} days", strtotime($lastStatus['created_at']))));
                    $dateToday          = strtotime(date('Y-m-d', time()));

                    if($crossDocking == $dateToday) {
                        $crossDocking = "<span class='text-warning'>Enviar Hoje</span>";
                        $orderCrossDocking = 5;
                    }
                    elseif($crossDocking < $dateToday) {
                        $crossDocking = "<span class='text-danger'>Atrasado</span>";
                        $orderCrossDocking = 6;
                    }
                    elseif($crossDocking > $dateToday) {
                        $crossDocking = "<span class='text-success'>Em Dia</span>";
                        $orderCrossDocking = 4;
                    }

                    break;
                case 7:
                case 55:
                case 99:
                    $crossDocking = "Cancelado";
                $orderCrossDocking = 3;
                    break;
                case 51:
                    $crossDocking = "Enviado";
                    $orderCrossDocking = 2;
                    break;
                default:
                    $crossDocking = "Outros";
                    $orderCrossDocking = 1;
                    break;
            }

            array_push($arrOrders, [
                "id"                    => $order["id"],
                "id_view"               => '#' . str_pad($order["id"], 5, "0", STR_PAD_LEFT),
                "name"                  => $order["name_sender"],
                "tel"                   => $this->formatPhone($order['tel_sender']),
                'id_trans'              => $order['id_transaction'],
                'item_count'            => (int)$order["item_count"],
                'created_at'            => $order["created_at"] ? date('d/m/Y H:i:s', strtotime($order["created_at"])) : 'Não Informado',
                'datetime_order'        => $order["created_at"] ? strtotime($order["created_at"]) : 0,
                'cross_docking'         => $crossDocking,
                'order_cross_docking'   => $orderCrossDocking,
                'last_status'           => $lastStatus['status'] ? $this->statusOrder[$lastStatus['status']] : $this->statusOrder[0],
                'code_status'           => $lastStatus['status']
            ]);
        }

        return view('admin.order.index', compact('arrOrders'));
    }

    public function view(int $id)
    {
        /** Dados do pedido */
        $total_products = 0;
        $total_items = 0;
        $arrOrder = $this->order->getOrderComplet($id);

        if (!count($arrOrder))
            return redirect()->route('admin.orders');

        foreach ($arrOrder as $iten) {
            $total_products += (float)$iten['total_iten'];
            $total_items += (int)$iten['quantity'];
        }
        $arrOrder[0]['total_value_products'] = $total_products;
        $arrOrder[0]['total_qty_products'] = $total_items;
        $arrOrder[0]['cep'] = $this->formataCep($arrOrder[0]['cep']);

        /** Dados dos status */
        $arrStatus = array();
        $statuses = $this->status->getStatusOrder($id);
        $datePayment = null;
        foreach ($statuses as $status) {
            if ($status->status == 3 || $status->status == 50) $datePayment = $status->created_at;
            array_push($arrStatus, array(
                'id' => $status['status'],
                'name'  => $this->statusOrder[$status['status']],
                'date'  => $status["date"] ? date('d/m/Y H:i:s', strtotime($status["date"])) : 'Não Informado',
            ));
        }


        $dataAdmin          = $this->admin->getAdminMain();
        $limitCrossDocking  = (int)$dataAdmin->order_submission_limit;
        if(!$datePayment)
            $arrOrder[0]['date_cross_docking'] = "Não Pago";
        else
            $arrOrder[0]['date_cross_docking'] = date('d/m/Y', strtotime("+{$limitCrossDocking} days", strtotime($datePayment)));

        /** Rastreio */
        $statusEmTransporte = false;
        // pedido enviado - verificar se existe o status
        $statusEmTransporteDb = $this->status
            ->where(['order_id' => $id, 'status' => 51]);

        if ($statusEmTransporteDb->count() > 0) $statusEmTransporte = true;
        $trackings = [];
        if($statusEmTransporte){
            $codes_tracking = json_decode($arrOrder[0]['codes_tracking']);
            $dates_tracking = json_decode($arrOrder[0]['dates_tracking']);

            $trackings = [
                'codes' => $codes_tracking,
                'dates' => $dates_tracking
            ];

        }

        return view('admin.order.view', compact('arrOrder', 'arrStatus', 'trackings'));
    }

    public function freight($id)
    {
        return view('admin.order.freight', compact('id'));
    }

    public function freightUpdate(Request $request)
    {
        $code_tracking  = $request->code_tracking;
        $date_time_post = $request->date_time_post;
        $order_id       = $request->order_id;
        $date_formated  = array();

        if(count($code_tracking) != count($date_time_post) || count($code_tracking) == 0)
            return redirect()->route('admin.orders')
                ->withErrors(['Não foi possível atualizar o rastreio, tente novamente']);

        if ($this->status->getStatusByOrder($order_id, [51]) != 0)
            return redirect()->route('admin.orders')
                ->with('success', 'Pedido atualizado com sucesso!');

        foreach ($date_time_post as $date) {
            array_push($date_formated, $this->formatDateTime($date, 'en'));
        }

        $this->order->updateTracking(json_encode($code_tracking), json_encode($date_formated), $order_id);
        $this->status->updateStatus([
            'order_id'          => $order_id,
            'code'              => "",
            'status'            => 51,
            'reference_order_id'=> $order_id,
            'date'              => date('Y-m-d H:i:s')
        ]);
        $this->sendMail->updateOrder((int)$order_id, 51);

        return redirect()->route('admin.orders')
                         ->with('success', 'Pedido atualizado com sucesso!');


    }

    public function received(Request $request)
    {
        $order_id = $request->order_id;
        $order = $this->order->where('id', $order_id);

        if($order->count() == 0)
            return redirect()->route('admin.orders')
                ->withErrors(['Não foi possível atualizar o pedido, tente novamente']);

        if ($this->status->getStatusByOrder($order_id, [54]) != 0)
            return redirect()->route('admin.orders')
                ->with('success', 'Pedido atualizado com sucesso!');

        $order->update(['date_received' => date('Y-m-d H:i:s')]);
        $this->status->updateStatus([
            'order_id'          => $order_id,
            'code'              => "",
            'status'            => 54,
            'reference_order_id'=> $order_id,
            'date'              => date('Y-m-d H:i:s')
        ]);
        $this->sendMail->updateOrder((int)$order_id, 54);

        return redirect()->route('admin.orders')
            ->with('success', 'Pedido atualizado com sucesso!');
    }

    public function cancel(Request $request)
    {
        $order_id = $request->order_id;
        $order = $this->order->where('id', $order_id);

        if($order->count() == 0)
            return redirect()->route('admin.orders')
                ->withErrors(['Não foi possível atualizar o pedido, tente novamente']);

        if ($this->status->getStatusByOrder($order_id, [7,55,99]) != 0)
            return redirect()->route('admin.orders')
                ->with('success', 'Pedido atualizado com sucesso!');

        $this->status->updateStatus([
            'order_id'          => $order_id,
            'code'              => "",
            'status'            => 99,
            'reference_order_id'=> $order_id,
            'date'              => date('Y-m-d H:i:s')
        ]);

        $product = new Product();
        $product_items = new OrderItems();
        $items = $product_items->getItemsOfOrder($order_id);
        foreach ($items as $iten)
            $product->updateStockProduct((int)$iten['product_id'], (int)$iten['quantity']);

        $this->sendMail->updateOrder((int)$order_id, 99);

        return redirect()->route('admin.orders')
            ->with('success', 'Pedido atualizado com sucesso!');

    }

    public function generateTag($id)
    {
        $orders = explode('-',$id);
        $dataTags = array();

        $admin      = $this->admin->getAdminMain();
        $admin->cep = $this->formataCep($admin->cep);

        foreach ($orders as $id) {

            $clientAddress = $this->order_address->getAddressOfOrder($id);
            $code = $this->barCode->getBarcode($clientAddress->cep, $this->barCode::TYPE_CODE_128, 2, 60);
            $clientAddress->cep = $this->formataCep($clientAddress->cep);
            $client = $this->order->getOrder($id);

            array_push($dataTags, ['admin' => $admin, 'client' => $client, 'clientAddress' => $clientAddress, 'code' => $code]);
        }

        $data['dataTags'] = $dataTags;

//        return view('user.tags-correios.tag', $data);

        $pdf = \PDF::loadView('user.tags-correios.tag', $data);
//    return $pdf->download('invoice.pdf');
        return $pdf->stream();
    }

    public function tags()
    {
        $orders = $this->order->getOrders();
        $arrOrders = array();

        foreach ($orders as $order) {
            $lastStatus = $this->status->getLastStatus($order["id"]);

            if(!isset($lastStatus->status) || $lastStatus->status != 50) continue;

            array_push($arrOrders, [
                "id"                => $order["id"],
                "id_view"           => '#' . str_pad($order["id"], 5, "0", STR_PAD_LEFT),
                "name"              => $order["name_sender"],
//                "cpf"               => $this->formatDoc($order["cpf_sender"]),
                "tel"               => $this->formatPhone($order['tel_sender']),
                'id_trans'          => $order['id_transaction'],
                'item_count'        => (int)$order["item_count"],
                'created_at'        => $order["created_at"] ? date('d/m/Y H:i:s', strtotime($order["created_at"])) : 'Não Informado',
                'datetime_order'    => $order["created_at"] ? strtotime($order["created_at"]) : 0,
                'last_status'       => $this->statusOrder[$lastStatus->status],
                'code_status'       => $lastStatus->status
            ]);
        }

        return view('admin.order.tags', compact('arrOrders'));
    }
}
