<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{    
    public function invoice(){
        return $this->belongsTo('App\Invoice');
    }

    public function product(){
        return $this->belongsTo('App\Product');
    }
}
