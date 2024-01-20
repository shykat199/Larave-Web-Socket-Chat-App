<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
use App\Events\DeleteMessageEvent;
use App\Events\SendMessageEvent;
use App\Events\TypingEvent;
use App\Models\Chat;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;

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
            if ($newChat) {
                $getUserInformation = Chat::with(['sender' => function ($select) {
                    $select->select('name', 'email', 'user_image', 'id');
                }, 'receiver' => function ($select) {
                    $select->select('name', 'email', 'user_image', 'id');
                }])->where('id', '=', $newChat->id)->first();

                event(new SendMessageEvent($newChat, $getUserInformation));

                return response()->json([
                    'success' => true,
                    'data' => $newChat
                ]);
            } else {
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

    public function loadOldChat(Request $request)
    {
        try {
            $oldChats = Chat::with(['sender' => function ($query) {
                $query->selectRaw('id,name,user_image');
            }, 'receiver' => function ($query) {
                $query->selectRaw('id,name,user_image');
            }])
                ->where(function ($query) use ($request) {
                    $query->where('sender_id', '=', $request->post('sender_id'));
                    $query->orWhere('sender_id', '=', $request->post('receiver_id'));
                })->where(function ($query) use ($request) {
                    $query->where('receiver_id', '=', $request->post('receiver_id'));
                    $query->orWhere('receiver_id', '=', $request->post('sender_id'));
                })->orderBy('id', 'ASC')->get();

            $oldChats->each(function ($chat) {

                // Modify the user image in the sender relation
                $chat->sender->user_image = isset($chat->sender->user_image) && !empty($chat->sender->user_image)
                    ? (filter_var($chat->sender->user_image, FILTER_VALIDATE_URL) ? $chat->sender->user_image : asset('storage/user-image/' . $chat->sender->user_image))
                    : 'https://bootdey.com/img/Content/avatar/avatar1.png';


                // Modify the user image in the receiver relation
                $chat->receiver->user_image = isset($chat->receiver->user_image) && !empty($chat->receiver->user_image)
                    ? (filter_var($chat->receiver->user_image, FILTER_VALIDATE_URL) ? $chat->receiver->user_image : asset('storage/user-image/' . $chat->receiver->user_image))
                    : 'https://bootdey.com/img/Content/avatar/avatar1.png';
            });
//            dd($oldChats);
            $totalChats = Chat::where(function ($query) use ($request) {
                $query->where('sender_id', '=', $request->post('sender_id'));
                $query->orWhere('sender_id', '=', $request->post('receiver_id'));
            })->where(function ($query) use ($request) {
                $query->where('receiver_id', '=', $request->post('receiver_id'));
                $query->orWhere('receiver_id', '=', $request->post('sender_id'));
            })->get();

            return response()->json([
                'success' => true,
                'data' => $oldChats,
                'totalChat' => count($totalChats) ?? 0
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function loadMoreChat(Request $request)
    {
        try {
//            $moreChats = Chat::join('users as senders','senders.id','=','chats.sender_id')
//                ->join('users as receiver','receiver.id','=','chats.receiver_id')
//            ->where(function ($query) use($request){
//                $query->where('sender_id','=',$request->post('sender_id'));
//                $query->orWhere('sender_id','=',$request->post('receiver_id'));
//            })->where(function ($query) use ($request){
//                $query->where('receiver_id','=',$request->post('receiver_id'));
//                $query->orWhere('receiver_id','=',$request->post('sender_id'));
//            })->limit(10)->offset($request->post('offset'))->get();

            $moreChats = Chat::with(['sender:id,name,user_image', 'receiver:id,name,user_image'])
                ->where(function ($query) use ($request) {
                    $query->where('sender_id', '=', $request->post('sender_id'));
                    $query->orWhere('sender_id', '=', $request->post('receiver_id'));
                })->where(function ($query) use ($request) {
                    $query->where('receiver_id', '=', $request->post('receiver_id'));
                    $query->orWhere('receiver_id', '=', $request->post('sender_id'));
                })->limit(10)->offset($request->post('offset'))->get();


            return response()->json([
                'success' => true,
                'data' => $moreChats,
                'moreChatCount' => count($moreChats)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function startTyping($id, $id2)
    {
        $this->broadcastTypingEvent($id, $id2, true);
        return response()->json(['status' => 'success']);
    }

    protected function broadcastTypingEvent($id, $id2, $isTyping)
    {
        broadcast(new TypingEvent('Typing....', $id, $id2, $isTyping));
    }

    public function stopTyping($id, $id2)
    {
        $this->broadcastTypingEvent($id, $id2, false);
        return response()->json(['status' => 'success']);
    }


    public function deleteChat($id)
    {
        $deleteChat = Chat::find($id)->delete();
        if ($deleteChat) {
            event(new DeleteMessageEvent($id));
        }
        return response()->json([
            'status' => true,
            'msg' => 'Message Deleted Successfully.'
        ]);
    }

}
