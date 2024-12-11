<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model 
{

    protected $table = 'carts';
    public $timestamps = true;
    protected $fillable = array('client_id', 'product_id', 'quantity', 'total_price', 'price');

    public function client()
    {
        return $this->hasOne('App\Models\Client');
    }

    public function product()
    {
        return $this->hasOne('App\Models\Product');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\PaymenMethod');
    }

    public function order()
    {
        return $this->belongsToMany('App\Models\Order');
    }

}