<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\User;
use Illuminate\Http\Request;
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
        return view('user.chat.index');
    }

    public function getUsers()
    {
        $userId = auth()->guard('client')->user()->id;
        $users = User::where('id', '!=', $userId)->get();

        return response()->json([
            'users' => $users
        ], Response::HTTP_OK);
    }

    public function getMessage(Request $request)
    {
        $userFrom   = auth()->guard('client')->user()->id;
        $userTo     = (int)$request->userId;
        $animalId   = (int)$request->animalId;

        return response()->json(
            $this->message->getMessages($userTo, $userFrom, $animalId)
        , Response::HTTP_OK);

    }

    public function sendMessage(Request $request)
    {

        $userFrom   = auth()->guard('client')->user()->id;

        $userTo     = $request->userTo;
        $animalTo   = $request->animalTo;
        $content    = filter_var($request->content, FILTER_SANITIZE_STRIPPED);

        $message = new Message();

        $message->from      = $userFrom;
        $message->to        = $userTo;
        $message->animal_id = $animalTo;
        $message->content   = $content;

        $message->save();

        return response()->json(null, Response::HTTP_OK);
    }
}
