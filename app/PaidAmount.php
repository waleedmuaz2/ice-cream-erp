<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaidAmount extends Model
{
    public function user(){
    	return $this->belongsTo('App\User');
    }
}
