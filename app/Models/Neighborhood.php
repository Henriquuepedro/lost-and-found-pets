<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'city_id'
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

    public function getAllNeigh()
    {
        return $this->orderBy('name', 'ASC')->get();
    }

    public function getNeighByCity($city)
    {
        return $this->where('city_id', $city)->orderBy('name', 'ASC')->get();
    }

    public function getNeigh($id)
    {
        return $this->find($id);
    }
}
