<?php

namespace App\Http\Controllers\User;

use App\Events\Chat\SendMessage;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;

class ChatController extends Controller
{
    private $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function animals()
    {
//        DB::enableQueryLog();
//        dd(DB::getQueryLog());

        return view('user.chat.index');
    }

    public function getUsers()
    {
        $userId = auth()->guard('client')->user()->id;
        $users = $this->message->getMessagesUser($userId);
        foreach ($users as $key => $user) {
            $users[$key]['no_read'] = $this->message->getMessageNotRead($user['id'], $userId, $user['animal_id']);
        }

        return response()->json([
            'users' => $users,
            'userLogged' => $userId
        ], Response::HTTP_OK);
    }

    public function getMessage(Request $request)
    {
        $userFrom   = auth()->guard('client')->user()->id;
        $userTo     = (int)$request->userId;
        $animalId   = (int)$request->animalId;

        $this->message->setAllMessagesRead($userTo, $userFrom, $animalId);

        return response()->json(
            $this->message->getMessages($userTo, $userFrom, $animalId)
        , Response::HTTP_OK);

    }

    public function sendMessage(Request $request)
    {

        $userFrom   = auth()->guard('client')->user()->id;

        $userTo     = $request->userTo;
        $animalTo   = $request->animalTo;
        $content    = filter_var($request['content'], FILTER_SANITIZE_STRIPPED);

        $message = new Message();

        $message->from      = $userFrom;
        $message->to        = $userTo;
        $message->animal_id = $animalTo;
        $message->content   = $content;

        $message->save();

        return response()->json(null, Response::HTTP_OK);
    }

    public function getNewMessages(Request $request)
    {
        //DB::enableQueryLog();

        $userTo = auth()->guard('client')->user()->id;
        $usersAnimalsFrom = $request->usersAnimals;

        if (!$usersAnimalsFrom) return response()->json(null, Response::HTTP_OK);

        return response()->json($this->message->getMessagesNotReadUserLastMinute($userTo, $usersAnimalsFrom), Response::HTTP_OK);

        //DB::getQueryLog()

    }

    public function getNewMessageConversation(Request $request)
    {
        $userTo   = auth()->guard('client')->user()->id;

        $userFrom = $request->user;
        $animalTo = $request->animal;

        $responseMessages = $this->message->getNewMessagesNotReadLastMinuteConversation($userTo, $userFrom, $animalTo);

        if ($responseMessages) {
            $this->message->setAllMessagesRead($userFrom, $userTo, $animalTo);
        }

        return response()->json($responseMessages, Response::HTTP_OK);
    }
}
