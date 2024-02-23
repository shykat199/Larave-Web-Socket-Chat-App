@push('custom.style')
    <style>
        .audio-video-container {
            min-height: 500px;
            max-height: 500px;
        }

        .icons img {
            width: 30px;
            /* Adjust the width as needed */
            height: auto;
            margin-right: 10px;
            border: 2px solid transparent;
            /* Add border */
            border-radius: 50%;
            /* Add border radius */
            transition: border-color 0.3s;
            /* Add transition effect */
            display: block;
            margin: 10px;
            cursor: pointer;
            opacity: 0.5;
            border-radius: 10px;
            transition: opacity 0.5s, background 0.5s;

        }

        .icons img:hover {

            opacity: 1;
            background: #3388cc;
            /* Change border color on hover */
        }

        .active-icon {
            opacity: 1;
            background: #3388cc;
        }

        .main-content {
            width: 98%;
            height: 100%;
            padding: 10px;
            background-color: #5e5959;
            border-radius: 10px;
            margin-left: 10px;
            margin-right: 10px;
        }

        .image-container {
            display: grid;
            grid-template-columns: repeat(0, 1fr);
            grid-gap: 10px;
        }

        .image img {
            width: 90%;
            border-radius: 10px;
            cursor: pointer;
            height: 70%;
        }

        .main-icons {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .main-icons .call-icon {
            width: 60px;
        }

        .videos {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1em;
        }

        .video-player {
            background-color: #444346;
            width: 100%;
            height: 400px;
            border: 2px solid #ffffff;
            border-radius: 10px;
            transform: scaleX(-1);
        }
    </style>
@endpush

<div class="container audio-video-container hidden">

    <div class="main-content">
        <!-- Main content goes here -->
        <div class="image-container">
            <div class="videos">
                <video class="video-player" id="user-1" autoplay playsinline></video>
                <video class="video-player" id="user-2" autoplay playsinline></video>
            </div>

        </div>
        <div class="icons main-icons">
            <!-- Icons here -->
            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20240206151955/temp1.png" height="10"
                 alt="Icon 1">
            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20240206151955/temp2.png" height="10"
                 alt="Icon 2">
            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20240130162531/call.png" height="10"
                 class="call-icon"
                 alt="Icon 3">
            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20240206151954/temp4.png" height="10"
                 alt="Icon 4">
            <img src="https://media.geeksforgeeks.org/wp-content/uploads/20240206151954/temp5.png" height="10"
                 alt="Icon 5">
        </div>
    </div>

</div>
