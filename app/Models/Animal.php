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
        'city',
        'neigh',
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

    public function getAllAnimals($city, $neigh, $date, $order)
    {
        $query = $this->select('animals.name', 'species', 'color', 'size', 'disappearance_date', 'animals.created_at', 'path', 'animals.id', 'observation', 'place', 'neighborhoods.name as neigh_name')
            ->leftJoin('animal_images',function ($join) {
                $join->on(function ($queryone){
                    $queryone->on('animal_images.animal_id', 'animals.id');
                    $queryone->where('animal_images.primary', true);
                });
            })
            ->leftJoin('neighborhoods', 'neighborhoods.id', '=', 'animals.neigh');

        if ($city) $query->where('animals.city', $city);
        if ($neigh) $query->where('animals.neigh', $neigh);
        if ($date) $query->where('animals.updated_at', '>', $date);

        return $query->orderBy($order[0], $order[1])->get();
    }

    public function getAnimal($id, $user_id = null)
    {
        if (!$user_id)
            return $this->find($id);

        return $this->where(['id' => $id, 'user_created' => $user_id])->first();
    }

    public function edit($data, $id)
    {
        return $this->where('id', $id)->update($data);
    }

    public function remove($animal_id)
    {
        return $this->where('id', $animal_id)->delete();
    }
}
