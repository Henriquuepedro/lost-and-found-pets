<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','picture','cep','address','number','complement','neighborhood','city','state','tel', 'email', 'password', 'name_user'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAdmin($id = null)
    {
        if($id) return $this->find($id);

        return $this->get();
    }

    public function edit($data, $id)
    {
        return $this->where('id', $id)->update($data);
    }

    public function getAdminMain()
    {
        return $this->where('main', 1)->first();
    }
}
