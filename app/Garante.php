<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Garante extends Model
{
	protected $guarded = ['id'];    

    public function solicitud()
    {
        return $this->belongsTo('App\Solicitud');
    }
    
    public function tipo_de_documento()
    {
        return $this->belongsTo('App\Tipo_de_documento');
    }

}
