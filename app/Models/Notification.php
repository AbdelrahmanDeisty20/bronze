<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model 
{

    protected $table = 'notifications';
    public $timestamps = true;
    protected $fillable = array('title', 'content', 'order_id', 'client_id');

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function order_id()
    {
        return $this->belongsTo('App\Models\Order');
    }

}