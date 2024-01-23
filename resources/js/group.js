$(document).ready(function () {
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
                }else {
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

    $('.my-group-lists').on('click', function (e) {
        $('.message-body').removeClass('d-none');
        let groupImage = $(this).attr('data-grpImage');
        let groupName = $(this).attr('data-grpName');
        let isAdmin = $(this).attr('data-isAdmin');
        let groupId = $(this).attr('data-groupId');

        $('#user-image').attr('src', groupImage)
        // $('#sender-name').remove()

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

    })

    $(document).on('click', '.check-group-request', function (e) {
        let groupId = $(this).attr('data-groupId')
        $.ajax({
            url:`get-group-request-list/${groupId}`,
            type:'GET',
            success:function (response){
                if (response.status){
                    $('.append-group-request-list').html(response.html);
                }
            }
        })
    })

    $(document).on('click','#requestApproveBtn',function (e){
        e.preventDefault();

        let userIds = [];
        $("input:Checkbox[name=userIds]:checked").each(function() {
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
                    url:'update-group-request-list',
                    type:'POST',
                    data: {
                        userIds:userIds,
                        groupId:groupId,
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
});


