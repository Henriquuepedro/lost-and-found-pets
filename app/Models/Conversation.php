<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_start',
        'user_animal',
        'animal_id',
        'created_at'
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

    public function insert($data)
    {
        return $this->create($data);
    }

    /**
     * @param $user1
     * @param $user2
     * @param $animal_id
     * @return bool
     */
    public function getConversation($user1, $user2, $animal_id): bool
    {
        return $this
                ->where(function ($query) use ($user1, $user2) {
                    $query->where('user_start', $user1)
                        ->orWhere('user_start', $user2)
                        ->orWhere('user_animal', $user1)
                        ->orWhere('user_animal', $user2);
                })->where('animal_id', $animal_id)->count() > 0;
    }

    /**
     * @param $user1
     * @param $user2
     * @param $animal_id
     * @return object
     */
    public function getConversations($user)
    {
        return $this
            ->select('animals.name', 'conversations.user_start', 'conversations.user_animal', 'conversations.animal_id', 'conversations.created_at')
            ->join('animals', 'animals.id', '=', 'conversations.animal_id')
            ->where('conversations.user_start', $user)
            ->orWhere('conversations.user_animal', $user)
            ->orderBy('conversations.updated_at', 'DESC')
            ->get();
    }
}
