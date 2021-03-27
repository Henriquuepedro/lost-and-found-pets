<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'active', 'type', 'value'
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

    public function getPromotions($id = null)
    {
        if ($id) return $this->find($id);

        return $this->get();
    }

    public function insert($data)
    {
        return $this->create($data);
    }

    public function getPromotionForTypeActive($type, $id = null)
    {
        if ($id) return $this->where(['active' => 1, 'type' => $type])->whereNotIn('id', [$id])->count() == 0;

        return $this->where(['active' => 1, 'type' => $type])->count() == 0;
    }

    public function edit($data, $id)
    {
        return $this->where('id', $id)->update($data);
    }

    public function getPromotionForType($type)
    {
        return $this->where(['type' => $type, 'active' => 1])->first();
    }

    public function remove($promotion_id)
    {
        $delete = $this->where('id', $promotion_id)->delete();

        return $delete ? true : false;
    }
}
