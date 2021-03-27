<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address', 'cep', 'number', 'complement', 'reference', 'neighborhood', 'city', 'state', 'user_id'
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

    public function getAddressClient($client)
    {
        return $this->where('user_id', $client)->get();
    }

    public function checkAddressExist($data)
    {
        return $this->where($data)->count() ? true : false;
    }
}
