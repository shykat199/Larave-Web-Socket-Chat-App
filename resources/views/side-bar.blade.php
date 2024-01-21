<div class="col-12 col-lg-5 col-xl-3 border-right" style="min-height:580px">
    <div class="px-4  d-md-block">
        <div class="d-flex align-items-center">
            <div class="flex-grow-1">
                <input
                    type="text"
                    class="form-control my-3"
                    placeholder="Search..."
                />
            </div>
        </div>
    </div>
    @if(Request::segment(1) == 'dashboard')
        @php
            $currentUser = \App\Models\User::select('name','user_image')->where('id','=',Auth::id())->first();
            $currentUserImage = isset($currentUser->user_image) && !empty($currentUser->user_image) ? (asset('storage/user-image/'.$currentUser->user_image)) : 'https://bootdey.com/img/Content/avatar/avatar1.png';
        @endphp
        @forelse($allUsers as $user)
            <a href="#" class="list-group-item list-group-item-action border-0
            all-group" style="min-height: 50px" data-userid="3" data-username="JavaScript" data-currentuserimage="" data-userimage="http://127.0.0.1:8000/storage/group-image/bbe13d21-bef7-3658-bd34-f6f3a8bef16a.jpeg">

                <div class="badge  float-right">

                    <button class="rounded-full h-5 w-5 bg-sky-500 text-white" disabled="">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="d-flex align-items-start">
                    <img src="http://127.0.0.1:8000/storage/group-image/bbe13d21-bef7-3658-bd34-f6f3a8bef16a.jpeg" class="rounded-circle mr-1" alt="Vanessa Tucker" width="40" height="40">
                    <div class="flex-grow-1 ml-3">
                        JavaScript
                    </div>
                </div>
            </a>
        @empty
            <span>No User Found</span>
        @endforelse


    @elseif(Request::segment(1) == 'group')

        <a href="javascript:void(0)" class="list-group-item list-group-item-action border-0 mb-3">
            <div class="badge bg-success float-right"></div>
            <div class="d-flex align-items-start">
                <div class="flex-grow-1 ml-3">
                    <div class="small">
                        <button class="btn btn-primary create-group" data-toggle="modal"
                                data-target=".bd-example-modal-lg">
                            Create Group &nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        </a>
        @forelse($allGroups as $user)
            <a href="#" class="list-group-item list-group-item-action border-0
            all-group" style="min-height: 50px" data-userId="{{$user->getGroup->creator_id}}" data-groupId="{{$user->getGroup->id}}" data-userName="{{$user->getGroup->name}}" data-currentUserImage="{{asset('storage/group-image/'.$user->getGroup->image)}}"
               data-userImage="{{asset('storage/group-image/'.$user->getGroup->image)}}">

                <div class="badge bg-success float-right">
                    10
                </div>
                <div class="d-flex align-items-start">
                    <img src="{{asset('storage/group-image/'.$user->getGroup->image)}}" class="rounded-circle mr-1" alt="{{$user->getGroup->name}}" width="40" height="40">
                    <div class="flex-grow-1 ml-3">
                        {{$user->getGroup->name}}
                    </div>
                </div>
            </a>

        @empty
            <div class="d-flex align-items-start">
                <p>No Group Found</p>
            </div>
        @endforelse

    @else
        <a href="javascript:void(0)" class="list-group-item list-group-item-action border-0 mb-3">
            <div class="badge bg-success float-right"></div>
            <div class="d-flex align-items-start">
                <div class="flex-grow-1 ml-3">
                    <div class="small">
                        <button class="btn btn-primary create-group" data-toggle="modal"
                                data-target=".bd-example-modal-lg">
                            Create Group &nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        </a>

        @forelse($allGroups as $user)
            @php
                $groupRequestedUser = $user->users->find(Auth::id());
            @endphp
            <a href="#" class="list-group-item list-group-item-action border-0
            {{Request::segment(1) != null && Request::segment(1) == 'all-group' ? ('all-group'):('block')}}"
               style="min-height: 50px"
               data-userId="{{$user->id}}" data-userName="{{$user->name}}"
               data-currentUserImage="{{@$currentUserImage}}"
               data-userImage="{{isset($user->image) && !empty($user->image) ? (asset('storage/group-image/'.$user->image)) : ('https://bootdey.com/img/Content/avatar/avatar5.png')}}">

                <div class="badge {{Request::segment(1) != 'all-group' ? ('bg-success') : ('')}} float-right">

                    @if(Request::segment(1) != null && Request::segment(1) == 'all-group')
                        @if($groupRequestedUser && $groupRequestedUser->pivot->status == 1)
                            <button class="rounded-full h-5 w-5 bg-sky-500 text-white" disabled>
                                <i class="fa fa-check" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="send-request rounded-full h-5 w-5 bg-sky-500 text-white" data-target=".bd-group-details-modal-lg"
                                    data-toggle="modal" data-groupSlug="{{$user->slug}}">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </button>
                        @endif
                    @else
                        10
                    @endif
                </div>
                <div class="d-flex align-items-start">
                    <img
                        src="{{isset($user->image) && !empty($user->image) ?asset('storage/group-image/'.$user->image):('https://bootdey.com/img/Content/avatar/avatar5.png')}}"
                        class="rounded-circle mr-1"
                        alt="Vanessa Tucker"
                        width="40"
                        height="40"
                    />
                    <div class="flex-grow-1 ml-3">
                        {{$user->name}}
                    </div>
                </div>
            </a>

        @empty
            <div class="d-flex align-items-start">
                <p>No Group Found</p>
            </div>
        @endforelse

    @endif

    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Create New Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('add-group') }}" class="mt-6 space-y-6"
                          enctype="multipart/form-data">
                        @csrf
                        @method('post')

                        <div>
                            <x-input-label for="name" :value="__('Group Name')"/>
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                          :value="old('name')" required autofocus autocomplete="name"/>
                            <x-input-error class="mt-2" :messages="$errors->get('name')"/>
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Short Description')"/>
                            <textarea id="message" name="short_description" rows="4"
                                      class="block p-2.5 w-full text-sm text-dark  rounded-lg border  focus:ring-indigo-500  dark:border-gray-600 dark:placeholder-gray-400 dark:text-white focus:ring-indigo-500"
                                      placeholder="Your message..."></textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('email')"/>

                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Long Description')"/>
                            <textarea id="message" name="long_description" rows="6"
                                      class="block p-2.5 w-full text-sm text-dark  rounded-lg border  focus:ring-indigo-500  dark:border-gray-600 dark:placeholder-gray-400 dark:text-white focus:ring-indigo-500"
                                      placeholder="Your message..."></textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('email')"/>

                        </div>

                        <div>
                            <x-input-label for="name" :value="__('Image')"/>
                            <div class="groupImage">
                                <input type="file" name="userImage" class="dropify"
                                       data-height="300"
                                       data-default-file=""/>
                            </div>

                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-group-details-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Group Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form  class="mt-6 space-y-6">

                        <div>
                            <x-input-label for="name" :value="__('Group Name')"/>
                            <x-text-input id="g_name" name="name" type="text" class="mt-1 block w-full"
                                          :value="old('name')" readonly autofocus autocomplete="name"/>

                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Short Description')"/>
                            <textarea id="g_short_description" name="short_description" rows="4" readonly
                                      class="block p-2.5 w-full text-sm text-dark  rounded-lg border  focus:ring-indigo-500  dark:border-gray-600 dark:placeholder-gray-400 dark:text-white focus:ring-indigo-500"
                                      placeholder="Your message..."></textarea>


                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Long Description')"/>
                            <textarea id="g_long_description" name="long_description" rows="6" readonly
                                      class="block p-2.5 w-full text-sm text-dark  rounded-lg border  focus:ring-indigo-500  dark:border-gray-600 dark:placeholder-gray-400 dark:text-white focus:ring-indigo-500"
                                      placeholder="Your message..."></textarea>


                        </div>

{{--                        <div>--}}
{{--                            <x-input-label for="name" :value="__('Image')"/>--}}
{{--                            <div class="groupImage">--}}
{{--                               <img id="g_image" class="mt-2" src="" style="max-height: 150px; max-width: 150px;">--}}
{{--                            </div>--}}

{{--                        </div>--}}

                        <div id="send-group-request" class="flex items-center gap-4" data-groupSlug="">
                            <x-primary-button>{{ __('Send Request') }}</x-primary-button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <hr class="d-block d-lg-none mt-1 mb-0"/>
</div>
