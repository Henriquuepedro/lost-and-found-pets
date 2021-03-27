<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Mail\SendMailController;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private $order;
    private $status;
    private $sendMail;
    private $admin;
    private $order_address;

    public function __construct(Order $order, OrderStatus $status, SendMailController $sendMail, Admin $admin, OrderAddress $order_address)
    {
        $this->order    = $order;
        $this->status   = $status;
        $this->sendMail = $sendMail;
        $this->admin    = $admin;
        $this->order_address = $order_address;
    }

    public function order_reconciliation()
    {

        $orders = $this->order->getOrderComplet();
        $arrOrders = array();

        foreach ($orders as $order) {

            $orderId = $order['order_id'];

            if (array_key_exists($orderId, $arrOrders)) continue;

            // verifica se já foi pago
            if (!$this->status->getStatusByOrder($orderId, [3])) continue;
            // verifica se foi cancelado
            if ($this->status->getStatusByOrder($orderId, [7, 55, 99])) continue;

            $arrOrders[$orderId] = array(
                "id"                    => $orderId,
                "id_view"               => '#' . str_pad($orderId, 5, "0", STR_PAD_LEFT),
                "client"                => $order["name_sender"],
                "transaction"           => $order["id_transaction"],
                'gross_amount'          => 'R$ ' . number_format($order['gross_amount'], 2, ',', '.'),
                'discount_amount'       => 'R$ ' . number_format($order['discount_amount'], 2, ',', '.'),
                'value_ship'            => 'R$ ' . number_format($order['value_ship'], 2, ',', '.'),
                'net_amount'            => 'R$ ' . number_format($order['net_amount'], 2, ',', '.'),
                'fee_amount'            => 'R$ ' . number_format($order['fee_amount'] * (-1), 2, ',', '.'),
                'expectancy_amount'     => 'R$ ' . number_format($order['net_amount'] - $order['fee_amount'], 2, ',', '.'),
                'gross_amount_order'    => $order['gross_amount'],
                'discount_amount_order' => $order['discount_amount'],
                'value_ship_order'      => $order['value_ship'],
                'net_amount_order'      => $order['net_amount'],
                'fee_amount_order'      => $order['fee_amount'] * (-1),
                'expectancy_amount_order'=> $order['net_amount'] - $order['fee_amount']
            );
        }

        return view('admin.report.order_reconciliation', compact('arrOrders'));
    }
}
