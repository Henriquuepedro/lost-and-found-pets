<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimony extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'testimony', 'picture', 'rate', 'user_id', 'approved', 'primary'
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

    public function getTestimony($id = null)
    {
        if($id) return $this->find($id);

        return $this->orderBy('name')->get();
    }

    public function edit($data, $id)
    {
        return $this->where('id', $id)->update($data);
    }

    public function remove($id)
    {
        return $this->where('id', $id)->delete();
    }

    public function insert($data)
    {
        $create = $this->create($data);
        return $create->id;
    }
}
