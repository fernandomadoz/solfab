<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
	protected $guarded = ['id'];    

    public function empresa()
    {
        return $this->belongsTo('App\Empresa');
    }

}
