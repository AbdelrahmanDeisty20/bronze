<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{

    protected $table = 'tokens';
    public $timestamps = true;
    protected $fillable = array('type','token');

    public function client()
    {
        return $this->hasOne('App\Models\Client');
    }

}