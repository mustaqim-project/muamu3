@extends('vendor.layouts.master')

@section('content')
    <style>
        /* CSS */
        .wsus__chat_area {
            display: flex;
            flex-direction: column;
            height: 70vh;
            border: 1px solid #ccc;
            background-color: #fff;
        }

        .wsus__chat_area_header {
            flex: 0 0 auto;
            padding: 10px;
            background-color: #f5f5f5;
            border-bottom: 1px solid #ddd;
        }

        .wsus__chat_area_body {
            flex: 1 1 auto;
            overflow-y: auto;
            padding: 10px;
        }

        .wsus__chat_area_footer {
            flex: 0 0 auto;
            padding: 10px;
            background-color: #f5f5f5;
            border-top: 1px solid #ddd;
        }

        #message-form {
            display: flex;
        }

        .message-box {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        #message-form button {
            padding: 8px 12px;
            margin-left: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #message-form button:hover {
            background-color: #0056b3;
        }
    </style>
    <section id="wsus__dashboard">
        <div class="container-fluid">
            @include('frontend.dashboard.layouts.sidebar')
            <div class="row">
                <div class="col-xl-9 col-xxl-10 col-lg-9 ms-auto">
                    <div class="dashboard_content mt-2 mt-md-0">
                        <h3><i class="far fa-star" aria-hidden="true"></i> Message</h3>
                        <div class="wsus__dashboard_review">
                            <div class="row">
                                <div class="col-xl-4 col-md-5">
                                    <div class="wsus__chatlist d-flex align-items-start">
                                        <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist"
                                            aria-orientation="vertical">
                                            <h2>Seller List</h2>
                                            <div class="wsus__chatlist_body">

                                                @php
                                                    use Illuminate\Support\Facades\DB;
                                                    use App\Models\Chat;

                                                    $vendorId = auth()->user()->id;

                                                    $users = DB::select(
                                                        '
                                                    SELECT DISTINCT
                                                        o.user_id,
                                                        u.name AS user_name,
                                                        u.username AS user_username,
                                                        u.image AS user_image
                                                    FROM
                                                        orders o
                                                    JOIN
                                                        order_products op ON o.id = op.order_id
                                                    JOIN
                                                        users u ON o.user_id = u.id
                                                    WHERE
                                                        op.vendor_id = ?
                                                    ',
                                                        [$vendorId],
                                                    );
                                                @endphp

                                                @foreach ($users as $user)
                                                    @php
                                                        $unseenMessagesCount = \App\Models\Chat::where([
                                                            'sender_id' => $vendorId,
                                                            'receiver_id' => $user->user_id,
                                                            'seen' => 0,
                                                        ])->count();
                                                    @endphp


                                                    <button class="nav-link chat-user-profile"
                                                        data-id="{{ $user->user_id }}" data-bs-toggle="pill"
                                                        data-bs-target="#v-pills-home" type="button" role="tab"
                                                        aria-controls="v-pills-home" aria-selected="true">
                                                        <div
                                                            class="wsus_chat_list_img {{ $unseenMessagesCount > 0 ? 'msg-notification' : '' }}">
                                                            <img src="{{ asset($user->user_image) }}" alt="user"
                                                                class="img-fluid">
                                                            @if ($unseenMessagesCount > 0)
                                                                <span class="pending" id="pending-{{ $user->user_id }}">
                                                                    {{ $unseenMessagesCount }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="wsus_chat_list_text">
                                                            <h4>{{ $user->user_name }}</h4>
                                                        </div>
                                                    </button>
                                                @endforeach


                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-8 col-md-10">
                                    <div class="wsus__chat_main_area">
                                        <div class="tab-content" id="v-pills-tabContent">
                                            <div class="tab-pane fade show" id="v-pills-home" role="tabpanel"
                                                aria-labelledby="v-pills-home-tab">
                                                <div id="chat_box">
                                                    <div class="wsus__chat_area">
                                                        <div class="wsus__chat_area_header">
                                                            <h2 id="chat-inbox-title">Chat</h2>
                                                        </div>
                                                        <div class="wsus__chat_area_body" data-inbox="">

                                                            <!-- Tambahkan pesan lainnya -->
                                                        </div>
                                                        <div class="wsus__chat_area_footer">
                                                            <form id="message-form">
                                                                @csrf
                                                                <input type="text" placeholder="Type Message"
                                                                    class="message-box" autocomplete="off" name="message">
                                                                <input type="hidden" name="receiver_id" value=""
                                                                    id="receiver_id">
                                                                <button type="submit"><i
                                                                        class="fas fa-paper-plane send-button"
                                                                        aria-hidden="true"></i></button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const mainChatInbox = $('.wsus__chat_area_body');

        function formatDateTime(dateTimeString) {
            const options = {
                year: 'numeric',
                month: 'short',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }
            const formatedDateTime = new Intl.DateTimeFormat('en-Us', options).format(new Date(dateTimeString));
            return formatedDateTime;
        }

        function scrollTobottom() {
            mainChatInbox.scrollTop(mainChatInbox.prop("scrollHeight"));
        }

        $(document).ready(function() {
            let messageInterval = null; // Variable to hold the interval ID

            function loadMessages(receiverId, senderImage, chatUserName) {
                $.ajax({
                    method: 'get',
                    url: '{{ route('vendor.get-messages') }}',
                    data: {
                        receiver_id: receiverId
                    },
                    beforeSend: function() {
                        mainChatInbox.html(""); // Clear messages before loading new ones
                        // Set chat inbox title
                        $('#chat-inbox-title').text(`Chat With ${chatUserName}`);
                    },
                    success: function(response) {
                        $.each(response, function(index, value) {
                            let message;
                            if (value.sender_id == USER.id) {
                                message = `
                    <div class="wsus__chat_single single_chat_2">
                                    <div class="wsus__chat_single_img">
                                        <img src="${USER.image}" alt="user" class="img-fluid">
                                    </div>
                                    <div class="wsus__chat_single_text">
                                        <p>${value.message}</p>
                                        <span>${formatDateTime(value.created_at)}</span>
                                    </div>
                                </div>`;
                            } else {
                                message = `
                    <div class="wsus__chat_single">
                                    <div class="wsus__chat_single_img">
                                        <img src="${senderImage}" alt="user" class="img-fluid">
                                    </div>
                                    <div class="wsus__chat_single_text">
                                        <p>${value.message}</p>
                                        <span>${formatDateTime(value.created_at)}</span>
                                    </div>
                                </div>`;
                            }
                            mainChatInbox.append(message);
                        });

                        // Scroll to bottom
                        scrollTobottom();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading messages:", error);
                    }
                });
            }

            $('.chat-user-profile').on('click', function() {
                let receiverId = $(this).data('id');
                let senderImage = $(this).find('img').attr('src');
                let chatUserName = $(this).find('h4').text();

                $(this).find('.wsus_chat_list_img').removeClass('msg-notification');
                $('.chat-box').removeClass('d-none');
                mainChatInbox.attr('data-inbox', receiverId);
                $('#receiver_id').val(receiverId);

                // Stop any previous interval
                if (messageInterval) {
                    clearInterval(messageInterval);
                }

                // Load messages immediately
                loadMessages(receiverId, senderImage, chatUserName);

                // Start auto-loading messages every 10 seconds
                messageInterval = setInterval(function() {
                    loadMessages(receiverId, senderImage, chatUserName);
                }, 10000); // 10000 milliseconds = 10 seconds
            });



            $('#message-form').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                let messageData = $('.message-box').val();

                var formSubmitting = false;

                if (formSubmitting || messageData === "") {
                    return;
                }

                // set message in inbox
                let message = `
                <div class="wsus__chat_single single_chat_2">
                    <div class="wsus__chat_single_img">
                        <img src="${USER.image}"
                            alt="user" class="img-fluid">
                    </div>
                    <div class="wsus__chat_single_text">
                        <p>${messageData}</p>
                        <span></span>
                    </div>
                </div>
                `
                mainChatInbox.append(message);
                $('.message-box').val('');
                scrollTobottom()

                $.ajax({
                    method: 'POST',
                    url: '{{ route('vendor.send-message') }}',
                    data: formData,
                    beforeSend: function() {
                        $('.send-button').prop('disabled', true);
                        formSubmitting = true;
                    },
                    success: function(response) {

                    },
                    error: function(xhr, status, error) {
                        toastr.error(xhr.responseJSON.message);
                        $('.send-button').prop('disabled', false);
                        formSubmitting = false;
                    },
                    complete: function() {
                        $('.send-button').prop('disabled', false);
                        formSubmitting = false;
                    }
                })
            })
        })
    </script>
@endpush
