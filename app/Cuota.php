<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
	protected $guarded = ['id'];    

    public function solicitud()
    {
        return $this->belongsTo('App\Solicitud');
    }

}
