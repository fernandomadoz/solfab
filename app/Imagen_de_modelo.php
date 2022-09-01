<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Imagen_de_modelo extends Model
{
	protected $guarded = ['id'];    

    public function modelo()
    {
        return $this->belongsTo('App\Modelo');
    }  

    protected $table = 'imagenes_de_modelos'; 
    
}
