<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model 
{

    protected $table = 'calls';
    public $timestamps = true;
    protected $fillable = array('number');

}