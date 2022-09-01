<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{
	protected $guarded = ['id'];    

    public function sucursal()
    {
        return $this->belongsTo('App\Sucursal');
    }

    protected $table = 'vendedores';  
}
