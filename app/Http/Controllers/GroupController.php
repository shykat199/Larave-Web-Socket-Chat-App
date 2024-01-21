<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;


class GroupController extends Controller
{

    public function index()
    {
        $allGroups = GroupUser::with('getGroup')
            ->where('user_id','=',\Auth::user()->id)->get();

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
                'status' => 1,
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

    public function getGroupInformation($slug)
    {
        $getGroupInformation = Group::where('slug', '=', $slug)->first();

        if ($getGroupInformation) {
            return response()->json([
                'status' => true,
                'groupInformation' => $getGroupInformation,
                'groupImage' => isset($getGroupInformation->image) && !empty($getGroupInformation->image) ? asset('storage/group-image/' . $getGroupInformation->image) : (asset('storage/group-image/group-default.png'))
            ]);
        } else {
            return response()->json([
                'status' => false,
                'groupInformation' => null
            ]);
        }
    }
}
