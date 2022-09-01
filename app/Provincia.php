<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
	protected $guarded = ['id'];    

    public function pais()
    {
        return $this->belongsTo('App\Pais');
    } 
}
