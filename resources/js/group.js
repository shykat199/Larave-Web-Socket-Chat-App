import Echo from "laravel-echo";

$(document).ready(function () {
    let typingTimer;
    const typingTimeout = 1000;

    $('.send-request').on('click', function () {
        let groupSlug = $(this).attr('data-groupSlug');
        $.ajax({
            url: `/get-group-information/${groupSlug}`,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    $('#g_name').val(response.groupInformation.name)
                    $('#g_short_description').val(response.groupInformation.short_description)
                    $('#g_long_description').val(response.groupInformation.title)
                    $('#g_image').attr('src', response.groupImage)
                    $('#send-group-request').attr('data-groupSlug', response.groupInformation.slug)
                } else {
                    $('#g_name').val(response.groupInformation.name)
                    $('#g_short_description').val(response.groupInformation.short_description)
                    $('#g_long_description').val(response.groupInformation.title)
                    $('#g_image').attr('src', response.groupImage)
                    $('#send_request').attr("disabled", true);
                }
            }
        })
    })

    $('#send-group-request').on('click', function (e) {
        e.preventDefault();
        let groupSlug = $(this).attr('data-groupSlug');

        Swal.fire({
            title: "Are you sure?",
            text: "You want to send request!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, send it!"
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: `/send-group-request`,
                    type: 'POST',
                    data: {
                        slug: groupSlug
                    },
                    success: function (response) {
                        if (response.status) {
                            Swal.fire({
                                title: "Send!",
                                text: `${response.msg}`,
                                icon: "success"
                            });

                            $('.swal2-confirm').on('click', function (e) {
                                e.preventDefault();
                                location.reload(true);
                            })
                        }
                    }
                })


            }
        });

    })

    $('.my-group-lists').on('click', async function (e) {

        let groupId = $(this).attr('data-groupId');
        group_id = groupId

        try {

            await checkUserAccessPermission(sender_id, groupId)
                .then((status) => {
                    if (status) {

                        $('.message-body').removeClass('d-none');
                        let groupImage = $(this).attr('data-grpImage');
                        let groupName = $(this).attr('data-grpName');
                        let isAdmin = $(this).attr('data-isAdmin');

                        $('#user-image').attr('src', groupImage)

                        if (isAdmin == 1) {
                            $('.append-gName').html(`
         <strong class="check-group-request" data-toggle="modal" data-groupId="${groupId}"
                                data-target=".bd-group-request-modal" style="text-decoration: underline; cursor: pointer;">
                           ${groupName}
         </strong>
        `)
                        } else {

                            $('.append-gName').html(`
             <strong id="sender-name">${groupName}</strong>
            `)
                        }

                        loadGroupOldChat(groupId);
                    } else {
                        Swal.fire({
                            title: "Sorry!",
                            text: `You are not authorized.`,
                            icon: "warning"
                        });
                    }
                })

        } catch (error) {
            console.error("Error in AJAX call:", error);
        }
    })

    $(document).on('click', '.check-group-request', function (e) {
        let groupId = $(this).attr('data-groupId')
        $.ajax({
            url: `get-group-request-list/${groupId}`,
            type: 'GET',
            success: function (response) {
                if (response.status) {
                    $('.append-group-request-list').html(response.html);
                }
            }
        })
    })

    $(document).on('click', '#requestApproveBtn', function (e) {
        e.preventDefault();

        let userIds = [];
        $("input:Checkbox[name=userIds]:checked").each(function () {
            userIds.push($(this).val());
        });

        let groupId = $("input:hidden[name=groupId]").val();

        Swal.fire({
            title: "Are you sure?",
            text: "You want to approve request!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, approve it!"
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: 'update-group-request-list',
                    type: 'POST',
                    data: {
                        userIds: userIds,
                        groupId: groupId,
                    },
                    success: function (response) {
                        if (response.status) {
                            Swal.fire({
                                title: "Approved!",
                                text: `${response.msg}`,
                                icon: "success"
                            });

                            $('.swal2-confirm').on('click', function (e) {
                                e.preventDefault();
                                location.reload(true);
                            })
                        }
                    }
                })
            }
        });
    })

    $('#group').submit(function (e) {
        e.preventDefault();
        let message = $("input:text[name=message]").val();
        $.ajax({
            url: '/save-group-chat',
            type: "POST",
            data: {
                message: message,
                sender_id: sender_id,
                group_id: group_id,
            },
            success: function (response) {
                $("input:text[name=message]").val('');
                let chat = response.groupChatInfo.message;
                let time = response.groupChatInfo.created_at;
                let chat_id = response.groupChatInfo.id;
                let userImage = response.groupChatInfo.get_message_with_user_info.user_image;
                let userName = response.groupChatInfo.get_message_with_user_info.name;

                let chatHtml = ` <div id="group-chat-${response.groupChatInfo.id}" class="chat-message-right pb-4">
                                            <div>
                                                <img
                                                    src="${userImage}"
                                                    class="rounded-circle mr-1"
                                                    alt="${userName}"
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
                                                    <li><a class="dropdown-item delete-group-chat" id="delete-${chat_id}" data-GroupId="${group_id}" data-chatId="${chat_id}" href="#">Delete</a></li>
                                                    <li><a class="dropdown-item edit-group-chat" id="edit-${chat_id} + '" data-GroupId="${group_id}" data-chatId="${chat_id}" href="#">Edit</a></li>
                                                </ul>
                                            </div>
                                        </div>`;

                $('.chat-messages').append(chatHtml)
                scrollChat();
            }
        })
    })

    $(document).on('input', '#message', function () {
        clearTimeout(typingTimer);
        startTyping()
        typingTimer = setTimeout(stopTyping, typingTimeout);
    })

    $(document).on('click', '.delete-group-chat', function (e) {
        e.preventDefault()
        let groupId = $(this).attr('data-GroupId');
        let chatId = $(this).attr('data-chatId');

        if (chatId && groupId) {
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
                            url: 'group-chat-delete',
                            type: "GET",
                            data: {
                                groupId: groupId,
                                chatId: chatId
                            },
                            success: function (response) {
                                if (response.status) {
                                    $('#group-chat-' + chatId).remove();
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

});

function startTyping() {
    $.get(`group-start-typing/${group_id}/${sender_id}`, function ($data) {

    });
}

function stopTyping() {
    $.get(`group-stop-typing/${group_id}/${sender_id}`, function ($data) {

    });
}

function loadGroupOldChat(groupId) {
    $.ajax({
        url: 'load-group-old-chat',
        type: 'GET',
        data: {
            groupId: groupId
        },
        success: function (response) {
            if (response.status) {
                let chats = response.chatList;
                let html = '';
                let dynamicClass = ''
                let user = '';
                let userImage = '';

                Object.keys(chats).forEach(key => {
                    if (chats[key].sender_id == sender_id) {
                        dynamicClass = 'chat-message-right';
                        user = 'You';
                        userImage = chats[key].get_group_message_with_user_info.user_image
                    } else {
                        dynamicClass = 'chat-message-left';
                        user = chats[key].get_group_message_with_user_info.name
                        userImage = chats[key].get_group_message_with_user_info.user_image
                    }


                    html += `<div id="group-chat-${chats[key].id}" class="` + dynamicClass + ` pb-4">
                                            <div>
                                                <img
                                                    src="${userImage}"
                                                    class="rounded-circle mr-1"
                                                    alt="Chris Wood"
                                                    width="40"
                                                    height="40"
                                                />
                                                <div class="text-muted small text-nowrap mt-2">
                                                    ${formateDateTime(chats[key].created_at)}
                                                </div>
                                            </div>
                                            <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-3">
                                                <div class="font-weight-bold mb-1">${user}</div>
                                                ${chats[key].message}
                                            </div>`;
                    if (chats[key].sender_id == sender_id) {
                        html += '<div class="dropdown">' +
                            '<button class="btn"  id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">' +
                            '...' +
                            '</button>' +
                            '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">' +
                            '<li><a class="dropdown-item delete-group-chat" id="delete-' + chats[key].id + '" data-chatId="' + chats[key].id + '" data-groupId="' + group_id + '" href="#">Delete</a></li>' +
                            '<li><a class="dropdown-item edit-group-chat" id="edit-' + chats[key].id + '" data-chatId="' + chats[key].id + '" href="#">Edit</a></li>' +
                            '</ul>' +
                            '</div>'
                    }
                    html += `</div>`

                });

                $('.chat-messages').html(html);
                // $('.chat-messages').attr('data-totalChat', response.totalChat);
                scrollChat();

            }
        }
    })
}

function checkUserAccessPermission(userId, groupId) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'check-group-user-access',
            type: 'GET',
            data: {
                userId: userId,
                groupId: groupId
            },
            success: function (response) {
                const status = response.status;
                resolve(status);
            },
            error: function (error) {
                reject(error);
            }
        });
    });
}

function scrollChat() {
    $('.chat-messages').animate({
        scrollTop: $('.chat-messages').offset().top + $('.chat-messages')[0].scrollHeight
    }, 0)
}

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

window.Echo.private('get-group-chat')
    .listen('.getGroupChat', (data) => {
        console.log(data);
        let time = data.chat.created_at
        let chat = data.chat.message
        let name = data.chat.get_message_with_user_info.name
        let userImage = data.userImage

        if (sender_id != data.chat.sender_id && group_id == data.chat.group_id) {

            let chatHtml = ` <div class="chat-message-left pb-4">
                                 <div>
                                    <img
                                        src="${userImage}"
                                        class="rounded-circle mr-1"
                                        alt="${name}"
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

window.Echo.private('group-typing-event')
    .listen('GroupTypingEvent', (data) => {
        if (data.isTyping && data.groupId == group_id && data.sender_id != sender_id) {
            $('#typingIndicator').removeClass('d-none')

        } else {
            $('#typingIndicator').addClass('d-none')

        }
    })

window.Echo.private('delete-group-chat')
    .listen('GroupChatDeleteEvent', (data) => {
        let chatId = data.chatId;
        if (chatId) {
            $('#group-chat-' + chatId).remove();
        }
    })


