<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Forma_de_pago extends Model
{
	protected $guarded = ['id'];

    public function empresa()
    {
        return $this->belongsTo('App\Empresa');
    }

    protected $table = 'formas_de_pago';
}
