<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model 
{

    protected $table = 'reviews';
    public $timestamps = true;
    protected $fillable = array('comment', 'rate', 'client_id', 'product_id');

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

}