<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'code', 'status', 'reference_order_id', 'date'
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

    /**
     * Criado na criação do pedido(APENAS NESSE MOMENTO)
     *
     * @param $data
     * @param $order_id
     * @return mixed
     */
    public function createNewStatus($data, $order_id)
    {
        if($this->deParaStatus($data->status) == 3){
            $this->create([
                'order_id'          => $order_id,
                'code'              => $data->id,
                'status'            => 1,
                'reference_order_id'=> $order_id,
                'date'              => date('Y-m-d H:i:s', strtotime($data->date_created))
            ]);
        }
        $this->create([
            'order_id'          => $order_id,
            'code'              => $data->id,
            'status'            => $this->deParaStatus($data->status),
            'reference_order_id'=> $order_id,
            'date'              => date('Y-m-d H:i:s', strtotime($data->date_created))
        ]);
        if($this->deParaStatus($data->status) == 3){
            $this->create([
                'order_id'          => $order_id,
                'code'              => $data->id,
                'status'            => 50,
                'reference_order_id'=> $order_id,
                'date'              => date('Y-m-d H:i:s', strtotime($data->date_created))
            ]);
        }
    }

    public function updateStatus($data)
    {
        $create = $this->create($data);
        return $create;
    }

    public function getStatusOrder(int $order_id)
    {
        return $this->where('order_id', $order_id)->get();
    }

    public function getLastStatus(int $order_id)
    {
        return $this
                ->where('order_id', $order_id)
                ->whereNotIn('order_statuses.status', [4,5])
                ->orderBy('id', 'desc')
                ->first();
    }

    public function deParaStatus($status)
    {
        switch ($status) {
            case 'pending':
                return 1;
            case 'in_process':
                return 2;
            case 'approved':
            case 'authorized':
                return 3;
            case 'in_mediation':
                return 5;
            case 'rejected':
            case 'cancelled':
                return 7;
            case 'refunded':
            case 'charged_back':
                return 6;
            default:
                return 0;
        }
    }

    public function getCountStatusForOrderAndStatusId(int $order_id, int $status)
    {
        return $this->where(['order_id' => $order_id, 'status' => $status])->count();
    }

    public function getStatusByOrder(int $order_id, array $status)
    {
        return $this
                ->where('order_id', $order_id)
                ->whereIn('status', $status)
                ->count();
    }
}
