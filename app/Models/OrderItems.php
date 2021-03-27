<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderItems extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'product_id', 'description', 'quantity', 'amount', 'total_iten', 'user_id'
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

    public function createNewIten($data, $order_id)
    {
        $y = array();
        foreach($data['dataProducts'] as $item){
            array_push($y, [
                'order_id'      => $order_id,
                'product_id'    => $item['itemId'],
                'description'   => $item['itemDescription'],
                'quantity'      => $item['itemQuantity'],
                'amount'        => $item['itemAmount'],
                'total_iten'    => (float)$item['itemAmount'] * (float)$item['itemQuantity'],
                'user_id'       => auth()->guard('client')->user()->id
            ]);
        }
        $this->insert($y);
    }

    public function getCountOrderItems()
    {
        $ordersItems = $this
            ->select([DB::raw("SUM(order_items.quantity) as qty_total")])
            ->join('order_statuses', 'order_items.order_id', 'order_statuses.order_id')
            ->where('status', 3)
            ->first();

        return (int)$ordersItems->qty_total;
    }

    public function getBestSellingItems($orders_id)
    {
        $cods   = array();
        $order  = new Order();

        $query = $this->select([DB::raw("SUM(quantity) as qty_total"), 'product_id', 'order_items.order_id'])
                        ->join('order_statuses', 'order_items.order_id', '=', 'order_statuses.order_id')
                        ->whereNotIn('order_items.order_id', $orders_id)
                        ->where('order_statuses.status', 3)
                        ->limit(5)
                        ->groupBy('product_id')
                        ->orderBy('qty_total', 'DESC')
                        ->get();

        foreach ($query as $iten) {
            if(count($order->getPaidOrder($iten['order_id'])) == 0) continue;
                array_push($cods, ['id' => $iten['product_id'], 'qty' => $iten['qty_total']]);
        }

        return $cods;
    }

    public function getItemsOfOrder($order_id)
    {
        return $this->where('order_id', $order_id)->get();
    }

    public function getCountProductSold($product_id)
    {
        return $this->where('product_id', $product_id)->count();
    }
}
