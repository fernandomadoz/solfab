<?php

namespace App\Http\Controllers;
use App\Cliente;
use App\Opcion;
use App\Parametro;
use Auth;
use Session;

use App\Http\Controllers\GenericController;

class ClienteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }



    public function CientesPorSucursal()
    {


        $gen_opcion = 1;
        $gen_modelo = 'Cliente';

        $GenericController = new GenericController();

        $gen_campos_a_ocultar = array('empresa_id');
        if ($gen_opcion > 0) {
            $Opcion = Opcion::where('id', $gen_opcion)->get();
            $campos_a_ocultar_array = explode('|', $Opcion[0]->no_listar_campos);
            foreach ($campos_a_ocultar_array as $campos_a_ocultar) {
                array_push($gen_campos_a_ocultar, $campos_a_ocultar);  
            }
        }
        $gen_campos = $GenericController->traer_campos($gen_modelo, $gen_campos_a_ocultar);

        $habilitar_abm_cliente = Parametro::find(2);
        if ($habilitar_abm_cliente->valor == 'SI') {
            $gen_permisos = [
                'C',
                'R',
                'U',
                'D',
                ];
        }
        else {
            $gen_permisos = [
                'R',
                ];
        }

        $gen_filas = '';
        $gen_seteo['filtro_where'] = ['sucursal_id', '=', Auth::user()->sucursal_id];
        $gen_seteo['gen_url_siguiente'] = 'back';
        $gen_seteo['gen_permisos'] = $gen_permisos;
        

        return View('genericas/list')
        ->with('gen_campos', $gen_campos)
        ->with('gen_filas', $gen_filas)
        ->with('gen_modelo', $gen_modelo)
        ->with('gen_permisos', $gen_permisos)
        ->with('gen_opcion', $gen_opcion)
        ->with('gen_seteo', $gen_seteo);

    }

}

