<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['from', 'to', 'animal_id', 'content'];

    public function getMessages($userTo, $userFrom, $animalId)
    {
        return $this->where(
            function ($query) use ($userTo, $userFrom, $animalId) {
                $query->where([
                    'from' => $userFrom,
                    'to' => $userTo,
                    'animal_id' => $animalId
                ]);
            }
        )->orWhere(
            function ($query) use ($userTo, $userFrom, $animalId) {
                $query->where([
                    'from' => $userTo,
                    'to' => $userFrom,
                    'animal_id' => $animalId
                ]);
            }
        )->orderBy('created_at','ASC')->get();
    }
}
