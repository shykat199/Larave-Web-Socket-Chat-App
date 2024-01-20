$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function () {

    $('.user-list').on('click', function () {
        let getRequestedUserId = $(this).attr('data-userId')
        let getRequestedUserName = $(this).attr('data-userName')
        let getRequestedUserImage = $(this).attr('data-userImage')
        receiver_id = getRequestedUserId;
        $('.message-body').removeClass('d-none');
        $('#message').val('');
        // $('.chat-messages').html('');
        $('#sender-name').text(getRequestedUserName)
        $('#user-image').attr('src', getRequestedUserImage)

        loadOldChat(getRequestedUserName)
        scrollChat()

    });

    //save chat hear

    $('#chat-form').submit(function (e) {
        e.preventDefault();
        let message = $('#message').val();
        let currentUserImage = $('#message').attr('data-currentUserImage');

        $.ajax({
            url: '/save-chat',
            type: 'POST',
            data: {
                sender_id: sender_id,
                receiver_id: receiver_id,
                message: message
            },
            success: function (response) {
                if (response.success) {

                    $('#message').val('');

                    let chat = response.data.messages;
                    let time = response.data.created_at;
                    let chat_id = response.data.id;

                    let chatHtml = ` <div class="chat-message-right pb-4">
                                            <div>
                                                <img
                                                    src="${currentUserImage}"
                                                    class="rounded-circle mr-1"
                                                    alt="Chris Wood"
                                                    width="40"
                                                    height="40"
                                                />
                                                <div class="text-muted small text-nowrap mt-2">
                                                    ${formateDateTime(time)}
                                                </div>
                                            </div>
                                            <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                                                <div class="font-weight-bold mb-1">You</div>
                                                ${chat}
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn"  id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                ...
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                    <li><a class="dropdown-item delete" id="delete-${chat_id}" data-chatId="${chat_id}" href="#">Delete</a></li>
                                                    <li><a class="dropdown-item edit" id="edit-${chat_id} + '" data-chatId="${chat_id}" href="#">Edit</a></li>
                                                </ul>
                                            </div>
                                        </div>`;

                    $('.chat-messages').append(chatHtml)
                    scrollChat()

                } else {
                    alert(response.msg)
                }
            }
        })
    })

})

function loadOldChat(getRequestedUserName) {
    $.ajax({
        url: '/load-old-chat',
        type: 'POST',
        data: {
            sender_id: sender_id,
            receiver_id: receiver_id
        },
        success: function (response) {

            if (response.success) {
                let chats = response.data
                let html = '';
                let dynamicClass = ''
                let user = '';
                let userImage = '';
                let modfyChat = ''

                for (let i = 0; i < chats.length; i++) {

                    if (chats[i].sender_id == sender_id) {
                        dynamicClass = 'chat-message-right';
                        user = 'You';
                    } else {
                        dynamicClass = 'chat-message-left';
                        user = getRequestedUserName
                    }

                    if (chats[i].sender_id === sender_id) {
                        userImage = chats[i].receiver.user_image;
                    } else {
                        userImage = chats[i].sender.user_image;
                    }

                    html += `<div id="chat-${chats[i].id}" class="` + dynamicClass + ` pb-4">
                                            <div>
                                                <img
                                                    src="${userImage}"
                                                    class="rounded-circle mr-1"
                                                    alt="Chris Wood"
                                                    width="40"
                                                    height="40"
                                                />
                                                <div class="text-muted small text-nowrap mt-2">
                                                    ${formateDateTime(chats[i].created_at)}
                                                </div>
                                            </div>
                                            <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                                                <div class="font-weight-bold mb-1">${user}</div>
                                                ${chats[i].messages}
                                            </div>`;
                    if (chats[i].sender_id == sender_id) {
                        html += '<div class="dropdown">' +
                            '<button class="btn"  id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">' +
                            '...' +
                            '</button>' +
                            '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">' +
                            '<li><a class="dropdown-item delete" id="delete-' + chats[i].id + '" data-chatId="' + chats[i].id + '" href="#">Delete</a></li>' +
                            '<li><a class="dropdown-item edit" id="edit-' + chats[i].id + '" data-chatId="' + chats[i].id + '" href="#">Edit</a></li>' +
                            '</ul>' +
                            '</div>'
                    }
                    html += `</div>`

                }

                $('.chat-messages').html(html);
                $('.chat-messages').attr('data-totalChat', response.totalChat);
                scrollChat();

            }

        }
    })
}

function scrollChat() {
    $('.chat-messages').animate({
        scrollTop: $('.chat-messages').offset().top + $('.chat-messages')[0].scrollHeight
    }, 0)
}

//scroll call ajax
$(document).ready(function () {
    let container = $('.chat-messages');
    let totalChat = $('.chat-messages').attr('data-totalChat');
    let offset = $('.chat-messages').attr('data-offset');

    if (totalChat < offset) {
        container.scroll(function () {

            if (container.scrollTop() == 0) {

                let userName = $('.user-list').attr('data-userName');

                $.ajax({
                    url: '/load-more-chat',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        sender_id: sender_id,
                        receiver_id: receiver_id,
                        offset: offset
                    },
                    success: function (response) {


                        if (response.success) {
                            let html = '';
                            let chats = response.data
                            let dynamicClass = ''
                            let user = '';
                            let lateseOffsetValue = parseInt(response.moreChatCount) + parseInt(offset);
                            $('.chat-messages').attr('data-offset', lateseOffsetValue)
                            for (let i = 0; i < chats.length; i++) {

                                if (chats[i].sender_id == sender_id) {
                                    dynamicClass = 'chat-message-right';
                                    user = 'You';
                                } else {
                                    dynamicClass = 'chat-message-left';
                                    user = userName
                                }

                                html += `<div class="` + dynamicClass + ` pb-4">
                                            <div>
                                                <img
                                                    src="https://bootdey.com/img/Content/avatar/avatar1.png"
                                                    class="rounded-circle mr-1"
                                                    alt="Chris Wood"
                                                    width="40"
                                                    height="40"
                                                />
                                                <div class="text-muted small text-nowrap mt-2">
                                                    ${formateDateTime(chats[i].created_at)}
                                                </div>
                                            </div>
                                            <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                                                <div class="font-weight-bold mb-1">${user}</div>
                                                ${chats[i].messages}
                                            </div>
                                        </div>`

                            }

                            $('.chat-messages').prepend(html);

                        }

                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching data: ', error);
                    }
                });
            }
        });
    }

});

//Typing... event

$(document).ready(function () {
    let typingTimer;
    const typingTimeout = 1000;

    $('#message').on('input', function () {
        clearTimeout(typingTimer);
        startTyping();
        typingTimer = setTimeout(stopTyping, typingTimeout);
    });

    function startTyping() {
        $.post(`/start-typing/${receiver_id}/${sender_id}`, function (data) {

        });

    }

    function stopTyping() {

        $.post(`/stop-typing/${receiver_id}/${sender_id}`, function (data) {

        });
    }

    window.Echo.channel(`typing`)
        .listen('.typingEvent', (data) => {

            if (data.isTyping && data.senderId == receiver_id) {
                $('#typingIndicator').removeClass('d-none')

            } else {
                $('#typingIndicator').addClass('d-none')

            }
        });

})


// For Delete message

$(document).on('click', '.delete', function (e) {

    let chatId = $(this).attr('data-chatId')

    if (chatId) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        })
            .then((result) => {

                if (result.isConfirmed) {
                    $.ajax({
                        url: `delete-chat/${chatId}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            if (response.status) {
                                $('#chat-' + chatId).remove();
                                Swal.fire({
                                    title: "Deleted!",
                                    text: `${response.msg}`,
                                    icon: "success"
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Something went wrong!",
                                });
                            }
                        }
                    })

                }
            });
    } else {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Something went wrong!",
        });
    }

})


function formateDateTime(time) {
    const currentDate = new Date(time);
    const day = currentDate.getDate();
    const month = new Intl.DateTimeFormat('en-US', {month: 'short'}).format(currentDate);
    const year = currentDate.getFullYear();
    const hours = currentDate.getHours();
    const minutes = currentDate.getMinutes();
    const ampm = hours >= 12 ? 'pm' : 'am';
    const formattedHours = hours % 12 || 12;
    // const formattedDateTime = `${day} ${month} ${year} - ${formattedHours}:${minutes} ${ampm}`;
    return `${formattedHours}:${minutes} ${ampm}`;
}

window.Echo.join('private-chat')
    .here((users) => {

        for (let i = 0; i < users.length; i++) {
            // console.log(users[i]['id'])
            if (sender_id != users[i]['id']) {
                console.log('check')
                $(`#${users[i]['id']}-status`).removeClass('chat-offline')
                $(`#${users[i]['id']}-status`).addClass('chat-online')
                $(`#${users[i]['id']}-status`).text(' Online')
            }
        }

    })
    .joining((user) => {
        // console.log(`${user.id}-status`)
        $(`#${user.id}-status`).removeClass('chat-offline')
        $(`#${user.id}-status`).addClass('chat-online')
        $(`#${user.id}-status`).text(' Online')
    })
    .leaving((user) => {
        $(`#${user.id}-status`).addClass('chat-offline')
        $(`#${user.id}-status`).removeClass('chat-online')
        $(`#${user.id}-status`).text(' Offline')
    })
    .listen('.private_msg', (e) => {
        console.log(e, 'eee')
    })

window.Echo.private('send-message')
    .listen('.getChatMessage', (data) => {
        let time = data.chat.created_at
        let chat = data.chat.messages
        let name = data.userInformations.sender.name
        if (sender_id == data.chat.receiver_id && receiver_id == data.chat.sender_id) {

            let chatHtml = ` <div class="chat-message-left pb-4">
                                 <div>
                                    <img
                                        src="${data.userImage}"
                                        class="rounded-circle mr-1"
                                        alt="Sharon Lessman"
                                        width="40"
                                        height="40"
                                    />
                                    <div class="text-muted small text-nowrap mt-2">
                                        ${formateDateTime(time)}
                                    </div>
                                </div>
                                <div class="flex-shrink-1 bg-light rounded py-2 px-3 ml-3">
                                    <div class="font-weight-bold mb-1">${name}</div>
                                    ${chat}
                                </div>
                        </div>`

            $('.chat-messages').append(chatHtml)
        }

    })


window.Echo.private('message-deleted')
    .listen('DeleteMessageEvent', (data) => {
        let chatId = data.chatId;
        if (chatId) {
            $('#chat-' + chatId).remove();
        }
    })
