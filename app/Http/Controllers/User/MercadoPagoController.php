<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LogHistory;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderItems;
use App\Models\Product;
use App\Http\Controllers\Mail\SendMailController;
use MercadoPago\Payment;
use MercadoPago\SDK;

class MercadoPagoController extends Controller
{
    private $log_history;
    private $order;
    private $order_status;
    private $order_items;
    private $product;
    private $sendMail;

    public function __construct(LogHistory $log_history, Order $order, OrderStatus $order_status, OrderItems $order_items, Product $product, SendMailController $sendMail)
    {
        $this->log_history  = $log_history;
        $this->order        = $order;
        $this->order_status = $order_status;
        $this->order_items  = $order_items;
        $this->product      = $product;
        $this->sendMail     = $sendMail;
    }

    public function notification(Request $request)
    {
        // Cria log
        $this->createLog('Chegou notificação', json_encode($request->all()), "success",__CLASS__, __FUNCTION__, $_SERVER['HTTP_HOST']);

        try {
            // Veio via IPN, não será usado, apenas webhook
            if(!isset($request->data_id)){
                // Cria log
                $this->createLog('Chegou IPN', "Não será usado IPN ação ignorada", "warning",__CLASS__, __FUNCTION__, $_SERVER['HTTP_HOST']);
                response()->json(['success' => 'success'], 200);
                return false;
            }

            $code = $request->data_id;
            $StatusWebHook = $request->action;
            $order = $this->order->getOrderMercadoPago($code);
            if (!$order) {
                // Cria log
                $this->createLog('Erro Notificação Mercado Pago', "Pedido não encontrado com esse código: " . $code, "error",__CLASS__, __FUNCTION__, $_SERVER['HTTP_HOST']);
                response()->json(['success' => 'success'], 401);
            }
            else if ($order) {
                $order_id = (int)$order->id;
                $method_payment = $order->method_payment;
                $delivery_date = $order->delivery_date;
                $created_at = $order->created_at;

                if ($StatusWebHook == "payment.updated") {
                    // recupera dados do mercado pago
                    SDK::setAccessToken(PROD_TOKEN); // Either Production or SandBox AccessToken
                    $payment = new Payment();
                    try {
                        $dataPayment = $payment->find_by_id($code);
                    } catch(Exception $e) {
                        $log = ['name' => 'Consulta Pagamento', 'description' => "Encontrou um problema com o pedido: {$code} \n Erro: "  . $e->getMessage(), 'type' => 'error', 'class' => __CLASS__, 'method' => __FUNCTION__, 'user_id' => $_SERVER['HTTP_HOST']];
                        $this->log_history->createLog($log);
                        response()->json(['error' => 'invalid'], 401);
                        exit();
                    }

                    // Cria log
                    $this->createLog('pegou os dados no mercado pago', "Recuperou os dados corretamente", "success",__CLASS__, __FUNCTION__, $_SERVER['HTTP_HOST']);

                    $dateUpdate = $dataPayment->last_modified;
                    $status = $dataPayment->status;
                    $codeStatus = (int)$this->deParaStatus($status);

                    if ($codeStatus == 0) {
                        $this->createLog('status desconhecido', "Chegou o status: {$codeStatus} para o pedido: {$order_id}, status desconhecido no sistema", "warning",__CLASS__, __FUNCTION__, $_SERVER['HTTP_HOST']);
                        response()->json(['success' => 'success'], 200);
                        return;
                    }

                    /** Se estiver cancelado, não receber mais atualização */
                    if (
                        $this->order_status->getCountStatusForOrderAndStatusId($order_id, 7) > 0 ||
                        $this->order_status->getCountStatusForOrderAndStatusId($order_id, 99) > 0 ||
                        $this->order_status->getCountStatusForOrderAndStatusId($order_id, 55) > 0
                    ) {
                        $this->createLog('pedido cancelado, não será mais atualizado', "Chegou o status: {$codeStatus} para o pedido: {$order_id}", "warning",__CLASS__, __FUNCTION__, $_SERVER['HTTP_HOST']);
                        response()->json(['success' => 'success'], 200);
                        return;
                    }

                    /** Não atualizar novamente um status já existente */
                    if ($this->order_status->getCountStatusForOrderAndStatusId($order_id, $codeStatus) > 0) {
                        $this->createLog('status duplicado', "Chegou o status: {$codeStatus} para o pedido: {$order_id}, status já existe no sistema", "warning",__CLASS__, __FUNCTION__, $_SERVER['HTTP_HOST']);
                        response()->json(['success' => 'success'], 200);
                        return;
                    }

                    /** Cria status */
                    $arrUpdate = [
                        'order_id' => $order_id,
                        'code' => $code,
                        'status' => $codeStatus,
                        'reference_order_id' => $order_id,
                        'date' => date('Y-m-d H:i:s', strtotime($dateUpdate))
                    ];
                    $this->order_status->updateStatus($arrUpdate);
                    $this->sendMail->updateOrder((int)$order_id, (int)$codeStatus);

                    /** Se for pago, já deixar como aguardando envio */
                    if ($codeStatus == 3) {

                        if ($method_payment == 'billet') {

                            $create_at_delivery = new \DateTime( date('Y-m-d', strtotime($created_at)) );
                            $date_now_delivery = new \DateTime( date('Y-m-d') );

                            $timeNewDelivery = $create_at_delivery->diff($date_now_delivery)->days;
                            $newDateDelivery = date('Y-m-d', strtotime("+{$timeNewDelivery} days", strtotime($delivery_date)));

                            $this->order->updateDateDelivery($order_id, $newDateDelivery);
                        }

                        $arrUpdatePago = [
                            'order_id' => $order_id,
                            'code' => $code,
                            'status' => 50,
                            'reference_order_id' => $order_id,
                            'date' => date('Y-m-d H:i:s', strtotime($dateUpdate))
                        ];
                        $this->order_status->updateStatus($arrUpdatePago);
                        $this->sendMail->updateOrder($order_id, 50);
                    }

                    /** Volta estoque se pedido for cancelado */
                    if ($codeStatus == 7) {
                        $items = $this->order_items->getItemsOfOrder($order_id);
                        foreach ($items as $iten)
                            $this->product->updateStockProduct((int)$iten['product_id'], (int)$iten['quantity']);
                    }
                }

                response()->json(['success' => 'success'], 200);
            }

        } catch (\Exception $e) {
            $this->createLog("Erro notificação", $e->getMessage(), "error", __CLASS__, __FUNCTION__, $_SERVER['HTTP_HOST']);
            response()->json(['error' => 'invalid'], 401);
        }
    }

    public function deParaStatus($status)
    {
        switch ($status) {
            case 'pending':
                return 1;
                break;
            case 'in_process':
                return 2;
                break;
            case 'approved':
            case 'authorized':
                return 3;
                break;
            case 'in_mediation':
                return 5;
                break;
            case 'rejected':
            case 'cancelled':
                return 7;
                break;
            case 'refunded':
            case 'charged_back':
                return 6;
            default:
                return 0;
                break;
        }
    }
}
