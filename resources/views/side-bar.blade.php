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
            <a href="#" class="list-group-item list-group-item-action border-0 user-list" data-userId="{{$user->id}}" data-userName="{{$user->name}}"
               data-currentUserImage="{{$currentUserImage}}" data-userImage="{{isset($user->user_image) && !empty($user->user_image) ? (asset('storage/user-image/'.$user->user_image)) : ('https://bootdey.com/img/Content/avatar/avatar5.png')}}">
                <div class="badge bg-success float-right">{{$user->id}}</div>
                <div class="d-flex align-items-start">
                    <img
                        src="{{isset($user->user_image) && !empty($user->user_image) ?asset('storage/user-image/'.$user->user_image):('https://bootdey.com/img/Content/avatar/avatar5.png')}}"
                        class="rounded-circle mr-1"
                        alt="Vanessa Tucker"
                        width="40"
                        height="40"
                    />
                    <div class="flex-grow-1 ml-3">
                        {{$user->name}}
                        <div class="small">
                            <span id="{{$user->id}}-status" class="fas fa-circle chat-offline">&nbsp;Offline</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <span>No User Found</span>
        @endforelse

    @else
        <a href="#" class="list-group-item list-group-item-action border-0 mb-3">
            <div class="badge bg-success float-right"></div>
            <div class="d-flex align-items-start">
                <div class="flex-grow-1 ml-3">
                    <div class="small">
                        <button  class="btn btn-primary create-group" data-toggle="modal" data-target=".bd-example-modal-lg">
                            Create Group &nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        </a>

        @forelse($allGroups as $user)
            <a href="#" class="list-group-item list-group-item-action border-0 user-list" data-userId="{{$user->id}}" data-userName="{{$user->name}}"
               data-currentUserImage="{{@$currentUserImage}}" data-userImage="{{isset($user->image) && !empty($user->image) ? (asset('storage/group-image/'.$user->image)) : ('https://bootdey.com/img/Content/avatar/avatar5.png')}}">
{{--                <div class="badge bg-success float-right">{{$user->id}}</div>--}}
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
            <span>No User Found</span>
        @endforelse

    @endif

    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Create New Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('add-group') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('post')

                        <div>
                            <x-input-label for="name" :value="__('Group Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Short Description')" />
                            <textarea id="message" name="short_description" rows="4" class="block p-2.5 w-full text-sm text-dark  rounded-lg border  focus:ring-indigo-500  dark:border-gray-600 dark:placeholder-gray-400 dark:text-white focus:ring-indigo-500" placeholder="Your message..."></textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />

                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Long Description')" />
                            <textarea id="message" name="long_description" rows="6" class="block p-2.5 w-full text-sm text-dark  rounded-lg border  focus:ring-indigo-500  dark:border-gray-600 dark:placeholder-gray-400 dark:text-white focus:ring-indigo-500" placeholder="Your message..."></textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />

                        </div>

                        <div>
                            <x-input-label for="name" :value="__('Image')" />
                            <div class="groupImage">
                                <input type="file" name="userImage" class="dropify"
                                       data-height="300"
                                       data-default-file="" />
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

    <hr class="d-block d-lg-none mt-1 mb-0"/>
</div>
