<?php

namespace App\Http\Controllers;

use App\Events\SendOffer;
use App\Events\WebRTCEvent;
use Illuminate\Http\Request;

class VideoAudioController extends Controller
{

    public function createOffer(Request $request)
    {
        if ($request->ajax()){
            $data['sender_id']=$request->get('sender_id');
            $data['receiver_id']=$request->get('receiver_id');

            broadcast(new WebRTCEvent($data))->toOthers();

        }
    }

    public function handleOffer(Request $request)
    {
        if ($request->ajax()){
            $data['offer']=$request->post('offer');
            $data['sender_id']=$request->post('sender_id');
            $data['receiver_id']=$request->post('receiver_id');
            $data['connected_userId']=$request->post('connected_userId');
            $data['type']=$request->post('type');

            broadcast(new SendOffer($data))->toOthers();
        }
    }

    public function handleCandidate(Request $request)
    {
        $data['sender_id'] = $request->post('sender_id');
        $data['receiver_id'] = $request->post('receiver_id');
        $data['type'] = $request->post('type');
        $data['offer'] = $request->post('offer');

        dd($data);
    }

    public function createAnswer(Request $request)
    {

    }
}
