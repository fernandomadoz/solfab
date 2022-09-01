<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lista_de_precio extends Model
{
	protected $guarded = ['id'];    

    public function zona()
    {
        return $this->belongsTo('App\Zona');
    }

    public function forma_de_pago()
    {
        return $this->belongsTo('App\Forma_de_pago');
    }    

    public function moneda()
    {
        return $this->belongsTo('App\Moneda');
    }
    
    protected $table = 'listas_de_precio';
}
