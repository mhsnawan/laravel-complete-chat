@extends('layouts.app')

@section('content')

<div class="container">
    <ul>
        @foreach($users as $user)
        <li onclick="getMessages({{ $user->id }})">{{ $user->name }}</li>
        @endforeach
    </ul>
</div>

<script>
    function getMessages(user_id){
        console.log('im in func');
        $.ajax({
            url: "{{ route('get-con') }}",
            data: {
                _token: '{{ csrf_token() }}',
                user_id: user_id
                },
            success: function(data){
                if(data.check == 1){
                    window.location = "result";
                    console.log('check is 1');
                }
                else{
                    window.location = "new-chat/"+data.user2;
                }

            },
            error: function(){
            }
        });
    }
</script>
@endsection
