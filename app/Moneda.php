<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
	protected $guarded = ['id'];    

    public function empresa()
    {
        return $this->belongsTo('App\Empresa');
    }
}
