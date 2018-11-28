<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\User;
use App\ConversationUser;
use App\Messages;
use App\Conversations;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('conversations', 'ConversationsController');
Route::resource('messages', 'MessagesController');
Route::get('ajax', 'ConversationsController@ajax')->name('ajax');

Route::get('/message', function(){
    return view('chat.inbox');
});

Route::get('/inbox', function(){
    $id = Auth::user()->id;
    $data = array();
    $conversations = User::find($id)->conversations; //getting all the conversation in which this user is participating

    foreach($conversations as $conversation){
        //Getting last message of each conversation//
        $messages = Messages::where('conversation_id', $conversation->id)->orderBy('created_at', 'desc')->take(1)->get();
        //Getting participants for this conversation//
        $participants = Conversations::find($conversation->id)->participants;
        //Getting information for other user////
        foreach($participants as $participant){
            if($participant->id != $id){
                $reciever = $participant->id;
            }
        }
        $user2 = User::find($reciever);
        //Adding data to array//
        foreach($messages as $message){
            $data[] = array(
                'conversation_id' => $message->conversation_id,
                'user1_id' => $id,
                'user2_id' => $user2->id,
                'user2_name' => $user2->name,
                'message_user' => $message->user_id,
                'message' => $message->message,
                'created_at' => $message->created_at,
                );
        }
    }
    return view('chat.inbox')->with(compact('data'));
});

//Getting conversation in box
Route::get('/get-messages', function(Request $request){
    $id = Auth::user()->id;
    $input = $request->all();
    $conversation_id = $input['conversation_id'];
    $messages = Messages::where('conversation_id', $conversation_id)->get();
    foreach ($messages as $message){
        if ($message->user_id != $id){
            $m = Messages::find($message->id);
            $m->read = 1;
            $m->save();
        }
    }
    return response()->json([
        'messages' => $messages
    ]);
})->name('getmessages');

Route::post('/store-messages', function(Request $request){
    $input = $request->all();
    Messages::create($input);

})->name('store-messages');

Route::get('/live-messages', function(Request $request){
    $id = Auth::user()->id;
    $messages = Messages::where('conversation_id', '=', $request['conversation_id'])->where('user_id','!=',$id)->where('read','=', false)->get();
    foreach($messages as $message){
        $m = Messages::find($message->id);
        $m->read = 1;
        $m->save();
        //echo $message->id;
    }
    return response()->json([
        'messages' => $messages
    ]);

    //echo $request['conversation_id'];
})->name('live-messages');

Route::get('users', function(Request $request){
    $users = User::all();
    return view('chat.users')->with(compact('users'));
});

Route::get('get-con', function(Request $request){
    $input = $request->all();
    $user1 = Auth::user()->id;
    $user2 = $input['user_id'];
    $check = 0;

    //Getting all the records in which logged in user is involved
    $user1_conversations = ConversationUser::where('user_id', $user1)->get();
    //echo $user1_conversations;
    foreach($user1_conversations as $con){  //this iteration is for number of conversation_id
        $conversations = ConversationUser::where('conversation_id', $con->conversation_id)->where('user_id', $user2)->get();
        if(!$conversations->isEmpty()){
            $check = 1;
        }
    }
    return response()->json([
        'check' => $check,
        'user2' => $user2
    ]);
})->name('get-con');

Route::get('new-chat/{user_id}', function($user_id){
    $user = User::find($user_id);
    return view('chat.new')->with(compact('user'));
})->name('new-chat');

Route::post('make-conversation', function(Request $request){
    $input = $request->all();

    //Create conversation
    $conversationId = Conversations::create([
        'user_id' => Auth::user()->id
    ]);

    //Sender
    $conversation_user = ConversationUser::create([
        'user_id' => Auth::user()->id,
        'conversation_id' => $conversationId->id
    ]);

    //reciever
    ConversationUser::firstOrCreate([
        'user_id' => $request['user2_id'],
        'conversation_id' => $conversationId->id
    ]);

    Messages::create([
        'user_id' => Auth::user()->id,
        'conversation_id' => $conversationId->id,
        'message' => $request['message']
    ]);

})->name('make-conversation');
