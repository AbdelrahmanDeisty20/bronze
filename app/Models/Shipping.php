<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model 
{

    protected $table = 'shippings';
    public $timestamps = true;
    protected $fillable = array('name', 'cost', 'time');

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\PaymenMethod');
    }

}