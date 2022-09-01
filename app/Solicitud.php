<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Composicion_de_modelo_de_solicitud;
use App\Precio;
use App\Garante;

class Solicitud extends Model
{
	protected $guarded = ['id'];    

    public function modelo()
    {
        return $this->belongsTo('App\Modelo');
    }
    
    public function cliente()
    {
        return $this->belongsTo('App\Cliente');
    }
    
    public function lista_de_precio()
    {
        return $this->belongsTo('App\Lista_de_precio');
    }
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function moneda()
    {
        return $this->belongsTo('App\Moneda');
    }
    
    public function sucursal()
    {
        return $this->belongsTo('App\Sucursal');
    }

    public function vendedor()
    {
        return $this->belongsTo('App\Vendedor');
    }
    
    public function contarSuperficieSolicitud($id = -1) {
        if ($id == -1) {            
            $ComponentesDeModeloSolicitud = Composicion_de_modelo_de_solicitud::
                where('solicitud_id', $this->id)
                ->get();
        }
        else {                
            $ComponentesDeModeloSolicitud = Composicion_de_modelo_de_solicitud::
                where('solicitud_id', $this->id)
                ->where('id', '<>', $id)
                ->get();
        }
        $total_de_metros_cuadrados = 0;
        foreach ($ComponentesDeModeloSolicitud as $Componente) {
            $total_de_metros_cuadrados = $total_de_metros_cuadrados +($Componente->ancho*$Componente->largo);
        }
       

        $Precio = Precio::where('lista_de_precio_id', $this->lista_de_precio_id)->where('modelo_id', $this->modelo_id)->first();
        if (isset($Precio->precio)) {
            $precio_m2 = $Precio->precio;    
        }
        else {
            $precio_m2 = 0;    
        }
        
        //$valor_total = $precio_m2 * $total_de_metros_cuadrados;

        $this->total_de_metros_cuadrados = $total_de_metros_cuadrados;
        //$this->valor_total = $valor_total;
        $this->save();
    } 

    public function cantidadDeGarantes() {

        $cant_garantes = Garante::where('solicitud_id', $this->id)->count();
        
        return $cant_garantes;

    } 

    protected $table = 'solicitudes';
}
