<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = array('a_benefit', 'c_benefit');

    public function invoicedetail(){
        return $this->hasMany('App\InvoiceDetail');
    }

    public function customer(){
    	return $this->belongsTo('App\Customer');
    }
}
