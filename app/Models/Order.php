<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model 
{

    protected $table = 'orders';
    public $timestamps = true;
    protected $fillable = array('state', 'total_price');

    public function shippings()
    {
        return $this->hasMany('App\Models\Shipping');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product');
    }

    public function addresses()
    {
        return $this->belongsToMany('App\Models\Address');
    }

    public function carts()
    {
        return $this->hasMany('App\Models\Cart');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function notifications()
    {
        return $this->hasMany('App\Models\Notification');
    }

}