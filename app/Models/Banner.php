<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path', 'order'
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

    public function getBanners($id = null)
    {
        if($id) return $this->find($id);

        return $this->orderBy('order')->get();
    }

    public function getLastNumberOrder()
    {
        $lastBanner = $this->orderBy('order', 'DESC')->first();

        if(!$lastBanner) return 0;

        return $lastBanner->order;
    }

    public function insert($data)
    {
        return $this->create($data);
    }

    public function remove($id)
    {
        return $this->where('id', $id)->delete();
    }

    public function edit($data, $id)
    {
        return $this->where('id', $id)->update($data);
    }

    public function rearrangeOrder()
    {
        $banners = $this->orderBy('order')->get();
        $order = 0;
        $updated = true;

        foreach ($banners as $banner) {
            $order++;
            $update = $this->where('id', $banner['id'])->update(['order' => $order]);
            if(!$update) $updated = false;
        }

        return $updated;

    }
}
