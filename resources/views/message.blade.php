<div class="col-12 col-lg-7 col-xl-9 message-body d-none">
    <div class="py-2 px-4 border-bottom d-none d-lg-block">
        <div class="d-flex align-items-center py-1">
            <div class="position-relative">
                <img id="user-image"
                    src="https://bootdey.com/img/Content/avatar/avatar3.png"
                    class="rounded-circle mr-1"
                    alt="Sharon Lessman"
                    width="40"
                    height="40"
                />
            </div>
            <div class="flex-grow-1 pl-3">
                <strong id="sender-name"></strong>
                <div class="text-muted small">
                    <em id="typingIndicator" class="d-none">Typing...</em>
                </div>
            </div>
            <div>
                <button class="btn btn-primary btn-lg mr-1 px-3">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-phone feather-lg"
                    >
                        <path
                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"
                        ></path>
                    </svg>
                </button>
                <button
                    class="btn btn-info btn-lg mr-1 px-3 d-none d-md-inline-block"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-video feather-lg"
                    >
                        <polygon points="23 7 16 12 23 17 23 7"></polygon>
                        <rect
                            x="1"
                            y="5"
                            width="15"
                            height="14"
                            rx="2"
                            ry="2"
                        ></rect>
                    </svg>
                </button>
                <button class="btn btn-light border btn-lg px-3">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-more-horizontal feather-lg"
                    >
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="19" cy="12" r="1"></circle>
                        <circle cx="5" cy="12" r="1"></circle>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div class="position-relative">
        <div class="chat-messages p-4" style="min-height: 450px; max-height: 420px;" data-offset="10" data-totalChat="">


        </div>
    </div>
    @php
        $currentUser = \App\Models\User::select('name','user_image')->where('id','=',Auth::id())->first();
        $currentUserImage = isset($currentUser->user_image) && !empty($currentUser->user_image) ? (asset('storage/user-image/'.$currentUser->user_image)) : 'https://bootdey.com/img/Content/avatar/avatar1.png';
    @endphp
    <form action="" id="chat-form">
    <div class="flex-grow-0 py-3 px-4 border-top">
        <div class="input-group">

                <input
                    type="text"
                    class="form-control"
                    placeholder="Type your message...."
                    name="message"
                    data-currentUserImage="{{$currentUserImage}}"
                    id="message"
                />
                <button class="btn btn-primary">Send</button>
        </div>
    </div>
    </form>
</div>
