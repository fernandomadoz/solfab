<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_de_documento extends Model
{
    protected $guarded = ['id'];   

    protected $table = 'tipos_de_documentos';
}
