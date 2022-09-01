<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
	protected $guarded = ['id'];    

    public function provincia()
    {
        return $this->belongsTo('App\Provincia');
    }

    protected $table = 'localidades';  
}
