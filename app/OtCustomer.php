<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class OtCustomer extends Model
{
    public function customer(){
        return $this->belongsTo('App\Customer');
    }

    public function ot_customers(){
        return $this->where('ot_id', Auth::id())->belongsTo('App\Customer','customer_id');
    }
}
