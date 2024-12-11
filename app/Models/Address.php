<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model 
{

    protected $table = 'addresses';
    public $timestamps = true;
    protected $fillable = array('recipient_name', 'cntry_region', 'zip_code', 'cntry_name', 'phone');

    public function order()
    {
        return $this->belongsToMany('App\Models\Order');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\PaymenMethod');
    }

}