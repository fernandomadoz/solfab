<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
	protected $guarded = ['id'];    

    public function descrip_modelo()
    {
        return $this->sucursal.', '.$this->Zona->zona;
    }

    public function zona()
    {
        return $this->belongsTo('App\Zona');
    }

    public function localidad()
    {
        return $this->belongsTo('App\Localidad');
    }

    protected $table = 'sucursales';
}
