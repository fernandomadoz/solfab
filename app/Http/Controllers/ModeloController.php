<?php

namespace App\Http\Controllers;
use App\Modelo;
use App\Composicion_de_modelo;
use App\Imagen_de_modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\GenericController;


class ModeloController extends Controller
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



    public function composicionDeModelo($modelo_id)
    {   
        $Modelo = Modelo::whereRaw('id = '.$modelo_id)->get();
        $Imagenes_de_modelo = Imagen_de_modelo::whereRaw('modelo_id = '.$modelo_id)->get();
        
        return View('modelos/composicion')
        ->with('modelo_id', $modelo_id)
        ->with('Modelo', $Modelo)
        ->with('Imagenes_de_modelo', $Imagenes_de_modelo);
    }

    

    public function crearListaModelo()
    {
        $gen_modelo = 'Composicion_de_modelo';
        $gen_opcion = 0;
        $acciones_extra = array();
        $modelo_id = $_POST['modelo_id'];
        $gen_seteo['filtros_por_campo'] = array('modelo_id' => $modelo_id);
        $gen_seteo['gen_url_siguiente'] = 'back';

        $gen_campos_a_ocultar = array('id', 'empresa_id', 'modelo_id');
        if ($gen_opcion > 0) {
            $Opcion = Opcion::where('id', $gen_opcion)->get();

            // Traigo los campos a Ocultar
            $campos_a_ocultar_array = explode('|', $Opcion[0]->no_listar_campos);
            foreach ($campos_a_ocultar_array as $campos_a_ocultar) {
                array_push($gen_campos_a_ocultar, $campos_a_ocultar);  
            }

            // Traigo las acciones extra
            $acciones_extra = explode('|', $Opcion[0]->acciones_extra);

        }        
        $GenericController = new GenericController();
        $gen_campos = $GenericController->traer_campos($gen_modelo, $gen_campos_a_ocultar);
        $gen_permisos = [
            'C',
            'R',
            'U',
            'D'
            ];

        $gen_filas = Composicion_de_modelo::where('modelo_id', $modelo_id)->get();

        //$gen_filas = call_user_func(array($this->dirModel($gen_modelo), 'all'), '*');
        //$gen_fila = call_user_func(array($this->dirModel($gen_modelo), 'find'), $gen_id);    

        $gen_nombre_tb_mostrar = $GenericController->nombreDeTablaAMostrar($gen_modelo);

        return View('genericas/func_list')
        ->with('gen_campos', $gen_campos)
        ->with('gen_modelo', $gen_modelo)
        ->with('gen_filas', $gen_filas)
        ->with('gen_seteo', $gen_seteo)
        ->with('gen_permisos', $gen_permisos)
        ->with('gen_opcion', $gen_opcion)
        ->with('gen_nombre_tb_mostrar', $gen_nombre_tb_mostrar)
        ->with('acciones_extra', $acciones_extra);       
    }


}
