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

                            $('.swal2-confirm').on('click',function (e){
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
