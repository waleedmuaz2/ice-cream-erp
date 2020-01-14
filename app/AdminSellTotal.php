<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminSellTotal extends Model
{
    protected $fillable = [
        'total_amount', 'total_units',
    ];

    public function admin_total(){
    	return $this->belongsTo('App\User');
    }
}
