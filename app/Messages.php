<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    protected $fillable = [
        'conversation_id', 'user_id', 'message',
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function conversations(){
        return $this->belongsTo('App\Conversations');
    }
}
