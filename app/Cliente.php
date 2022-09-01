<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
	protected $guarded = ['id'];    

    public function sucursal()
    {
        return $this->belongsTo('App\Sucursal');
    }

    public function tipo_de_documento()
    {
        return $this->belongsTo('App\Tipo_de_documento');
    }

    public function situacion_de_iva()
    {
        return $this->belongsTo('App\Situacion_de_iva');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function localidad()
    {
        return $this->belongsTo('App\Localidad');
    }

    public function zona_local()
    {
        return $this->belongsTo('App\Zona_local');
    }

    public function vendedor()
    {
        return $this->belongsTo('App\Vendedor');
    }

}
