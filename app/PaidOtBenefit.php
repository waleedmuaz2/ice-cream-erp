<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaidOtBenefit extends Model
{
    public function user(){
    	return $this->belongsTo('App\User');
    }
}
