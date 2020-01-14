<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Ordertaker extends Model
{
    public function orders(){
        return $this->hasmMany('App\Orders');
    }

    public function ot_seller_match(){
        $users = User::where('ot_of', Auth::user()->seller_of)->get();
        return $users;
    }
}
