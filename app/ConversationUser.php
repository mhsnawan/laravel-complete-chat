<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConversationUser extends Model
{
    protected $fillable = [
        'conversation_id', 'user_id',
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function conversation(){
        return $this->belongsTo('App\Conversations');
    }

}
