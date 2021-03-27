<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'tel'
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

    public function getCountClients()
    {
        return $this->count();
    }

    public function getNameClient($cod)
    {
        return $this->where('id', $cod)->first();
    }

    public function getAllClients()
    {
        return $this->get();
    }

    public function getClient($id)
    {
        return $this->find($id);
    }

    public function getClientForEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function updateLastLogin($userId)
    {
        return $this->where('id', $userId)->update(['last_login' => date('Y-m-d H:i:s')]);
    }

    public function getLastLogins($limit = 5)
    {
        return $this->whereNotNull('last_login')->orderByDesc('last_login')->limit($limit)->get();
    }
}
