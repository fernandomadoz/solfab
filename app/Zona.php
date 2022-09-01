<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
	protected $guarded = ['id'];    

    public function empresa()
    {
        return $this->belongsTo('App\Empresa');
    }

    public function sucursales()
    {
        return $this->hasMany('App\Sucursal');
    }

}
