<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gift extends Model 
{

    protected $table = 'gifts';
    public $timestamps = true;
    protected $fillable = array('gift');

}