<?php

namespace App;
use Auth;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = array('a_benefit', 'c_benefit');

    public function orderdetail(){
        return $this->hasMany('App\OrderDetail','order_id');
    }

    public function ordertaker(){
    	return $this->belongsTo('App\User','ot_id');
    }

    public function get_ot_creater(){
        return $this->belongsTo('App\User','ot_id')->where('ot_of', Auth::id());
    }

    public function seller(){
    	return $this->belongsTo('App\User','is_confirmed_seller','id');
    }

    public function admin(){
    	return $this->belongsTo('App\User','is_confirmed_admin','id');
    }
    
    public function customers(){
    	return $this->belongsTo('App\Customer','customer_id','id');
    }

    public function is_subadmin_customer(){
    	return $this->belongsTo('App\Customer','customer_id','id')->where('created_by', Auth::id());
    }

}
