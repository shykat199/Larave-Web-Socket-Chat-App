<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Group;
use App\Models\User;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;


class GroupController extends Controller
{

    public function index()
    {
        $allGroups = Group::where('creator_id', '=', \Auth::user()->id)
            ->where('status','=',1)->get();

        return view('group_dashboard', compact('allGroups'));
    }

    public function store(Request $request)
    {
        $fileName=null;
       if ($request->has('userImage')){
           $fileName = Uuid::uuid() . '.' . $request->file('userImage')->getClientOriginalExtension();
           $file = Storage::put('/public/group-image/' . $fileName, file_get_contents($request->file('userImage')));
       }

       $groupData=[
           'name'=>$request->post('name'),
           'creator_id'=>\Auth::user()->id,
           'short_description'=>$request->post('short_description'),
           'title'=>$request->post('long_description'),
           'image'=>$fileName,
           'status'=>1
       ];

       if ($groupData){
         $storeData= Group::create($groupData);
         if ($storeData){
             toast('New Group Created','success');
             return redirect()->back();
         }
       }

    }
}
