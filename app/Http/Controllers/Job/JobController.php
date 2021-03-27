<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderItems;
use App\Http\Controllers\Mail\SendMailController;
use App\Models\LogHistory;
use MercadoPago\Payment;
use App\Models\Product;
use MercadoPago\SDK;

class JobController extends Controller
{
    private $order;
    private $mail;
    private $log;
    private $order_status;
    private $order_items;
    private $product;

    public function __construct(Order $order, SendMailController $mail, LogHistory $log, OrderStatus $order_status, OrderItems $order_items, Product $product)
    {
        $this->order        = $order;
        $this->mail         = $mail;
        $this->log          = $log;
        $this->order_status = $order_status;
        $this->order_items  = $order_items;
        $this->product      = $product;
    }

    /** HASH: qHQQ5L4dplLlp2fdAFb2XuFtD5deUp1GxKU3bYDiErg9SOq6tYkcK */
    public function sendMailBillet($hash)
    {
        /** Cria log */
        $log = [
            'name' => 'Inicio CRON verifica boleto at: ' . date('d/m/Y H:i:s'), 'description' => $hash, 'type' => 'success', 'class' => __CLASS__, 'method' => __FUNCTION__, 'user_id' => $_SERVER['HTTP_HOST']
        ];
        $this->log->createLog($log);

        if($hash != "qHQQ5L4dplLlp2fdAFb2XuFtD5deUp1GxKU3bYDiErg9SOq6tYkcK")
            abort(404);

        $daysExpBillet = 3; // dias de vencimento do boleto

        foreach ($this->order->getUnpaidOrdersLastDays($daysExpBillet) as $order) {
//            echo "enviar e-mail pro pedido: {$order['id']}\n";
            $lastStatus = $this->order_status->getLastStatus($order['id']);
//            echo "último status: {$lastStatus['status']}\n";
            if (!isset($lastStatus['status']) || $lastStatus['status'] != 1) continue;
//            echo "enviou e-mail pro pedido: {$order['id']}\n";

            $this->mail->billetOverduePayment($order['id']);
            /** Cria log */
            $log = [
                'name' => 'Enviou e-mail boleto não pago, pedido: '.$order['id'], 'description' => "Pedido código: ".$order['id'], 'type' => 'success', 'class' => __CLASS__, 'method' => __FUNCTION__, 'user_id' => $_SERVER['HTTP_HOST']
            ];
            $this->log->createLog($log);
        }

        return true;
    }

    /** HASH: df90gFD4gd0fj4qHQQ5L4hjs3g93dplLlp2fdAFSOrbsFGg48Doe5 */
    public function cancelBillet($hash)
    {
        /** Cria log */
        $log = [
            'name' => 'Inicio CRON cancela boleto at: ' . date('d/m/Y H:i:s'), 'description' => $hash, 'type' => 'start', 'class' => __CLASS__, 'method' => __FUNCTION__, 'user_id' => $_SERVER['HTTP_HOST']
        ];
        $this->log->createLog($log);

        if($hash != "df90gFD4gd0fj4qHQQ5L4hjs3g93dplLlp2fdAFSOrbsFGg48Doe5")
            abort(404);

        $ordersNot = array();
        foreach ($this->order->getUnpaidOrdersForBetweenDate(10, 8) as $order)
            array_push($ordersNot, $order->id);

        echo "Pedido que não serão cancelados: \n\n" . json_encode($ordersNot);
        $this->createLog('CRON Cancelar Pedido', "Pedidos que não serão cancelados: " . json_encode($ordersNot), "process",__CLASS__, __FUNCTION__, $_SERVER['HTTP_HOST']);

        SDK::setAccessToken(PROD_TOKEN);

        foreach ($this->order->getUnpaidOrdersBillet(10, 8, $ordersNot) as $order) {

            $payment = new Payment();

            try {
                $update_payment = $payment->find_by_id($order->id_transaction);

                // Se já foi cancelado, mas não cancelou no sistema
                if($update_payment->status == "cancelled") {
                    $arrUpdatePago = [
                        'order_id'              => $order->id,
                        'code'                  => $order->id_transaction,
                        'status'                => 7,
                        'reference_order_id'    => $order->id,
                        'date'                  => date('Y-m-d H:i:s')
                    ];
                    $this->order_status->updateStatus($arrUpdatePago);
                    $this->mail->updateOrder($order->id, 50);

                    $items = $this->order_items->getItemsOfOrder($order->id);
                    foreach ($items as $iten)
                        $this->product->updateStockProduct((int)$iten['product_id'], (int)$iten['quantity']);
                } else {
                    $update_payment->status = "cancelled";
                    $update_payment->update();
                }

            } catch(\Exception $e) {
                /** Cria log */
                $log = [
                    'name' => 'Pagamento não encontrado: '.$order['id'], 'description' => "Não foi encontrado o pagamento do pedido: ".$order['id'], 'type' => 'error', 'class' => __CLASS__, 'method' => __FUNCTION__, 'user_id' => $_SERVER['HTTP_HOST']
                ];
                $this->log->createLog($log);
            }

            /** Cria log */
            $log = [
                'name' => 'Cancelou pedido: '.$order['id'], 'description' => "Pedido código: ".$order['id'], 'type' => 'success', 'class' => __CLASS__, 'method' => __FUNCTION__, 'user_id' => $_SERVER['HTTP_HOST']
            ];
            $this->log->createLog($log);
        }

        response()->json(['success' => 'success'], 200);
    }
}
