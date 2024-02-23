import Echo from "laravel-echo";

let localStream;
let remoteStream;
let peerConnection;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).ready(function () {

    $(document).on('click', '.audio-call', function (e) {
        $(this).parents('.message-body').find('.audio-video-container').removeClass('hidden')
        $(this).parents('.message-body').find('.position-relative').addClass('hidden')
        init();
    })

    $(document).on('click', '.call-icon', function (e) {
        $(this).parents('.message-body').find('.audio-video-container').addClass('hidden')
    })

});

let init = async () => {

    try {
        localStream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: false
        });

        $('#user-1')[0].srcObject = localStream;
        $('#user-1')[0].addEventListener('loadedmetadata', () => {
            $('#user-1')[0].play();
        });

        $.ajax({
            url: '/send-offer',
            method: 'POST',
            data: {
                sender_id: sender_id,
                receiver_id: receiver_id,
            },
            success: function (response) {

            },
            error: function (error) {
                console.error('Error making AJAX request:', error);
            }
        });
        window.Echo.join('MessageFromPeer')
            .here(async (users) => {
                // Handle currently online users
            })
            .joining(async (user) => {
                // Handle user joining
                await handleUserJoined(user);
            })
            .leaving(async (user) => {
            })
            .listen('WebRTCEvent', (data) => {
                // Handle WebRTCEvent
                // console.log(data, 'datadatadata');
                // handleMessageFromPeer(data.message, data.userId);
            });

        window.Echo.private('handel-offer-channel')
            .listen('SendOffer', (data) => {
                handleMessageFromPeer(data.offer, data.type,data.receiverId,data.senderId,data.connected_userId);
            });


    } catch (error) {
        console.error("Error accessing media devices:", error);
        // Display a user-friendly message or take appropriate action
    }

}

let createPeerConnection = async () => {
    try {
        peerConnection = new RTCPeerConnection()
        remoteStream = new MediaStream()

        $('#user-2')[0].srcObject = remoteStream;
        $('#user-2')[0].addEventListener('loadedmetadata', () => {
            $('#user-2')[0].play();
        });

        if (!localStream) {
            localStream = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: false
            });
            $('#user-1')[0].srcObject = localStream;
            $('#user-1')[0].addEventListener('loadedmetadata', () => {
                $('#user-1')[0].play();
            });
        }

        localStream.getTracks().forEach((track) => {
            peerConnection.addTrack(track, localStream)
        })

        peerConnection.ontrack = (event) => {
            event.streams[0].getTracks().forEach((track) => {
                remoteStream.addTrack(track)
            })
        }

        peerConnection.onicecandidate = async (event) => {
            if (event.candidate) {
                sendCandidate(JSON.stringify(event.candidate));
            }
        }

    } catch (error) {
        console.error("Error accessing media devices:", error);
    }
}

let handleUserJoined = async (user) => {
    createOffer(user)
}

let handleMessageFromPeer = async (offer, type,receiverId,senderId,connected_userId) => {
    try {
        let userOffer = JSON.parse(offer)

        if (type == 'offer') {
            createAnswer(userOffer,type,receiverId,senderId,connected_userId)
        }

        if (type == 'answer') {
            addAnswer(message.answer)
        }

        if (type == 'candidate') {
            if (peerConnection) {
                peerConnection.addIceCandidate(message.candidate)
            }
        }
    } catch (error) {
        console.error("Error accessing media devices:", error);
    }
}

let createOffer = async (user) => {
    try {
        await createPeerConnection()
        //create offer
        let offer = await peerConnection.createOffer()
        await peerConnection.setLocalDescription(offer)
        // socket to send offer
        if (offer) {
            sendOffer(JSON.stringify(offer),user)
        }

    } catch (error) {
        console.error("Error accessing media devices:", error);
    }
}

let createAnswer = async (userOffer,type,receiverId,senderId,connected_userId) => {
    try {
        await createPeerConnection()

        await peerConnection.setRemoteDescription(userOffer)

        let answer = await peerConnection.createAnswer()
        await peerConnection.setLocalDescription(answer)

        createOfferAnswer(userId, JSON.stringify(answer));

    } catch (error) {
        console.error("Error accessing media devices:", error);
    }
}

let addAnswer = async (answer) => {
    if (!peerConnection.currentRemoteDescription) {
        peerConnection.setRemoteDescription(answer)
    }
}

// Call ajax to communicate server and fire event.
let sendCandidate = (userId, candidate) => {
    try {
        const response = $.ajax({
            url: '/handle-candidate',
            type: 'POST',
            data: {
                candidate: candidate,
                sender_id: sender_id,
                receiver_id: receiver_id,
                type: 'candidate'
            },
            success: function (response) {

            }
        });
    } catch (error) {
        console.log('Error sending candidate data:', error);
    }
}

let sendOffer = (offer,user) => {
    try {
        const response = $.ajax({
            url: '/handle-offer',
            type: 'POST',
            data: {
                offer: offer,
                sender_id: sender_id,
                receiver_id: receiver_id,
                connected_userId:user.id,
                type: 'offer'
            },
            success: function (response) {

            }
        });
    } catch (error) {
        console.log('Error sending candidate data:', error);
    }
}

let createOfferAnswer = (userId, answer) => {
    try {
        const response = $.ajax({
            url: '/handle-offer',
            type: 'POST',
            data: {
                answer: answer,
                sender_id: sender_id,
                receiver_id: receiver_id,
                type: 'answer'
            },
            success: function (response) {

            }
        });
    } catch (error) {
        console.log('Error sending candidate data:', error);
    }
}

// end ajax to communicate server and fire event.

// window.Echo.join('private-chat')
//     .here(async (users) => {
//
//     })
//     .joining(async (user) => {
//         await handleUserJoined(user)
//     })
//     .leaving(async (user) => {
//
//     })
//     .listen('.private_msg', async (e) => {
//         await handleMessageFromPeer(e, e.userId)
//     })


