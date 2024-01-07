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
    @forelse($allUsers as $user)
        <a href="#" class="list-group-item list-group-item-action border-0 user-list" data-userId="{{$user->id}}" data-userName="{{$user->name}}"
           data-userImage="{{isset($user->user_image) && !empty($user->user_image) ? (asset('storage/user-image/'.$user->user_image)) : ('https://bootdey.com/img/Content/avatar/avatar5.png')}}">
            <div class="badge bg-success float-right">5</div>
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

    <hr class="d-block d-lg-none mt-1 mb-0"/>
</div>
