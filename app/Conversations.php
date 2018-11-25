<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversations extends Model
{
    protected $fillable = [
        'conversation_id', 'user_id', 'message',
    ];

    public function messages(){
        return $this->hasMany('App\Messages','conversation_id');
    }

    public function conversation_users(){
        return $this->hasMany('App\ConversationUser');
    }

    public function participants() {
        return $this->belongsToMany('App\User', 'conversation_users', 'conversation_id', 'user_id');
    }

}
