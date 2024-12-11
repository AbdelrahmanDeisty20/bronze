<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model 
{

    protected $table = 'countries';
    public $timestamps = true;
    protected $fillable = array('name');

    public function clients()
    {
        return $this->hasMany('App\Models\Country');
    }

    public function cities()
    {
        return $this->hasMany('App\Models\Client');
    }

}