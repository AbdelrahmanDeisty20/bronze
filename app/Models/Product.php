<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $table = 'products';
    public $timestamps = true;
    protected $fillable = array('name', 'small_discrip', 'description', 'price', 'color', 'quantity', 'category_id');

    public function order()
    {
        return $this->belongsToMany('App\Models\Order');
    }

    public function carts()
    {
        return $this->hasMany('App\Models\Cart');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

}
