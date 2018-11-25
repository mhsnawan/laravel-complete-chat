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


Route::get('/sendchat', function(){
    $users = User::all();
    return view('chat.sendchat')->with(compact('users'));
});

Route::get('/message', function(){
    return view('chat.message');
});

Route::get('/result', function(){
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
    return view('chat.message')->with(compact('data'));
    echo json_encode($data);
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
