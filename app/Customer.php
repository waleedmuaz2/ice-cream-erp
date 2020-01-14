<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Order;
use Auth;

class Customer extends Model
{
    protected $fillable = ['address' , 'phone' , 'freezer_model' , 'cnic' , 'other', 'area_id', 'allowed_products'];
    public function user(){
        return $this->belongsTo('App\User');
    }

    public function custom_prices(){
    	return $this->hasMany('App\CustomPrice');
    }

    public function custom_Ot_benefit(){
    	return $this->hasMany('App\CustomOTBenefit');
    }
    public function Ordertakers(){
    	return $this->hasMany('App\OtCustomer');
    }

    public function invoices(){
    	return $this->hasMany('App\Invoice');
    }

    public function scopeorder(){
    	return $this->hasMany('App\Order','customer_id');
    }
    
    public function area(){
        return $this->belongsTo('App\Area');
    }

}
