<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Precio extends Model
{
	protected $guarded = ['id'];    

    public function lista_de_precio()
    {
        return $this->belongsTo('App\Lista_de_precio');
    }    

    public function modelo()
    {
        return $this->belongsTo('App\Modelo');
    }    

}
