<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use App\Order;
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'email'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function customer(){
        return $this->belongsTo('App\Customer');
    }
    
    public function mycustomers(){
        return $this->belongsTo('App\Customer' , 'created_by');
    }
    
    public function mysellers(){
        return $this->hasMany('App\User' , 'seller_of');
    }

    public function admin_total(){
        return $this->belongsTo('App\AdminSellTotal');
    }
    
    public function oauth_access(){
        return $this->hasMany('App\OauthAccessToken');
    }

    public function invoices(){
        return $this->hasMany('App\Invoice');
    }

    public function paid(){
        return $this->hasMany('App\PaidAmount');
    }
    public function orders(){
            return $this->hasMany(Order::class,'ot_id');
    }
    public function scopeordertaker(){
        return $this->hasOne(Ordertaker::class,'user_id','id');
}
}
