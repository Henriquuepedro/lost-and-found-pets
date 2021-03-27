<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'picture', 'rate', 'user_id', 'order_id', 'product_id', 'name_user', 'approved'
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

    public function insert($data)
    {
        return $this->create($data);
    }

    public function existRateOrder($order)
    {
        return $this->where('order_id', $order)->count() == 0 ? false : true;
    }

    public function getRates($id = null)
    {
        if($id) return $this->find($id);

        return $this->get();
    }

    public function getRatesProduct($product_id)
    {
        return $this->where(['product_id' => $product_id, 'approved' => 1])->orderBy('created_at', 'DESC')->get();
    }

    public function edit($data, $id)
    {
        return $this->where('id', $id)->update($data);
    }

    public function getRateAdmin($id)
    {
        return $this->where(['id' => $id, 'user_id' => 0, 'order_id' => 0])->first();
    }

    public function remove($id)
    {
        return $this->where('id', $id)->delete();
    }
}
