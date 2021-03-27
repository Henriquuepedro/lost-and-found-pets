<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class OrderAddress extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'address',
        'cep',
        'number',
        'complement',
        'reference',
        'neighborhood',
        'city',
        'state',
        'value_ship',
        'type_ship',
        'user_id'
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

    public function createNewAddress($data, $order_id)
    {
        $create = $this->create([
            'order_id'      => $order_id,
            'address'       => $data['dataAddress']->address,
            'cep'           => $data['dataAddress']->cep,
            'number'        => $data['dataAddress']->number,
            'complement'    => $data['dataAddress']->complement,
            'reference'     => $data['dataAddress']->reference,
            'neighborhood'  => $data['dataAddress']->neighborhood,
            'city'          => $data['dataAddress']->city,
            'state'         => $data['dataAddress']->state,
            'value_ship'    => $data['dataFreteSelected']['price'],
            'type_ship'     => $data['dataFreteSelected']['name'],
            'user_id'       => auth()->guard('client')->user()->id
        ]);
        return $create;
    }

//    public function updateNewAddress($data, $order_id)
//    {
//        $update = $this->where('id', $order_id)->update([
//
//        ]);
//        return $update;
//    }

    public function getAddressOfOrder($order_id)
    {
        return $this->where('order_id', $order_id)->first();
    }

    public function getStateOrders($limit = null)
    {
        return $this->select('state', DB::raw('COUNT(*) As qty'), DB::raw('SUM(net_amount) as amount'))
            ->join('order_payments', 'order_addresses.order_id', 'order_payments.order_id')
            ->join('order_statuses', 'order_addresses.order_id', 'order_statuses.order_id')
            ->where('order_statuses.status', 3)
            ->orderBy('qty', 'desc')
            ->groupBy('state')
            ->limit($limit)
            ->get();
    }
}
