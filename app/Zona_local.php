<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zona_local extends Model
{
	protected $guarded = ['id'];    

    public function empresa()
    {
        return $this->belongsTo('App\Empresa');
    }

    public function localidad()
    {
        return $this->belongsTo('App\Localidad');
    }

    protected $table = 'zonas_locales';

}
