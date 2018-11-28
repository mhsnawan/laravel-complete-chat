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
            {{ $user }}
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
                    url : "{{ route('make-conversation') }}",
                    type : "post",
                    headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') },
                    data : {
                        _token:'{{ csrf_token() }}',
                        user1_id: {{ Auth::user()->id }},
                        user2_id: {{ $user->id }},
                        message: message,
                        },
                    success: function(data){
                        window.location = "../../inbox";
                    },
                    error: function(){

                    }
                });
            }
        });
        ////////////////////////////////////////////////////////////////////////////////////////////

    </script>

    @endsection


