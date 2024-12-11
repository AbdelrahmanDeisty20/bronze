<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymenMethod extends Model
{

    protected $table = 'payment_method';
    public $timestamps = true;
    protected $fillable = array('card_number', 'name', 'end_date', 'security_code','currency','amount','status','transaction_id','cart_id','address_id','shipping_id');

    public function cart()
    {
        return $this->belongsTo('App\Models\Cart');
    }

    public function address()
    {
        return $this->belongsTo('App\Models\Address');
    }

    public function shipping()
    {
        return $this->belongsTo('App\Models\Shipping');
    }

}
