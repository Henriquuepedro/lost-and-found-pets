<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name_sender', 'tel_sender', 'email_sender', 'cpf_sender', 'id_transaction', 'link_billet', 'item_count', 'delivery_date', 'codes_tracking', 'dates_tracking', 'user_id' , 'created_at'
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

    public function createNewOrder($data)
    {
        $create = $this->create([
            'name_sender'   => $data['dataClient']['senderName'],
            'tel_sender'    => $data['dataClient']['senderPhone'],
            'email_sender'  => $data['dataClient']['senderEmailSave'],
            'cpf_sender'    => $data['dataClient']['senderCPF'],
            'item_count'    => $data['totalItems'],
            'id_transaction'=> '',
            'link_billet'   => '',
            'delivery_date' => $data['dataFreteSelected']['date'],
            'codes_tracking'=> '',
            'dates_tracking'=> '',
            'user_id'       => auth()->guard('client')->user()->id
        ]);
        return $create->id;
    }

    public function updateNewOrder($data, $order_id)
    {
        $update = $this->where('id', $order_id)->update([
            'id_transaction'=> $data->id,
            'link_billet'   => $data->transaction_details->external_resource_url ?? '',
        ]);
        return $update;
    }

    public function getCountOrders()
    {
        return $this->count();
    }

    public function getOrderBetweenDate($date1, $date2)
    {
        return $this
            ->join('order_payments', 'orders.id', '=', 'order_payments.order_id')
            ->join('order_statuses', 'orders.id', '=', 'order_statuses.order_id')
            ->whereBetween('orders.created_at', [$date1, $date2])
            ->orderBy('order_statuses.id', 'DESC')
            ->get();
    }

    public function getOrderRecebido($date1, $date2, $orders_id)
    {
        return $this
                    ->select(['*', 'orders.created_at as date_created'])
                    ->join('order_payments', 'orders.id', '=', 'order_payments.order_id')
                    ->join('order_statuses', 'orders.id', '=', 'order_statuses.order_id')
                    ->whereBetween('orders.created_at', [$date1, $date2])
                    ->where('order_statuses.status', 3)
                    ->whereNotIn('orders.id', $orders_id)
                    ->orderBy('orders.created_at', 'DESC')
                    ->get();
    }

    public function getCustomersWhoBuyMore($orders_id)
    {
        $cods   = array();
        $order  = new Order();

        $query = $this->select([DB::raw("count(*) as qty_order"), 'orders.user_id', 'orders.id'])
                        ->join('order_statuses', 'orders.id', '=', 'order_statuses.order_id')
                        ->whereNotIn('orders.id', $orders_id)
                        ->where('order_statuses.status', 3)
                        ->limit(5)
                        ->groupBy('orders.user_id')
                        ->orderBy('qty_order', 'DESC')
                        ->get();

        foreach ($query as $iten) {
            if(count($order->getPaidOrder($iten['id'])) == 0) continue;
                array_push($cods, ['id' => $iten['user_id'], 'qty' => $iten['qty_order']]);
        }

        return $cods;
    }

    public function getOrders($user_id = null, $order_id = null)
    {
        if($user_id && $order_id) return $this->join('order_payments', 'orders.id', '=', 'order_payments.order_id')->where(['orders.user_id' => $user_id, 'orders.id' => $order_id])->get();
        if($user_id) return $this->join('order_payments', 'orders.id', '=', 'order_payments.order_id')->where('orders.user_id', $user_id)->get();
        return $this->orderBy('id', 'desc')->get();
    }

    public function getOrderComplet($order_id = null)
    {
        $order = $this
                ->select(['*', 'orders.created_at as date_order'])
                ->join('order_payments', 'orders.id', '=', 'order_payments.order_id')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('order_addresses', 'orders.id', '=', 'order_addresses.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id');
                if ($order_id) $order->where('orders.id', $order_id);
                else $order->orderBy('orders.id', 'DESC');
                return $order->get();
    }

    public function updateTracking($codes, $dates, $order_id)
    {
        $this->where('id', $order_id)->update([
            'codes_tracking' => $codes,
            'dates_tracking' => $dates
        ]);
    }

    public function getOrdersCanceled()
    {
        $array = array();
        $query = $this
                    ->join('order_statuses', 'orders.id', '=', 'order_statuses.order_id')
                    ->whereIn('order_statuses.status', [7, 55, 99])
                    ->get();

        foreach ($query as $order) {
            array_push($array, $order['order_id']);
        }

        return $array;
    }

    public function getOrder($order_id)
    {
        return $this->find($order_id);
    }

    public function getPaidOrder($order_id)
    {
        return $this->join('order_statuses', 'orders.id', '=', 'order_statuses.order_id')->where(['order_statuses.status' => 3, 'orders.id' => $order_id])->get();
    }

    public function getUnpaidOrdersLastDays($day = 1)
    {
        $yearCheck  = date('Y', strtotime("-{$day} days", strtotime(date('Y-m-d'))));
        $monthCheck  = date('m', strtotime("-{$day} days", strtotime(date('Y-m-d'))));
        $dayCheck  = date('d', strtotime("-{$day} days", strtotime(date('Y-m-d'))));

        return $this
                    ->select(['orders.id'])
                    ->join('order_statuses', 'orders.id', '=', 'order_statuses.order_id')
                    ->join('order_payments', 'orders.id', '=', 'order_payments.order_id')
                    ->whereNotIn('order_statuses.status', [3,7,55,99])
                    ->whereYear('orders.created_at', $yearCheck)
                    ->whereMonth('orders.created_at', $monthCheck)
                    ->whereDay('orders.created_at', $dayCheck)
                    ->where('order_payments.method_payment', 'billet')
                    ->groupBy('order_statuses.order_id')
                    ->get();
    }

    public function getOrderMercadoPago($id)
    {
        return $this
            ->select(["order_payments.method_payment", "orders.id", "orders.delivery_date", "orders.created_at"])
            ->join('order_payments', 'orders.id', '=', 'order_payments.order_id')
            ->where('id_transaction', $id)
            ->first();
    }

    public function getUnpaidOrdersForBetweenDate($dayStart = 10, $dayFinish = 1)
    {
        $dateStart = date('Y-m-d', strtotime("-{$dayStart} days", strtotime(date('Y-m-d')))) . " 00:00:00";
        $dateFinish = date('Y-m-d', strtotime("-{$dayFinish} days", strtotime(date('Y-m-d')))) . " 00:00:00";

        return $this
            ->select(['orders.id'])
            ->join('order_statuses', 'orders.id', '=', 'order_statuses.order_id')
            ->join('order_payments', 'orders.id', '=', 'order_payments.order_id')
            ->where(function($query) {
                $query
                    ->orWhere('order_statuses.status', 3)
                    ->orWhere('order_statuses.status', 7)
                    ->orWhere('order_statuses.status', 99)
                    ->orWhere('order_statuses.status', 55);
            })
            ->whereBetween('orders.created_at', [$dateStart, $dateFinish])
            ->where('order_payments.method_payment', 'billet')
            ->groupBy('order_statuses.order_id')
            ->get();
    }

    public function getUnpaidOrdersBillet($dayStart = 10, $dayFinish = 1, $orders_id = array())
    {
        $dateStart = date('Y-m-d', strtotime("-{$dayStart} days", strtotime(date('Y-m-d')))) . " 00:00:00";
        $dateFinish = date('Y-m-d', strtotime("-{$dayFinish} days", strtotime(date('Y-m-d')))) . " 00:00:00";

        return $this
            ->select(['orders.id', 'orders.id_transaction', 'orders.created_at'])
            ->join('order_statuses', 'orders.id', '=', 'order_statuses.order_id')
            ->join('order_payments', 'orders.id', '=', 'order_payments.order_id')
            ->whereNotIn('orders.id', $orders_id)
            ->whereBetween('orders.created_at', [$dateStart, $dateFinish])
            ->where('order_payments.method_payment', 'billet')
            ->groupBy('order_statuses.order_id')
            ->get();
    }

    public function getDelayedShipments($daysLimit)
    {
        $dateStart = date('Y-m-d', strtotime("-{$daysLimit} days", strtotime(date('Y-m-d')))) . " 00:00:00";

        $orders = $this
//            ->select(['orders.id', 'orders.id_transaction', 'orders.created_at'])
            ->join('order_statuses', 'orders.id', '=', 'order_statuses.order_id')
            ->where([
                'orders.codes_tracking' => '',
                'order_statuses.status' => 3
            ])
            ->where('order_statuses.created_at', '<', $dateStart)
            ->groupBy('order_statuses.order_id')
            ->get();

        $count = 0;
        $orderStatus = new OrderStatus();

        foreach ($orders as $order) {
            $status = $orderStatus->getLastStatus($order->order_id);

            if (!isset($status->status) || !$status) continue;

            if (!in_array($status->status, [3,50])) continue;

            $count++;
        }

        return $count;
    }

    public function getDeliveryForToday($daysLimit)
    {
        $yearCheck  = date('Y', strtotime("-{$daysLimit} days", strtotime(date('Y-m-d'))));
        $monthCheck  = date('m', strtotime("-{$daysLimit} days", strtotime(date('Y-m-d'))));
        $dayCheck  = date('d', strtotime("-{$daysLimit} days", strtotime(date('Y-m-d'))));

        $orders = $this
            ->join('order_statuses', 'orders.id', '=', 'order_statuses.order_id')
            ->where([
                'orders.codes_tracking' => '',
                'order_statuses.status' => 3
            ])
            ->whereYear('order_statuses.created_at', $yearCheck)
            ->whereMonth('order_statuses.created_at', $monthCheck)
            ->whereDay('order_statuses.created_at', $dayCheck)
            ->groupBy('order_statuses.order_id')
            ->get();

        $count = 0;
        $orderStatus = new OrderStatus();

        foreach ($orders as $order) {
            $status = $orderStatus->getLastStatus($order->order_id);

            if (!isset($status->status) || !$status) continue;

            if (!in_array($status->status, [3,50])) continue;

            $count++;
        }

        return $count;
    }

    public function getOrdersWithoutShipping()
    {
        $orders = $this
            ->join('order_statuses', 'orders.id', '=', 'order_statuses.order_id')
            ->where([
                'orders.codes_tracking' => '',
                'order_statuses.status' => 3
            ])
            ->groupBy('order_statuses.order_id')
            ->get();

        $count = 0;
        $orderStatus = new OrderStatus();

        foreach ($orders as $order) {
            $status = $orderStatus->getLastStatus($order->order_id);

            if (!isset($status->status) || !$status) continue;

            if (!in_array($status->status, [3,50])) continue;

            $count++;
        }

        return $count;
    }

    public function getLastOrders($limit = null)
    {
        return $this
            ->select(['id', 'name_sender', 'created_at'])
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }

    public function updateDateDelivery($orderId, $newDate)
    {
        return $this->where('id', $orderId)->update([
            'delivery_date'=> $newDate,
        ]);
    }

}
