<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name_coupon', 'date_expired', 'products_id', 'percentage'
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

    public function getAllCoupons()
    {
        return $this->get();
    }

    public function getCoupon($coupon)
    {
        return $this->find($coupon);
    }

    public function edit($data, $id)
    {
        return $this->where('id', $id)->update($data);
    }

    public function insert($data)
    {
        return $this->create($data);
    }

    public function remove($id)
    {
        return $this->where('id', $id)->delete();
    }
}
