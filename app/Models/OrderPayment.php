<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'gross_amount',
        'discount_amount',
        'fee_amount',
        'net_amount',
        'extra_amount',
        'coupon_name',
        'installment_count',
        'value_ship',
        'type_payment',
        'code_payment',
        'qty_parcels',
        'method_payment',
        'value_total_with_tax_card',
        'parcel_card',
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

    public function createNewPayment($data, $order_id)
    {
        $create = $this->create([
            'order_id'                  => $order_id,
            'gross_amount'              => ($data['total_order'] - $data['dataFreteSelected']['price']) + ($data['cupom']['value'] != 0 ? (float)$data['cupom']['value'] * (-1) : 0),
            'discount_amount'           => $data['cupom']['value'] != 0 ? (float)$data['cupom']['value'] : 0,
            'fee_amount'                => 0,
            'net_amount'                => 0,
            'extra_amount'              => $data['cupom']['value'] != 0 ? (float)$data['cupom']['value'] * (-1) : 0,
            'coupon_name'               => $data['cupom']['name'],
            'installment_count'         => 0,
            'type_payment'              => $data['methodPayment'] == "card" ? 1 : 2,
            'code_payment'              => 0,
            'value_ship'                => $data['dataFreteSelected']['price'],
            'qty_parcels'               => isset($data['card']) ? $data['card']['installment'] : 0,
            'method_payment'            => $data['methodPayment'],
            'value_total_with_tax_card' => 0,
            'parcel_card'               => 0,
            'user_id'                   => auth()->guard('client')->user()->id
        ]);
        return $create;
    }

    public function updateNewPayment($data, $order_id)
    {
        $update = $this->where('order_id', $order_id)->update([
//            'gross_amount'      => $data->transaction_details->total_paid_amount,
            'fee_amount'        => $data->transaction_details->net_received_amount != 0 ? $data->transaction_amount - $data->transaction_details->net_received_amount : 3.49,
            'net_amount'        => $data->transaction_amount,
            'installment_count' => $data->installments ?? 0,
//            'type_payment'      => $data->paymentMethod->type,
//            'code_payment'      => $data->paymentMethod->code,
            'value_total_with_tax_card' => $data->transaction_details->total_paid_amount ?? 0,
            'parcel_card'               => $data->transaction_details->installment_amount ?? 0,
        ]);
        return $update;
    }
}
