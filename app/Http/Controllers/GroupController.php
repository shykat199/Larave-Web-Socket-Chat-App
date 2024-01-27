<?php

namespace App\Http\Controllers;

use App\Events\GroupMessageEvent;
use App\Models\Chat;
use App\Models\Group;
use App\Models\GroupChat;
use App\Models\GroupUser;
use App\Models\User;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use mysql_xdevapi\Exception;
use RealRashid\SweetAlert\Facades\Alert;


class GroupController extends Controller
{

    public function index()
    {
        $allGroups = GroupUser::with('getGroup')
            ->where('status', '=', 1)
            ->where('user_id', '=', \Auth::user()->id)->get();

        return view('group_dashboard', compact('allGroups'));
    }

    public function allGroup()
    {
        $allGroups = Group::with('users')
            ->where('status', '=', 1)
            ->where('creator_id', '!=', \Auth::user()->id)
            ->get();

//        $userId = Auth::id();
//
//        $allGroups = DB::table('groups')
//            ->select('groups.*', 'group_users.status as request_status')
//            ->leftJoin('group_users', function ($join) use ($userId) {
//                $join->on('groups.id', '=', 'group_users.group_id')
//                    ->where('group_users.user_id', '=', $userId);
//            })
//            ->where('groups.status', '=', 1)
//            ->where('groups.creator_id', '!=', $userId)
//            ->get();
//        dd($allGroups);
        return view('all_group_dashboard', compact('allGroups'));
    }

    public function store(Request $request)
    {
        $fileName = null;
        if ($request->has('userImage')) {
            $fileName = Uuid::uuid() . '.' . $request->file('userImage')->getClientOriginalExtension();
            $file = Storage::put('/public/group-image/' . $fileName, file_get_contents($request->file('userImage')));
        }

        $groupData = [
            'name' => $request->post('name'),
            'creator_id' => \Auth::user()->id,
            'slug' => $this->generateGroupSlug(Uuid::uuid() . $request->post('name')),
            'short_description' => $request->post('short_description'),
            'title' => $request->post('long_description'),
            'image' => $fileName,
            'status' => 1
        ];

        $storeData = Group::create($groupData);
        if ($storeData) {
            $storeGroupUser = GroupUser::create([
                'group_id' => $storeData->id,
                'user_id' => $storeData->creator_id,
                'status' => 1,
            ]);
            if ($storeGroupUser) {
                toast('New Group Created', 'success');
                return redirect()->back();
            }
        } else {
            toast('Something Wrong', 'danger');
            return redirect()->back();
        }
    }

    private function generateGroupSlug($name)
    {
        $slug = strtolower($name);
        $slug = str_replace(' ', '-', $slug);
        $slug = preg_replace('/[^a-zA-Z0-9\-]/', '', $slug);
        return $slug;
    }

    public function sendGroupRequest(Request $request)
    {
        $getGroup = Group::where('slug', '=', $request->post('slug'))->first();

        if (!empty($getGroup)) {
            $requestedData = [
                'group_id' => $getGroup->id,
                'user_id' => \Auth::user()->id,
                'status' => 0,
            ];
            $storeData = GroupUser::create($requestedData);
            if ($storeData) {
                return response()->json([
                    'status' => true,
                    'msg' => 'Group request has send successfully.',
                ]);
            }
        } else {
            toast('Something Wrong ! Try again !', 'danger');
            return redirect()->back();
        }
    }

    public function getGroupRequestList($id)
    {
        $getRequestList = Group::with(['users' => function ($query) {
            $query->wherePivot('status', '=', 0);
        }])->where('id', '=', $id)->first();

        $html = '';
        if (!empty($getRequestList->users)) {

            $html .= '
                <form id="groupRequestedForm"  class="mt-6 space-y-6">
                <input name="groupId" type="hidden" value="' . $id . '">
                ';
            foreach ($getRequestList->users as $user) {
                $html .= '<div class="form-group">
                      <input name="userIds" value="' . $user->id . '" type="checkbox" id="user' . $user->id . '">
                      <label for="html">' . $user->name . '</label>
                    </div>';
            }
            $html .= '<button type="submit" id="requestApproveBtn" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md
                    font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900
                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Approve
                    </button>';
            $html .= '</form>';
            return response()->json([
                'status' => true,
                'html' => $html,
            ]);
        } else {
            return response()->json([
                'status' => true,
                'html' => '<span>No User Found</span>',
            ]);
        }


    }

    public function updateGroupRequestList(Request $request)
    {
        $groupIds = $request->post('groupId');
        $userIds = $request->post('userIds');

        $updateData = GroupUser::whereIn('user_id', $userIds)
            ->where('group_id', $groupIds)
            ->update([
                'status' => 1
            ]);
        if ($updateData) {
            return response()->json([
                'status' => true,
                'msg' => 'Request has been Approved',
            ]);
        }

    }

    public function getGroupInformation($slug)
    {
        $getGroupInformation = Group::with('users')->where('slug', '=', $slug)->first();

        if ($getGroupInformation && $getGroupInformation->users->contains('id', Auth::user()->id)) {

            return response()->json([
                'status' => false,
                'groupInformation' => $getGroupInformation
            ]);
        } else {
            return response()->json([
                'status' => true,
                'groupInformation' => $getGroupInformation,
                'groupImage' => isset($getGroupInformation->image) && !empty($getGroupInformation->image) ? asset('storage/group-image/' . $getGroupInformation->image) : (asset('storage/group-image/group-default.png'))
            ]);
        }
    }

    public function checkGroupUserAccess(Request $request)
    {
        $userId=$request->get('userId');
        $groupId=$request->get('groupId');
        try {
            $userInGroup=GroupUser::where('user_id','=',$userId)
                ->where('group_id','=',$groupId)
                ->where('status','=',1)->first();

            if (!empty($userInGroup)){
                return response()->json([
                    'status'=>true,
                    'msg'=>'Ok'
                ]);
            }else{
                return response()->json([
                    'status'=>false,
                    'msg'=>'You are not authorized.'
                ]);
            }

        }catch (Exception $e){

        }
    }


    public function saveGroupChat(Request $request)
    {
        try {
            if ($request->ajax()) {
                $message = $request->post('message');
                $sender_id = $request->post('sender_id');
                $group_id = $request->post('group_id');

                $groupMessageData = [
                    'group_id' => $group_id,
                    'sender_id' => $sender_id,
                    'message' => $message,
                ];

                if ($groupMessageData) {

                    $groupChatInfo = GroupChat::create($groupMessageData);

                    $getGroupWithUserInfo = GroupChat::with(['getMessageWithUserInfo' => function ($query) {
                        $query->select('id', 'email', 'user_image', 'name');
                    }])
                        ->where('id', '=', $groupChatInfo->id)
                        ->where('group_id', '=', $group_id)
                        ->first();

                    if ($getGroupWithUserInfo->getMessageWithUserInfo) {
                        $getGroupWithUserInfo->getMessageWithUserInfo->user_image = asset('storage/user-image/' . $getGroupWithUserInfo->getMessageWithUserInfo->user_image);
                    } else {
                        $getGroupWithUserInfo->getMessageWithUserInfo->user_image = asset('storage/user-image/user-default_512x512.png');
                    }
                    $userImage=$getGroupWithUserInfo->getMessageWithUserInfo->user_image;
                    event(new GroupMessageEvent($getGroupWithUserInfo,$userImage));
                    return response()->json([
                        'success' => true,
                        'groupChatInfo' => $getGroupWithUserInfo
                    ]);

                } else {
                    return response()->json([
                        'success' => false,
                        'msg' => 'Something wrong try again.'
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }

    }

    public function loadGroupOldChat(Request $request)
    {
        $groupId = $request->get('groupId');

        try {
            $getOldChatList = GroupChat::with(['getGroupMessageWithUserInfo' => function ($query) {
                $query->select('id', 'email', 'user_image', 'name');
            }])
                ->where('group_id', '=', $groupId)->get();

            $getOldChatList->each(function ($chat){

                $chat->getGroupMessageWithUserInfo->user_image = isset($chat->getGroupMessageWithUserInfo->user_image) && !empty($chat->getGroupMessageWithUserInfo->user_image)
                    ? (filter_var($chat->getGroupMessageWithUserInfo->user_image, FILTER_VALIDATE_URL) ? $chat->getGroupMessageWithUserInfo->user_image : asset('storage/user-image/' . $chat->getGroupMessageWithUserInfo->user_image))
                    : asset('storage/user-image/user-default_512x512.png');
            });

            return response()->json([
                'status' => true,
                'chatList' => $getOldChatList
            ]);

        }catch (\Exception $e){
            return response()->json([
                'status' => false,
                'msg' => $e->getMessage()
            ]);
        }

        dd($getOldChatList);

    }

}
