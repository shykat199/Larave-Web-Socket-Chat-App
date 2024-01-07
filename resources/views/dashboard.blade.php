<x-app-layout>
    @push('custom.style')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
              integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w=="
              crossorigin="anonymous"/>
        <style type="text/css">
            body {
                margin-top: 20px;
            }

            .chat-online {
                color: #34ce57;
            }

            .chat-offline {
                color: #e4606d;
            }

            .chat-messages {
                display: flex;
                flex-direction: column;
                max-height: 800px;
                overflow-y: scroll;
            }

            .chat-message-left,
            .chat-message-right {
                display: flex;
                flex-shrink: 0;
            }

            .chat-message-left {
                margin-right: auto;
            }

            .chat-message-right {
                flex-direction: row-reverse;
                margin-left: auto;
            }

            .py-3 {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }

            .px-4 {
                padding-right: 1.5rem !important;
                padding-left: 1.5rem !important;
            }

            .flex-grow-0 {
                flex-grow: 0 !important;
            }

            .border-top {
                border-top: 1px solid #dee2e6 !important;
            }
        </style>
    @endpush

    <div class="container">
        <div class="card mt-2" style="min-height: 580px">
            <div class="row g-0">
                @include('side-bar',['allUsers'=>$allUsers])

                @include('message')
            </div>
        </div>
    </div>

    @push('custom.script')
        <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
    @endpush
</x-app-layout>
