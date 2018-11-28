@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class=" text-center">Messaging</h3>
    <div class="messaging">
      <div class="inbox_msg">
        <div class="inbox_people">
          <div class="headind_srch">
            <div class="recent_heading">
              <h4>Recent</h4>
            </div>
            <div class="srch_bar">
              <div class="stylish-input-group">
                <input type="text" class="search-bar"  placeholder="Search" >
                <span class="input-group-addon">
                <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                </span> </div>
            </div>
          </div>

          <div class="inbox_chat">
            @foreach($data as $item)
            <div class="chat_list {{ $item['conversation_id'] }}" onclick="clickConversation({{ $item['user1_id'] }}, {{ $item['conversation_id'] }})">
              <div class="chat_people">
                <div class="chat_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> </div>
                <div class="chat_ib">
                  <h5>{{ $item['user2_name'] }} <span class="chat_date">{{ $item['created_at']->format('h:i | d M') }}</span></h5>
                  <p>{{ $item['message'] }}</p>
                </div>
              </div>
            </div>
            @endforeach
          </div>

        </div>
        <div class="mesgs">
          <div class="msg_history">
            {{-- Incoming and outgoing messages will come here   --}}
          </div>
          <div class="type_msg">
            <div class="input_msg_write">
              <input type="text" class="write_msg send" placeholder="Type a message" />
              {{--  <button class="msg_send_btn" type="button"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>  --}}
            </div>
          </div>
        </div>
      </div>
    </div></div>
    <script>
        var conversation_id = 0;
        var user1_id = 0;
        var previous_con_id=0;
        /////////////////////////////Getting Conversation when clicking on user./////////////////////////////
        function clickConversation(user_id, abc){
            $('.' + previous_con_id).removeClass("active_chat");
            $('.' + abc).addClass("active_chat");
            user1_id = user_id;
            conversation_id = abc;
            getMessages(user1_id, conversation_id);
        }

        function getMessages(user1_id, con){
            previous_con_id = con;
            $('.msg_history').empty();
            $.ajax({
                url: "{{ route('getmessages') }}",
                data: {
                    _token: '{{ csrf_token() }}',
                    conversation_id: con
                    },
                success: function(data){
                    messages = data.messages;
                    messages.forEach(function(entry) {
                        var timestamp = entry['created_at'];
                        date = timestamp.substr(0,timestamp.indexOf(' '));
                        time = timestamp.substr(timestamp.indexOf(' ')+1);
                        if(user1_id == entry.user_id){
                            $('.msg_history').append(`
                            <div class="outgoing_msg">
                                <div class="sent_msg">
                                    <p>`+entry['message']+`</p>
                                    <span class="time_date"> `+time+`    |    `+date+`</span>
                                </div>
                            </div>`);
                        }
                        else{
                            $('.msg_history').append(`
                            <div class="incoming_msg">
                                <div class="incoming_msg_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> </div>
                                <div class="received_msg">
                                    <div class="received_withd_msg">
                                    <p>`+entry['message']+`</p>
                                    <span class="time_date"> `+time+`    |    `+date+`</span></div>
                                </div>
                            </div>`);
                        }
                    });
                },
                error: function(){
                }
            });
        }
        ////////////////////////////////////END of Conversation//////////////////////////////////////

        ///////////////////////////////////Storing Conversation//////////////////////////////////////
        $(document).on('keydown', '.send', function(e){
            var message = $(this).val();
            if(!message == '' && e.keyCode == 13 && !e.shiftKey){
                var today = new Date();
                var date = today.getFullYear()+ '-' + (today.getMonth() + 1) + '-' +today.getDate();
                var time = today.getHours()+ ':' + today.getMinutes() + ':' +today.getSeconds();
                $('.msg_history').append(`
                <div class="outgoing_msg">
                    <div class="sent_msg">
                        <p>`+message+`</p>
                        <span class="time_date"> `+time+`    |    `+date+`</span>
                    </div>
                </div>`);
                $(this).val(' ');
                $.ajax({
                    url : "{{ route('store-messages') }}",
                    type : "post",
                    data : {
                        _token:'{{ csrf_token() }}',
                        conversation_id: conversation_id,
                        user_id: {{ Auth::user()->id }},
                        message: message,
                        }
                });
            }
        });
        ////////////////////////////////////////////////////////////////////////////////////////////

        ////////////////////////////////////Getting Unread Messages/////////////////////////////////
        $(function(){
            liveChat();
        })

        function liveChat(){
            $.ajax({
                url: "{{ route('live-messages') }}",
                data: {
                    _token: '{{ csrf_token() }}',
                    conversation_id: conversation_id
                    },
                success: function(data){
                    if(data != ''){
                        messages = data.messages;
                        messages.forEach(function(entry) {
                            var timestamp = entry['created_at'];
                            date = timestamp.substr(0,timestamp.indexOf(' '));
                            time = timestamp.substr(timestamp.indexOf(' ')+1);
    	                    $('.msg_history').append(`
                                <div class="incoming_msg">
                                    <div class="incoming_msg_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> </div>
                                    <div class="received_msg">
                                        <div class="received_withd_msg">
                                        <p>`+entry['message']+`</p>
                                        <span class="time_date"> `+time+`    |    `+date+`</span></div>
                                    </div>
                                </div>`);
                        });
                    }
                    setTimeout(liveChat, 1000);
                },
                error: function(){
                    setTimeout(liveChat, 5000);
                }
            });
        }

        ////////////////////////////////////////////////////////////////////////////////////////////
    </script>

    @endsection


