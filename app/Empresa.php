<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{

    protected $guarded = ['id']; 

    public function tipo_de_documento()
    {
        return $this->belongsTo('App\Tipo_de_documento');
    }
    
    public function situacion_de_iva()
    {
        return $this->belongsTo('App\Situacion_de_iva');
    }

    public function localidad()
    {
        return $this->belongsTo('App\Localidad');
    }

}
