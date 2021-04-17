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

    public function getMessagesUser($user_id)
    {
        return $this->select('users.*', 'messages.animal_id', 'animals.name as animal_name')
                    ->join('users',function ($join) use ($user_id) {
                        $join->on(function ($queryone){
                            $queryone->on('users.id', '=', 'messages.from');
                            $queryone->orWhere('users.id','=', 'messages.to');
                        })
                        ->where('users.id', '!=', $user_id);
                    })
                    ->join('animals', 'messages.animal_id', '=', 'animals.id')
                    ->where('users.id', '!=', $user_id)
                    ->orderBy('messages.created_at','DESC')
                    ->groupBy('users.id')
                    ->get();
    }

    public function getMessageNotRead($from, $to, $animal_id)
    {
        return $this->where(['from' => $from, 'to' => $to, 'animal_id' => $animal_id, 'read' => 0])->count() ? true : false;
    }

    public function setAllMessagesRead($from, $to, $animal_id)
    {
        $this->where(['from' => $from, 'to' => $to, 'animal_id' => $animal_id, 'read' => 0])->update(['read' => true]);
    }

    public function getMessagesNotReadUserLastMinute($userTo, $usersAnimalsFrom)
    {
        return $this->select('from', 'animal_id')
                    ->where([
                        'to' => $userTo,
                        'read' => 0,
                        ['created_at', '>', date('Y-m-d H:i:s', strtotime('-1 minute', time()))]
                    ])
                    ->where(function ($query) use ($usersAnimalsFrom) {
                        foreach ($usersAnimalsFrom as $userAnimal)
                            $query->orWhere($userAnimal);
                    })
                    ->groupBy('from')
                    ->get();
    }

    public function getNewMessagesNotReadLastMinuteConversation($userTo, $userFrom, $animalTo)
    {
        return $this->select('id', 'content', 'created_at')
            ->where([
                'to'        => $userTo,
                'from'      => $userFrom,
                'animal_id' => $animalTo,
                'read'      => 0,
                ['created_at', '>', date('Y-m-d H:i:s', strtotime('-1 minute', time()))]
            ])
            ->get();
    }

    public function removeByAnimalId($animal_id)
    {
        return $this->where('animal_id', $animal_id)->delete();
    }
}
