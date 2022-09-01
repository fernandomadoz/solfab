<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Composicion_de_modelo extends Model
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

}
