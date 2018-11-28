<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // public function conversation_users(){
    //     return $this->hasMany('App\ConversationUser');
    // }

    public function conversations() {
        return $this->belongsToMany('App\Conversations', 'conversation_users', 'user_id', 'conversation_id');
    }


    public function messages(){
        return $this->hasMany('App\Messages', 'conversation_id', 'id');
    }

    public function isOnline(){
        return Cache::has('user-is-online-'.$this->id);
    }

}
