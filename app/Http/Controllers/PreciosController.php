<?php

namespace App\Http\Controllers;
use App\Lista_de_precio;
use App\Precio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\GenericController;


class PreciosController extends Controller
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



    public function preciosDeLista($lista_de_precio_id)
    {   
        $Lista_de_precio = Lista_de_precio::find($lista_de_precio_id);
        return View('precios/precios_de_lista')
        ->with('Lista_de_precio', $Lista_de_precio);
    }

    

    public function crearlistaDePrecios()
    {
        $gen_modelo = 'Precio';
        $gen_opcion = 0;
        $acciones_extra = array();
        $lista_de_precio_id = $_POST['lista_de_precio_id'];
        $gen_seteo['filtros_por_campo'] = array('lista_de_precio_id' => $lista_de_precio_id);
        $gen_seteo['gen_url_siguiente'] = 'back';

        $gen_campos_a_ocultar = array('id', 'empresa_id', 'lista_de_precio_id');
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

        $gen_filas = Precio::where('lista_de_precio_id', $lista_de_precio_id)->get();

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
