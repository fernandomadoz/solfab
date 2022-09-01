<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Composicion_de_modelo_de_solicitud extends Model
{
	protected $guarded = ['id'];    

    public function articulo()
    {
        return $this->belongsTo('App\Articulo');
    }

    public function modelo()
    {
        return $this->belongsTo('App\Modelo');
    }

    public function solicitud()
    {
        return $this->belongsTo('App\Solicitud');
    }    

    protected $table = 'composicion_de_modelo_de_solicitudes';  
}
