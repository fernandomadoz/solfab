<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
	protected $guarded = ['id'];    

    public function empresa()
    {
        return $this->belongsTo('App\Empresa');
    }

    protected $table = 'paises';  
}
