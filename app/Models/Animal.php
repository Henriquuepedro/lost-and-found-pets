<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'user_created',
        'user_updated',
        'name',
        'species',
        'sex',
        'age',
        'size',
        'color',
        'race',
        'place',
        'disappearance_date',
        'phone_contact',
        'email_contact',
        'observation'
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

    public function getAnimals($userId)
    {
        return $this->select('name', 'species', 'color', 'size', 'disappearance_date', 'animals.created_at', 'path', 'animals.id', 'observation')
                    ->leftJoin('animal_images',function ($join) {
                        $join->on(function ($queryone){
                            $queryone->on('animal_images.animal_id', 'animals.id');
                            $queryone->where('animal_images.primary', true);
                        });
                    })
                    ->where('animals.user_created', $userId)
                    ->orderBy('animals.id', 'DESC')
                    ->get();
    }

    public function insert($data)
    {
        return $this->create($data);
    }
}
