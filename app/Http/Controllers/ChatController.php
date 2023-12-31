<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
use App\Events\SendMessageEvent;
use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function privateChannel()
    {
        event(new ChatEvent('Hello This is test'));

    }

    public function saveChat(Request $request)
    {

        try {

            $newChat = Chat::create([
                'sender_id' => $request->post('sender_id'),
                'receiver_id' => $request->post('receiver_id'),
                'messages' => $request->post('message')
            ]);
            if ($newChat){
                $getUserInformation = Chat::with(['senderDetails' => function ($select) {
                    $select->select('name', 'email', 'user_image', 'id');
                }, 'reveiverDetails' => function ($select) {
                    $select->select('name', 'email', 'user_image', 'id');
                }])->where('id', '=', $newChat->id)->first();

                event(new SendMessageEvent($newChat, $getUserInformation));

                return response()->json([
                    'success' => true,
                    'data' => $newChat
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'msg' => 'Something wrong try again.'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }
}
