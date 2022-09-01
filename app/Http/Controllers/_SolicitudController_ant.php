<?php

namespace App\Http\Controllers;
use App\Solicitud;
use App\Modelo;
use App\Composicion_de_modelo;
use App\Composicion_de_modelo_de_solicitud;
use App\Lista_de_precio;
use App\Sucursal;
use App\Parametro;
use App\Cliente;
use App\Precio;
use App\Cuota;
use App\Reporte;
use App\Garante;
use App\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\GenericController;
use \App\Http\Controllers\FxC; 
use Auth;
use Session;
use PDF;


class _SolicitudController_ant extends Controller
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



    public function index($estado)
    {   
        $Solicitudes = Solicitud::all();
        $user_id = Auth::user()->id;

        if (Auth::user()->rol_de_usuario_id == 3) {
            $titulo = 'Mis Solicitudes';
            if($estado == 't') {
                $titulo .= ' (Todas)';
                $Solicitudes = Solicitud::whereNotNull('fecha_de_firma_de_solicitud')
                    ->where('user_id', $user_id)
                    ->get();                
            }
            if($estado == 'p') {
                $titulo .= ' (Pendientes)';
                $Solicitudes = Solicitud::whereRaw('((fecha_de_firma_de_solicitud IS NOT NULL AND sino_aprobado_administracion IS NULL) or (sino_aprobado_administracion = "NO" AND sino_aprobado_solicitar_revision = "SI"))')
                    ->where('user_id', $user_id)
                    ->get();                
            }
            if($estado == 'r') {
                $titulo .= ' (Revisar)';
                $Solicitudes = Solicitud::whereRaw('(sino_aprobado_administracion = "NO" AND sino_aprobado_solicitar_revision IS NULL)')
                    ->where('user_id', $user_id)
                    ->get();                
            }
            if($estado == 'a') {
                $titulo .= ' (Aprobadas)';
                $Solicitudes = Solicitud::where('sino_aprobado_administracion', 'SI')
                    ->whereRaw('(sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO")')
                    ->where('user_id', $user_id)
                    ->get();                
            }
            if($estado == 'd') {
                $titulo .= ' (Desprobadas)';
                $Solicitudes = Solicitud::whereRaw('((sino_aprobado_administracion = "NO" OR sino_aprobado_garantes = "NO") AND sino_aprobado_solicitar_revision = "NO")')
                    ->where('user_id', $user_id)
                    ->get();                
            }
            if($estado == 'f') {
                $titulo .= ' (Finalizadas)';
                $Solicitudes = Solicitud::where('sino_aprobado_finalizada', 'SI')
                    ->where('user_id', $user_id)
                    ->get();                
            }
        }
        else {
            $titulo = 'Solicitudes';
            if($estado == 't') {
                $titulo .= ' (Todas)';
                $Solicitudes = Solicitud::whereNotNull('fecha_de_firma_de_solicitud')->get();
            }
            if($estado == 'p') {
                $titulo .= ' (Pendientes)';
                $Solicitudes = Solicitud::whereRaw('(fecha_de_firma_de_solicitud IS NOT NULL AND sino_aprobado_administracion IS NULL)')
                    ->get();                
            }
            if($estado == 'r') {
                $titulo .= ' (Revisar)';
                $Solicitudes = Solicitud::whereRaw('(sino_aprobado_administracion = "NO" AND sino_aprobado_solicitar_revision = "SI")')
                    ->get();                
            }
            if($estado == 'a') {
                $titulo .= ' (Aprobadas)';
                $Solicitudes = Solicitud::where('sino_aprobado_administracion', 'SI')
                    ->whereRaw('(sino_aprobado_finalizada IS NULL OR sino_aprobado_finalizada = "NO")')
                    ->get();                
            }
            if($estado == 'd') {
                $titulo .= ' (Desprobadas)';
                $Solicitudes = Solicitud::whereRaw('((sino_aprobado_administracion = "NO" OR sino_aprobado_garantes = "NO") AND (sino_aprobado_solicitar_revision = "NO" OR sino_aprobado_solicitar_revision IS NULL))')
                ->get();
            }
            if($estado == 'f') {
                $titulo .= ' (Finalizadas)';
                $Solicitudes = Solicitud::where('sino_aprobado_finalizada', 'SI')->get();                
            }
        }
        return View('solicitudes/solicitudeseee')
        ->with('titulo', $titulo)
        ->with('Solicitudes', $Solicitudes);
    }

    public function traerElementosPaginaSolicitud($solicitud_id)
    {   
        $Solicitud = Solicitud::find($solicitud_id);
        $ComponentesDeModeloSolicitud = Composicion_de_modelo_de_solicitud::
            where('solicitud_id', $solicitud_id)
            ->get();


        $GenericController = new GenericController();
        $nombre = 'lista_de_precio_id';
        $nombre_a_mostrar = 'Forma de Pago';
        $tipo = 'INT';
        $longitud = 10;
        $campo_fk = $GenericController->CampoFK('solicitudes', $nombre);
        $nulo = 'NO';
        $gen_accion = 'A';
        $valor_del_campo = $Solicitud->lista_de_precio_id;
        $hidden = 'NO';
        $filtro_campo = array('zona_id');
        $filtro_valor = array($Solicitud->User->Sucursal->Zona->id);
        $onChange = "";
        
        $schemaVFG_lista_de_precios = $GenericController->armarSchemaVFG($nombre, $nombre_a_mostrar, $tipo, $longitud, $campo_fk, $nulo, $gen_accion, $valor_del_campo, $hidden, $filtro_campo, $filtro_valor, $onChange);

        // RECALCULO LOS M2 EN LA SOLICITUD
        $total_de_metros_cuadrados = $this->contarSuperficieSolicitud($solicitud_id);
        $Solicitud->total_de_metros_cuadrados = $total_de_metros_cuadrados;
        $Solicitud->save();

        $Parametro = Parametro::where('id', 1)->first();
        $parametro_anticipo_minimo = $Parametro->valor;

        $Lista_de_precio = Lista_de_precio::where('zona_id', Auth::user()->Sucursal->Zona->id)->get();
        $nombre_del_campo = 'lista_de_precio';
        $valoresSchemaVFG_lista_de_precios = $this->valoresParaSelectEx($Lista_de_precio, $nombre_del_campo);

        $Vendedores = Vendedor::where('sucursal_id', Auth::user()->Sucursal->id)->get();
        $nombre_del_campo = 'nombre';
        $valoresSchemaVFG_vendedores = $this->valoresParaSelectEx($Vendedores, $nombre_del_campo);

        $Cuotas = Cuota::where('solicitud_id', $solicitud_id)->get();



        return array('Solicitud' => $Solicitud, 'ComponentesDeModeloSolicitud' => $ComponentesDeModeloSolicitud, 'schemaVFG_lista_de_precios' => $schemaVFG_lista_de_precios, 'parametro_anticipo_minimo' => $parametro_anticipo_minimo, 'valoresSchemaVFG_lista_de_precios' => $valoresSchemaVFG_lista_de_precios, 'valoresSchemaVFG_vendedores' => $valoresSchemaVFG_vendedores, 'Cuotas' => $Cuotas);
    }


    public function editarSolicitud($solicitud_id)
    {   
        $elementosPS = $this->traerElementosPaginaSolicitud($solicitud_id);
        return View('solicitudes/solicitud')
        ->with('Solicitud', $elementosPS['Solicitud'])
        ->with('schemaVFG_lista_de_precios', $elementosPS['schemaVFG_lista_de_precios'])
        ->with('parametro_anticipo_minimo', $elementosPS['parametro_anticipo_minimo'])
        ->with('valoresSchemaVFG_lista_de_precios', $elementosPS['valoresSchemaVFG_lista_de_precios'])    
        ->with('valoresSchemaVFG_vendedores', $elementosPS['valoresSchemaVFG_vendedores'])    
        ->with('Cuotas', $elementosPS['Cuotas'])        
        ->with('ComponentesDeModeloSolicitud', $elementosPS['ComponentesDeModeloSolicitud']);
    }

    public function cambiarClienteSolicitud($solicitud_id, $cliente_id)
    {   
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->cliente_id = $cliente_id;
        $Solicitud->save();

        $elementosPS = $this->traerElementosPaginaSolicitud($solicitud_id);
        
        return View('solicitudes/solicitud')
        ->with('Solicitud', $elementosPS['Solicitud'])
        ->with('schemaVFG_lista_de_precios', $elementosPS['schemaVFG_lista_de_precios'])
        ->with('parametro_anticipo_minimo', $elementosPS['parametro_anticipo_minimo'])
        ->with('valoresSchemaVFG_lista_de_precios', $elementosPS['valoresSchemaVFG_lista_de_precios'])
        ->with('valoresSchemaVFG_vendedores', $elementosPS['valoresSchemaVFG_vendedores'])    
        ->with('Cuotas', $elementosPS['Cuotas'])          
        ->with('ComponentesDeModeloSolicitud', $elementosPS['ComponentesDeModeloSolicitud'])
        ->with('mensaje', $mensaje);
    }


    public function cambiarModeloSolicitud($solicitud_id, $modelo_id)
    {   
        $Solicitud = $this->grabarNuevoModeloyComponentes($solicitud_id, $modelo_id);

        $mensaje = 'Modelo actualizado';

        $elementosPS = $this->traerElementosPaginaSolicitud($solicitud_id);
        
        return View('solicitudes/solicitud')
        ->with('Solicitud', $elementosPS['Solicitud'])
        ->with('schemaVFG_lista_de_precios', $elementosPS['schemaVFG_lista_de_precios'])
        ->with('parametro_anticipo_minimo', $elementosPS['parametro_anticipo_minimo'])
        ->with('valoresSchemaVFG_lista_de_precios', $elementosPS['valoresSchemaVFG_lista_de_precios'])  
        ->with('valoresSchemaVFG_vendedores', $elementosPS['valoresSchemaVFG_vendedores'])    
        ->with('Cuotas', $elementosPS['Cuotas'])        
        ->with('ComponentesDeModeloSolicitud', $elementosPS['ComponentesDeModeloSolicitud'])
        ->with('mensaje', $mensaje);

    }

    public function aprobacionAdministracion($solicitud_id)
    {   
        $sino_aprobado_administracion = $_POST['sino_aprobado_administracion'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->sino_aprobado_administracion = $sino_aprobado_administracion;
        if ($sino_aprobado_administracion == 'SI') {
            $mensaje = 'Solicitud Aprobada';
            $Solicitud->observaciones_aprobado_administracion = NULL;
        }
        else {
            $Solicitud->observaciones_aprobado_administracion = $_POST['observaciones_aprobado_administracion'];
            $mensaje = 'Solicitud Desaprobada';
        }
        $Solicitud->save();


        $elementosPS = $this->traerElementosPaginaSolicitud($solicitud_id);
        return redirect()->back()->withErrors([false, $mensaje]);
    }


    public function aprobacionGarantes($solicitud_id)
    {   
        $sino_aprobado_garantes = $_POST['sino_aprobado_garantes'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->sino_aprobado_garantes = $sino_aprobado_garantes;
        if ($sino_aprobado_garantes == 'SI') {
            $Solicitud->observaciones_aprobado_garantes = NULL;
        }
        $Solicitud->save();

        return $sino_aprobado_garantes;
    }



    public function aprobacionFinalizada($solicitud_id)
    {   
        $sino_aprobado_finalizada = $_POST['sino_aprobado_finalizada'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->sino_aprobado_finalizada = $sino_aprobado_finalizada;
        if ($sino_aprobado_finalizada == 'SI') {
            $Solicitud->observaciones_aprobado_finalizada = NULL;
        }
        $Solicitud->save();

        return $sino_aprobado_finalizada;
    }



    public function aprobacionSolicitarRevision($solicitud_id)
    {   
        $sino_aprobado_solicitar_revision = $_POST['sino_aprobado_solicitar_revision'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->sino_aprobado_solicitar_revision = $sino_aprobado_solicitar_revision;
        $Solicitud->save();

        return $sino_aprobado_solicitar_revision;
    }


    public function guardarObsAdm($solicitud_id)
    {   
        $observaciones_aprobado_administracion = $_POST['observaciones_aprobado_administracion'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->observaciones_aprobado_administracion = $observaciones_aprobado_administracion;
        $Solicitud->save();
    }

    public function guardarObsGar($solicitud_id)
    {   
        $observaciones_aprobado_garantes = $_POST['observaciones_aprobado_garantes'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->observaciones_aprobado_garantes = $observaciones_aprobado_garantes;
        $Solicitud->save();
    }


    public function guardarObsSolRev($solicitud_id)
    {   
        $observaciones_aprobado_solicitar_revision = $_POST['observaciones_aprobado_solicitar_revision'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->observaciones_aprobado_solicitar_revision = $observaciones_aprobado_solicitar_revision;
        $Solicitud->save();
    }

    public function guardarObsFin($solicitud_id)
    {   
        $observaciones_aprobado_finalizada = $_POST['observaciones_aprobado_finalizada'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->observaciones_aprobado_finalizada = $observaciones_aprobado_finalizada;
        $Solicitud->save();
    }


    public function valoresParaSelectEx($filas, $nombre_del_campo)
    {
        $valores = '';
        foreach ($filas as $fila) { 
            $valores .= '{ id: '.$fila['id'].', name: "'.$fila[$nombre_del_campo].'" }, ';
        }

        return $valores;

    }   


    public function listarClientesParaSeleccion()
    {
        $gen_modelo = 'Cliente';
        $gen_opcion = 0;
        $acciones_extra = array('Seleccionar,fa fa-hand-pointer-o,Solicitudes/crear/elegir-cliente');
        $gen_seteo['filtros_por_campo'] = array('sucursal_id' => Auth::user()->sucursal_id);
        $gen_seteo['gen_url_siguiente'] = 'back';

        $gen_campos_a_ocultar = array('empresa_id', 'tipo_de_documento_id', 'domicilio', 'provincia', 'email_correo', 'pais', 'observaciones', 'zona_local_id', 'sucursal_id', 'user_id');
  
        $GenericController = new GenericController();
        $gen_campos = $GenericController->traer_campos($gen_modelo, $gen_campos_a_ocultar);
        $gen_permisos = [
            'C',
            'R'
            ];

        $gen_filas = Cliente::all();

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

    public function crearSolicitudElegirCliente($cliente_id)
    {   
        //Session::put('cliente_id', $cliente_id);

        $Solicitud = new Solicitud;
        $Solicitud->cliente_id = $cliente_id;
        $Solicitud->user_id = Auth::user()->id;
        $Solicitud->sucursal_id = Auth::user()->sucursal_id;
        $Solicitud->save();

        $nombre_del_cliente = "Cliente: ".$Solicitud->Cliente->nombre.' '.$Solicitud->Cliente->apellido;
        $pasos_info = array($nombre_del_cliente);
        
        return View('solicitudes/solicitud-asistente')
        ->with('solicitud_id', $Solicitud->id)
        ->with('paso', 2)
        ->with('pasos_info', $pasos_info);               
    }

    public function listarModelosParaSeleccion($solicitud_id)
    {  
        $gen_modelo = 'Modelo';
        $gen_opcion = 0;
        $acciones_extra = array('Seleccionar,fa fa-hand-pointer-o,Solicitudes/crear/elegir-modelo/'.$solicitud_id);
        $Sucursal = Sucursal::where('id', Auth::user()->sucursal_id)->get();
        $gen_seteo['filtros_por_campo'] = array('empresa_id' => $Sucursal[0]->Zona->empresa_id);
        $gen_seteo['gen_url_siguiente'] = 'back';

        $gen_campos_a_ocultar = array('empresa_id', 'sino_activo');
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
            'R'
            ];

        $gen_filas = Modelo::where('sino_activo', 'SI')->get();

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

    public function grabarNuevoModeloyComponentes($solicitud_id, $modelo_id)
    {   

        // GRABO EL MODELO EN LA SOLICITUD
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->modelo_id = $modelo_id;
        $Solicitud->save();

        $ComponentesDeModeloSolicitud = Composicion_de_modelo_de_solicitud::
            where('solicitud_id', $solicitud_id)
            ->where('modelo_id', $modelo_id)
            ->get();
            
        // cargo si no hay componentes cargados
        if (count($ComponentesDeModeloSolicitud) == 0) {
            //Borro los componentes que halla cargados de modelos distintos
            $ComponentesDeModeloSolicitudABorrar = Composicion_de_modelo_de_solicitud::where('solicitud_id', $solicitud_id)->get();
            if (count($ComponentesDeModeloSolicitudABorrar) > 0) {
                foreach ($ComponentesDeModeloSolicitudABorrar as $Componente) {
                    $Componente->delete();
                }
            }

            $ComponentesDeModelo = Composicion_de_modelo::where('modelo_id', $modelo_id)->get();
            foreach ($ComponentesDeModelo as $Componente) {
                $Componente_de_solicitud = new Composicion_de_modelo_de_solicitud;
                $Componente_de_solicitud->solicitud_id = $solicitud_id;
                $Componente_de_solicitud->modelo_id = $Solicitud->Modelo->id;
                $Componente_de_solicitud->articulo_id = $Componente->articulo_id;
                $Componente_de_solicitud->ancho = $Componente->ancho;
                $Componente_de_solicitud->largo = $Componente->largo;
                $Componente_de_solicitud->observaciones = $Componente->observaciones;
                $Componente_de_solicitud->save();          
            }
        }

        
        // GRABO LOS M2 EN LA SOLICITUD
        $total_de_metros_cuadrados = $this->contarSuperficieSolicitud($solicitud_id);
        $Solicitud->total_de_metros_cuadrados = $total_de_metros_cuadrados;
        $Solicitud->save();

        return $Solicitud;

    }

    public function crearSolicitudElegirModelo($solicitud_id, $modelo_id)
    {   
        $Solicitud = $this->grabarNuevoModeloyComponentes($solicitud_id, $modelo_id);
        $total_de_metros_cuadrados = $this->contarSuperficieSolicitud($solicitud_id);

        //PASO LOS DATOS AL STEP
        $nombre_del_cliente = "Cliente: ".$Solicitud->Cliente->nombre.' '.$Solicitud->Cliente->apellido;
        $modelo = "Modelo: ".$Solicitud->Modelo->modelo;
        $leyenda_total_de_metros_cuadrados = '';
        if ($total_de_metros_cuadrados > 0) {
            $leyenda_total_de_metros_cuadrados = "Superficie: $total_de_metros_cuadrados m<sup>2</sup>";
        }        
        $pasos_info = array($nombre_del_cliente, $modelo, $leyenda_total_de_metros_cuadrados);

        return View('solicitudes/solicitud-asistente')        
        ->with('solicitud_id', $Solicitud->id)
        ->with('paso', 3)
        ->with('pasos_info', $pasos_info)
        ->with('total_de_metros_cuadrados', $total_de_metros_cuadrados);               
    }


    public function listarComposicionModeloParaSeleccion($solicitud_id)
    {  
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $modelo_id = $Solicitud->modelo_id;

        $gen_modelo = 'Composicion_de_modelo_de_solicitud';
        $gen_opcion = 0;
        $acciones_extra = array();
        $gen_seteo['filtros_por_campo'] = array('solicitud_id' => $solicitud_id, 'modelo_id' => $modelo_id);
        $gen_seteo['gen_url_siguiente'] = 'back';

        $gen_campos_a_ocultar = array('solicitud_id');
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

        $gen_filas = Composicion_de_modelo_de_solicitud::where('solicitud_id', $solicitud_id)->get();

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

    public function cargarComponentesASolicitud ($solicitud_id) {
        
    }


    public function crearSolicitudElegirFormaDePago($solicitud_id)
    {   
        $total_de_metros_cuadrados = $this->contarSuperficieSolicitud($solicitud_id);
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->total_de_metros_cuadrados = $total_de_metros_cuadrados;
        $Solicitud->save();

        $nombre_del_cliente = "Cliente: ".$Solicitud->Cliente->nombre.' '.$Solicitud->Cliente->apellido;
        $modelo = "Modelo: ".$Solicitud->Modelo->modelo;
        $leyenda_total_de_metros_cuadrados = "Superficie: ".$Solicitud->total_de_metros_cuadrados." m<sup>2</sup>";
        $pasos_info = array($nombre_del_cliente, $modelo, $leyenda_total_de_metros_cuadrados);
        

        $Lista_de_precio = Lista_de_precio::where('zona_id', Auth::user()->Sucursal->Zona->id)->get();
        $nombre_del_campo = 'lista_de_precio';
        $valoresSchemaVFG_lista_de_precios = $this->valoresParaSelectEx($Lista_de_precio, $nombre_del_campo);


        $Vendedores = Vendedor::where('sucursal_id', Auth::user()->Sucursal->id)->get();
        $nombre_del_campo = 'nombre';
        $valoresSchemaVFG_vendedores = $this->valoresParaSelectEx($Vendedores, $nombre_del_campo);
        

        $Parametro = Parametro::where('id', 1)->first();
        $parametro_anticipo_minimo = $Parametro->valor;

        return View('solicitudes/solicitud-asistente')        
        ->with('solicitud_id', $solicitud_id)     
        ->with('Solicitud', $Solicitud)     
        ->with('total_de_metros_cuadrados', $total_de_metros_cuadrados)     
        ->with('valoresSchemaVFG_lista_de_precios', $valoresSchemaVFG_lista_de_precios)
        ->with('valoresSchemaVFG_vendedores', $valoresSchemaVFG_vendedores)
        ->with('parametro_anticipo_minimo', $parametro_anticipo_minimo)
        ->with('paso', 4)
        ->with('pasos_info', $pasos_info);               
    }



    public function GuardarFormaDePagoyResumenParaEnvio(Request $request) {

        $gCon = new GenericController();

        $solicitud_id = $_POST['solicitud_id'];
        $fecha_de_vencimiento_de_la_solicitud = $gCon->FormatoFecha($_POST['fecha_de_vencimiento_de_la_solicitud']);

        $Lista_de_precio = Lista_de_precio::where('id', $_POST['lista_de_precio_id'])->first();
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->lista_de_precio_id = $_POST['lista_de_precio_id'];
        $Solicitud->vendedor_id = $_POST['vendedor_id'];
        $Solicitud->moneda_id = $Lista_de_precio->moneda_id;
        $Solicitud->fecha_de_vencimiento_de_la_solicitud = $fecha_de_vencimiento_de_la_solicitud;
        if (isset($_POST['anticipo'])) {
            $anticipo = $_POST['anticipo'];
            $fecha_de_cancelacion_del_anticipo = $gCon->FormatoFecha($_POST['fecha_de_cancelacion_del_anticipo']);
            $Solicitud->anticipo = $anticipo;
            $Solicitud->fecha_de_cancelacion_del_anticipo = $fecha_de_cancelacion_del_anticipo;
            $Solicitud->cuotas_anticipo = 0;
            if ($anticipo > 0) {
                $sino_contado = $_POST['sino_contado'];
                $Solicitud->sino_contado = $sino_contado;
                if ($sino_contado == 'NO') {
                    $Solicitud->cuotas_anticipo = $_POST['cuotas_anticipo'];
                }
                else {
                    $Solicitud->cuotas_anticipo = NULL; 
                }
            }
        }
        else {
            $Solicitud->anticipo = NULL; 
            $Solicitud->fecha_de_cancelacion_del_anticipo = NULL; 
            $Solicitud->cuotas_anticipo = NULL; 
            $Solicitud->sino_contado = NULL; 
        }

        $Solicitud->observaciones = $_POST['observaciones'];
        $Solicitud->valor_total = $_POST['valor_total'];

        $Solicitud->save();    

        $nombre_del_cliente = "Cliente: ".$Solicitud->Cliente->nombre.' '.$Solicitud->Cliente->apellido;
        $modelo = "Modelo: ".$Solicitud->Modelo->modelo;
        $leyenda_total_de_metros_cuadrados = "Superficie: ".$Solicitud->total_de_metros_cuadrados." m<sup>2</sup>";
        $forma_de_pago = "Forma de Pago: ".$Solicitud->Lista_de_precio->lista_de_precio;
        $pasos_info = array($nombre_del_cliente, $modelo, $leyenda_total_de_metros_cuadrados, $forma_de_pago, 'Revisi&oacute;n');

        $ComponentesDeModeloSolicitud = Composicion_de_modelo_de_solicitud::
            where('solicitud_id', $solicitud_id)
            ->get();

        if (isset($_POST['origen'])) {

            $mensaje = 'Solicitud actualizada';
            $elementosPS = $this->traerElementosPaginaSolicitud($solicitud_id);
            
            return View('solicitudes/solicitud')
            ->with('Solicitud', $elementosPS['Solicitud'])
            ->with('schemaVFG_lista_de_precios', $elementosPS['schemaVFG_lista_de_precios'])
            ->with('parametro_anticipo_minimo', $elementosPS['parametro_anticipo_minimo'])
            ->with('valoresSchemaVFG_lista_de_precios', $elementosPS['valoresSchemaVFG_lista_de_precios'])  
            ->with('valoresSchemaVFG_vendedores', $elementosPS['valoresSchemaVFG_vendedores'])    
            ->with('Cuotas', $elementosPS['Cuotas'])        
            ->with('ComponentesDeModeloSolicitud', $elementosPS['ComponentesDeModeloSolicitud'])
            ->with('mensaje', $mensaje);
        }
        else {
            return View('solicitudes/solicitud-asistente')        
            ->with('solicitud_id', $solicitud_id)        
            ->with('Solicitud', $Solicitud)          
            ->with('ComponentesDeModeloSolicitud', $ComponentesDeModeloSolicitud)       
            ->with('pasos_info', $pasos_info)     
            ->with('paso', 5);                 
        }

    }

    public function traerValoresPrecio() {
        $lista_de_precio_id = $_POST['lista_de_precio_id'];
        $solicitud_id = $_POST['solicitud_id'];
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $modelo_id = $Solicitud->modelo_id;        

        $Precio = Precio::where('lista_de_precio_id', $lista_de_precio_id)->where('modelo_id', $modelo_id)->first();
        if (count($Precio) > 0) {
            $precio_m2 = $Precio->precio;    
        }
        else {
            $precio_m2 = 0;    
        }
        
        $total_de_metros_cuadrados = $this->contarSuperficieSolicitud($solicitud_id);

        $valor_total = $precio_m2 * $total_de_metros_cuadrados;
        $forma_de_pago = $Precio->Lista_de_precio->Forma_de_pago->id;
        
        return $valor_total.'|'.$forma_de_pago;
    }

    public function contarSuperficieSolicitud($solicitud_id) {

        /*
        $ComponentesDeModeloSolicitud = Composicion_de_modelo_de_solicitud::
            where('solicitud_id', $solicitud_id)
            ->get();
        $total_de_metros_cuadrados = 0;
        foreach ($ComponentesDeModeloSolicitud as $Componente) {
            $total_de_metros_cuadrados = $total_de_metros_cuadrados +($Componente->ancho*$Componente->largo);
        }
        */
        $Solicitud = Solicitud::find($solicitud_id);
        $Solicitud->contarSuperficieSolicitud();
        $total_de_metros_cuadrados = $Solicitud->total_de_metros_cuadrados;

        return $total_de_metros_cuadrados;
    } 



    public function enviarSolicitud($solicitud_id)
    {   
        $total_de_metros_cuadrados = $this->contarSuperficieSolicitud($solicitud_id);
        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $now = new \DateTime();
        $Solicitud->fecha_de_firma_de_solicitud = $now->format('Y-m-d H:i:s');
        $Solicitud->save();

        $nombre_del_cliente = "Cliente: ".$Solicitud->Cliente->nombre.' '.$Solicitud->Cliente->apellido;
        $modelo = "Modelo: ".$Solicitud->Modelo->modelo;
        $leyenda_total_de_metros_cuadrados = "Superficie: ".$Solicitud->total_de_metros_cuadrados." m<sup>2</sup>";
        $forma_de_pago = "Forma de Pago: ".$Solicitud->Lista_de_precio->lista_de_precio;
        $pasos_info = array($nombre_del_cliente, $modelo, $leyenda_total_de_metros_cuadrados, $forma_de_pago, 'Revisi&oacute;n', 'Solicitud Enviada');

        return View('solicitudes/solicitud-asistente')        
        ->with('solicitud_id', $solicitud_id)     
        ->with('paso', 6)
        ->with('pasos_info', $pasos_info);               
    }


    public function imprimirSolicitud($solicitud_id) {

        //PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        //$pdf = PDF::loadView('pdfview');

        //$pdf = PDF::loadView('pruebaparapdf');
        //return $pdf->download();
        $gCon = new GenericController();
        $fcx = new FxC();
        $Solicitud = Solicitud::find($solicitud_id);
        $Reporte = Reporte::find(1);

        $total_de_metros_cuadrados = $gCon->formatoNumero($Solicitud->total_de_metros_cuadrados, 'decimal');

        $pdf = \App::make('dompdf.wrapper');

        $Composicion_de_modelo_de_solicitud = Composicion_de_modelo_de_solicitud::where('solicitud_id', $solicitud_id)->get();

        //COMPONENTES
        $solicitud_componentes = '<br><br>';
        foreach ($Composicion_de_modelo_de_solicitud as $Componente) {
            $largo = $gCon->formatoNumero($Componente->largo, 'decimal');
            $ancho = $gCon->formatoNumero($Componente->ancho, 'decimal');
            $can_letras_art = strlen($Componente->Articulo->articulo);
            $relleno_de_giones = ' ';
            for($i=$can_letras_art; $i<=55; $i++) {
                $relleno_de_giones .= '- ';
            }
            $solicitud_componentes .= '1 '.$Componente->Articulo->articulo.$relleno_de_giones.$largo.'m x '.$ancho.'m<br>';
            # code...
        }
        $solicitud_componentes .= '<br><br>';

        //SUMA TOTAL
        $moneda = $Solicitud->Moneda->moneda;
        $valor_total = $Solicitud->valor_total;
        $valor_total_entero = intval($valor_total);
        $valor_total_centavos_x_100 = ($valor_total - $valor_total_entero) * 100;
        $valor_total_formateado = $gCon->formatoNumero($valor_total, 'decimal');
        $valor_total_a_texto = $fcx->functionNumeroALetras($valor_total);
        $solicitud_suma_total = "$moneda $valor_total_a_texto y $valor_total_centavos_x_100 / 100 centavos ($valor_total_formateado)";

        //ANTICIPO
        $anticipo = $Solicitud->anticipo;
        $anticipo_entero = intval($anticipo);
        $anticipo_centavos_x_100 = ($anticipo - $anticipo_entero) * 100;
        $anticipo_formateado = $gCon->formatoNumero($anticipo, 'decimal');
        $anticipo_a_texto = $fcx->functionNumeroALetras($anticipo);
        $solicitud_anticipo = "$moneda $anticipo_a_texto y $anticipo_centavos_x_100 / 100 centavos ($anticipo_formateado)";

        //RESTO
        $resto = $valor_total - $anticipo;
        $resto_entero = intval($resto);
        $resto_centavos_x_100 = ($resto - $resto_entero) * 100;
        $resto_formateado = $gCon->formatoNumero($resto, 'decimal');
        $resto_a_texto = $fcx->functionNumeroALetras($resto);
        $solicitud_resto = "$moneda $resto_a_texto y $resto_centavos_x_100 / 100 centavos ($resto_formateado)";

        $fecha_de_firma_de_solicitud = strtotime($Solicitud->fecha_de_firma_de_solicitud);
        $solicitud_dia = date("d", $fecha_de_firma_de_solicitud);
        $numero_de_mes_firma_de_solicitud = date("m", $fecha_de_firma_de_solicitud);
        $solicitud_mes = $fcx->nombre_de_mes($numero_de_mes_firma_de_solicitud);
        $solicitud_anio = date("Y", $fecha_de_firma_de_solicitud);

        $patrones = array();
        $patrones[0] = '/cliente_id/';
        $patrones[1] = '/cliente_nombre/';
        $patrones[2] = '/cliente_apellido/';
        $patrones[3] = '/cliente_tipo_de_documento/';
        $patrones[4] = '/cliente_nro_de_documento/';
        $patrones[5] = '/cliente_domicilio/';
        $patrones[6] = '/cliente_localidad/';
        $patrones[7] = '/cliente_provincia/';
        $patrones[8] = '/solicitud_modelo/';
        $patrones[9] = '/solicitud_total_de_metros_cuadrados/';
        $patrones[10] = '/solicitud_componentes/';
        $patrones[11] = '/solicitud_suma_total/';
        $patrones[12] = '/solicitud_anticipo/';
        $patrones[13] = '/solicitud_resto/';
        $patrones[14] = '/solicitud_dia/';
        $patrones[15] = '/solicitud_mes/';
        $patrones[16] = '/solicitud_anio/';
        $sustituciones = array();
        $sustituciones[0] = $Solicitud->cliente_id;
        $sustituciones[1] = $Solicitud->Cliente->nombre;
        $sustituciones[2] = $Solicitud->Cliente->apellido;
        $sustituciones[3] = $Solicitud->Cliente->Tipo_de_documento->tipo_de_documento;
        $sustituciones[4] = $Solicitud->Cliente->nro_de_documento;
        $sustituciones[5] = $Solicitud->Cliente->domicilio;
        $sustituciones[6] = $Solicitud->Cliente->localidad;
        $sustituciones[7] = $Solicitud->Cliente->provincia;
        $sustituciones[8] = $Solicitud->Modelo->modelo;
        $sustituciones[9] = $total_de_metros_cuadrados;
        $sustituciones[10] = $solicitud_componentes;
        $sustituciones[11] = $solicitud_suma_total;
        $sustituciones[12] = $solicitud_anticipo;
        $sustituciones[13] = $solicitud_resto;
        $sustituciones[14] = $solicitud_dia;
        $sustituciones[15] = $solicitud_mes;
        $sustituciones[16] = $solicitud_anio;


        $encabezado = '<html>';
        $encabezado .= '<head>';
        $encabezado .= '  <style>';
        $encabezado .= '    @page { margin-top: 80px; font-family: Arial, Helvetica, sans-serif; font-size: 13px}';
        $encabezado .= '    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 100px; margin-top: 70px }';
        $encabezado .= '    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }';
        //$encabezado .= '    p { page-break-after: always; }';
        //$encabezado .= '    p:last-child { page-break-after: never; }';
        $encabezado .= '  </style>';
        $encabezado .= '</head>';
        $encabezado .= '<body>';
        $encabezado .= '<header><p style="text-align: right"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></p></header>';
        //$encabezado .= '<header><p style="text-align: right"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></p></header><footer>info pie</footer>';


        $texto_pdf = $encabezado;

        $texto_pdf .= preg_replace($patrones, $sustituciones, $Reporte->rtf_cuerpo);

        $texto_pdf .= '<div style="page-break-after: always;"></div>';

        $Reporte = Reporte::find(2);

        $texto_pdf .= preg_replace($patrones, $sustituciones, $Reporte->rtf_cuerpo);

        $texto_pdf .= '<footer style="text-align: center; font-size: 10px; font-weight: bold"><a href="http://www.viviendastecnohouse.com.ar" target="_blank">www.viviendastecnohouse.com.ar</a></footer>';

        $texto_pdf .= '</body></html>';

        $pdf->loadHTML($texto_pdf);

        return $pdf->stream();      
        
    }




    public function GuardarDistribucionDeCuotas(Request $request) {

        $gCon = new GenericController();

        $solicitud_id = $_POST['solicitud_id'];
        $cant_cuotas_calculadas = $_POST['cant_cuotas_calculadas'];

        $Solicitud = Solicitud::where('id', $solicitud_id)->first();
        $Solicitud->cuotas_contrato = $cant_cuotas_calculadas;
        $Solicitud->save();    

        $deletedRows = Cuota::where('solicitud_id', $solicitud_id)->delete();
        $td_fields = explode('|', $_POST['td_fields']);

        for ($i=0; $i<count($td_fields)-1; $i++) {

            $array_td_fields = explode('#', $td_fields[$i]);

            $numero_de_cuota = $array_td_fields[0];
            $importe = $array_td_fields[1];
            $porcentaje = $array_td_fields[2];
            $fecha_de_vencimiento = $array_td_fields[3];

            $Cuota = new Cuota;
            $Cuota->solicitud_id = $solicitud_id;
            $Cuota->numero_de_cuota = $numero_de_cuota;
            $Cuota->importe = $importe;
            $Cuota->porcentaje = $porcentaje;
            $Cuota->fecha_de_vencimiento = $fecha_de_vencimiento;
            $Cuota->save();

        }

        $mensaje = 'Solicitud actualizada';
        $elementosPS = $this->traerElementosPaginaSolicitud($solicitud_id);
        
        return View('solicitudes/solicitud')
        ->with('Solicitud', $elementosPS['Solicitud'])
        ->with('schemaVFG_lista_de_precios', $elementosPS['schemaVFG_lista_de_precios'])
        ->with('parametro_anticipo_minimo', $elementosPS['parametro_anticipo_minimo'])
        ->with('valoresSchemaVFG_lista_de_precios', $elementosPS['valoresSchemaVFG_lista_de_precios'])  
        ->with('valoresSchemaVFG_vendedores', $elementosPS['valoresSchemaVFG_vendedores'])    
        ->with('Cuotas', $elementosPS['Cuotas'])        
        ->with('ComponentesDeModeloSolicitud', $elementosPS['ComponentesDeModeloSolicitud'])
        ->with('mensaje', $mensaje);

    }


    public function guardarFechaDeContrato(Request $request) {

        $gCon = new GenericController();
        $solicitud_id = $_POST['solicitud_id'];
        $fecha_de_contrato = $gCon->FormatoFecha($_POST['fecha_de_contrato']);
        $observaciones_contrato = $_POST['observaciones_contrato'];
        $pagado = $_POST['pagado'];

        $Solicitud = Solicitud::find($solicitud_id);
        $Solicitud->fecha_de_contrato = $fecha_de_contrato;
        if ($observaciones_contrato <> '') {
            $Solicitud->observaciones_contrato = $observaciones_contrato;
        }
        if ($pagado <> '') {
            $Solicitud->pagado = $pagado;
        }
        $Solicitud->save();
    }



    public function imprimirContrato($solicitud_id) {

        //PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        //$pdf = PDF::loadView('pdfview');

        //$pdf = PDF::loadView('pruebaparapdf');
        //return $pdf->download();
        $gCon = new GenericController();
        $fcx = new FxC();
        $Solicitud = Solicitud::find($solicitud_id);

        $forma_de_pago = $Solicitud->Lista_de_precio->Forma_de_pago->id;

        if ($forma_de_pago == 1) {
            $reporte_id = 3;
        }
        if ($forma_de_pago == 2) {
            $reporte_id = 4;
        }
        if ($forma_de_pago == 3) {
            $reporte_id = 5;
        }
        if ($forma_de_pago == 4) {
            $reporte_id = 6;
        }


        $Reporte = Reporte::find($reporte_id);

        $pdf = \App::make('dompdf.wrapper');

        $total_de_metros_cuadrados = $gCon->formatoNumero($Solicitud->total_de_metros_cuadrados, 'decimal');

        $Composicion_de_modelo_de_solicitud = Composicion_de_modelo_de_solicitud::where('solicitud_id', $solicitud_id)->get();

        //COMPONENTES
        $solicitud_componentes = '<br><br>';
        foreach ($Composicion_de_modelo_de_solicitud as $Componente) {
            $largo = $gCon->formatoNumero($Componente->largo, 'decimal');
            $ancho = $gCon->formatoNumero($Componente->ancho, 'decimal');
            $can_letras_art = strlen($Componente->Articulo->articulo);
            $relleno_de_giones = ' ';
            for($i=$can_letras_art; $i<=55; $i++) {
                $relleno_de_giones .= '- ';
            }
            $solicitud_componentes .= '1 '.$Componente->Articulo->articulo.$relleno_de_giones.$largo.'m x '.$ancho.'m<br>';
            # code...
        }
        $solicitud_componentes .= '<br><br>';

        //SUMA TOTAL
        $moneda = $Solicitud->Moneda->moneda;
        $valor_total = $Solicitud->valor_total;
        $valor_total_entero = intval($valor_total);
        $valor_total_centavos_x_100 = ($valor_total - $valor_total_entero) * 100;
        $valor_total_formateado = $gCon->formatoNumero($valor_total, 'decimal');
        $valor_total_a_texto = $fcx->functionNumeroALetras($valor_total);
        $solicitud_suma_total = "<strong>$ $valor_total_formateado ($moneda $valor_total_a_texto y $valor_total_centavos_x_100 / 100 centavos)</strong>";

        //ANTICIPO
        $anticipo = $Solicitud->anticipo;
        $anticipo_entero = intval($anticipo);
        $anticipo_centavos_x_100 = ($anticipo - $anticipo_entero) * 100;
        $anticipo_formateado = $gCon->formatoNumero($anticipo, 'decimal');
        $anticipo_a_texto = $fcx->functionNumeroALetras($anticipo);
        $solicitud_anticipo = "$ $anticipo_formateado ($moneda $anticipo_a_texto y $anticipo_centavos_x_100 / 100 centavos)";

        //SALDO
        $saldo = $valor_total - $anticipo;
        $saldo_entero = intval($saldo);
        $saldo_centavos_x_100 = ($saldo - $saldo_entero) * 100;
        $saldo_formateado = $gCon->formatoNumero($saldo, 'decimal');
        $saldo_a_texto = $fcx->functionNumeroALetras($saldo);
        $solicitud_saldo = "$ $saldo_formateado ($moneda $saldo_a_texto y $saldo_centavos_x_100 / 100 centavos)";

        //SALDO50 MENOS ANTICIPO
        $saldo50_menos_anticipo = ($valor_total/2) - $anticipo;
        $saldo50_menos_anticipo_entero = intval($saldo50_menos_anticipo);
        $saldo50_menos_anticipo_centavos_x_100 = ($saldo50_menos_anticipo - $saldo50_menos_anticipo_entero) * 100;
        $saldo50_menos_anticipo_formateado = $gCon->formatoNumero($saldo50_menos_anticipo, 'decimal');
        $saldo50_menos_anticipo_a_texto = $fcx->functionNumeroALetras($saldo50_menos_anticipo);
        $solicitud_saldo50_menos_anticipo = "$ $saldo50_menos_anticipo_formateado ($moneda $saldo50_menos_anticipo_a_texto y $saldo50_menos_anticipo_centavos_x_100 / 100 centavos)";

        //SALDO50 
        $saldo50 = ($valor_total/2);
        $saldo50_entero = intval($saldo50);
        $saldo50_centavos_x_100 = ($saldo50 - $saldo50_entero) * 100;
        $saldo50_formateado = $gCon->formatoNumero($saldo50, 'decimal');
        $saldo50_a_texto = $fcx->functionNumeroALetras($saldo50);
        $solicitud_saldo50 = "$ $saldo50_formateado ($moneda $saldo50_a_texto y $saldo50_centavos_x_100 / 100 centavos)";


        //MONTO CUOTAS ANTICIPO
        if ($Solicitud->cuotas_anticipo > 0) {
            $monto_cuotas_anticipo = $Solicitud->anticipo / $Solicitud->cuotas_anticipo;
            $monto_cuotas_anticipo_entero = intval($monto_cuotas_anticipo);
            $monto_cuotas_anticipo_centavos_x_100 = ($monto_cuotas_anticipo - $monto_cuotas_anticipo_entero) * 100;
            $monto_cuotas_anticipo_formateado = $gCon->formatoNumero($monto_cuotas_anticipo, 'decimal');
            $monto_cuotas_anticipo_a_texto = $fcx->functionNumeroALetras($monto_cuotas_anticipo);
            $solicitud_monto_cuotas_anticipo = "$ $monto_cuotas_anticipo_formateado ($moneda $monto_cuotas_anticipo_a_texto y $monto_cuotas_anticipo_centavos_x_100 / 100 centavos)";
        }
        else {
            $solicitud_monto_cuotas_anticipo = '';
        }
        

        $Cuotas = Cuota::where('solicitud_id', $solicitud_id)->get();
        $solicitud_cuotas_contrato = count($Cuotas);
        if ($solicitud_cuotas_contrato > 0) {
            $solicitud_monto_cuotas_contrato = $Cuotas[0]->importe;
        }
        else {
            $solicitud_monto_cuotas_contrato = 0;
        }

        //MONTO CUOTAS CONTRATO
        $monto_cuotas_contrato = $Solicitud->monto_cuotas_contrato;
        $monto_cuotas_contrato_entero = intval($monto_cuotas_contrato);
        $monto_cuotas_contrato_centavos_x_100 = ($monto_cuotas_contrato - $monto_cuotas_contrato_entero) * 100;
        $monto_cuotas_contrato_formateado = $gCon->formatoNumero($monto_cuotas_contrato, 'decimal');
        $monto_cuotas_contrato_a_texto = $fcx->functionNumeroALetras($monto_cuotas_contrato);
        $solicitud_monto_cuotas_contrato = "$ $monto_cuotas_contrato_formateado ($moneda $monto_cuotas_contrato_a_texto y $monto_cuotas_contrato_centavos_x_100 / 100 centavos)";

        $fecha_de_contrato = strtotime($Solicitud->fecha_de_contrato);
        $contrato_dia = date("d", $fecha_de_contrato);
        $numero_de_mes_fecha_de_contrato = date("m", $fecha_de_contrato);
        $contrato_mes = $fcx->nombre_de_mes($numero_de_mes_fecha_de_contrato);
        $contrato_anio = date("Y", $fecha_de_contrato);



        $patrones = array();
        $patrones[0] = '/cliente_id/';
        $patrones[1] = '/cliente_nombre/';
        $patrones[2] = '/cliente_apellido/';
        $patrones[3] = '/cliente_tipo_de_documento/';
        $patrones[4] = '/cliente_nro_de_documento/';
        $patrones[5] = '/cliente_domicilio/';
        $patrones[6] = '/cliente_localidad/';
        $patrones[7] = '/cliente_provincia/';
        $patrones[8] = '/solicitud_modelo/';
        $patrones[9] = '/solicitud_total_de_metros_cuadrados/';
        $patrones[10] = '/solicitud_componentes/';
        $patrones[11] = '/solicitud_suma_total/';
        $patrones[12] = '/solicitud_anticipo/';
        $patrones[13] = '/solicitud_saldo50_menos_anticipo/';
        $patrones[14] = '/solicitud_saldo50/';
        $patrones[15] = '/solicitud_saldo/';
        $patrones[16] = '/contrato_dia/';
        $patrones[17] = '/contrato_mes/';
        $patrones[18] = '/contrato_anio/';
        $patrones[19] = '/solicitud_cuotas_anticipo/';
        $patrones[20] = '/solicitud_monto_cuotas_anticipo/';
        $patrones[21] = '/solicitud_cuotas_contrato/';
        $patrones[22] = '/solicitud_monto_cuotas_contrato/';
        $patrones[23] = '/sucursal_domicilio/';
        $patrones[24] = '/sucursal_localidad/';
        $patrones[24] = '/<ol>/';
        $sustituciones = array();
        $sustituciones[0] = $Solicitud->cliente_id;
        $sustituciones[1] = '<strong>'.$Solicitud->Cliente->nombre.'</strong>';
        $sustituciones[2] = '<strong>'.$Solicitud->Cliente->apellido.'</strong>';
        $sustituciones[3] = '<strong>'.$Solicitud->Cliente->Tipo_de_documento->tipo_de_documento.'</strong>';
        $sustituciones[4] = '<strong>'.$Solicitud->Cliente->nro_de_documento.'</strong>';
        $sustituciones[5] = '<strong>'.$Solicitud->Cliente->domicilio.'</strong>';
        $sustituciones[6] = '<strong>'.$Solicitud->Cliente->localidad->localidad.'</strong>';
        $sustituciones[7] = '<strong>'.$Solicitud->Cliente->localidad->provincia->provincia.'</strong>';
        $sustituciones[8] = '<strong>'.$Solicitud->Modelo->modelo.'</strong>';
        $sustituciones[9] = '<strong>'.$total_de_metros_cuadrados.'</strong>';
        $sustituciones[10] = $solicitud_componentes;
        $sustituciones[11] = '<strong>'.$solicitud_suma_total.'</strong>';
        $sustituciones[12] = '<strong>'.$solicitud_anticipo.'</strong>';
        $sustituciones[13] = '<strong>'.$solicitud_saldo50_menos_anticipo.'</strong>';
        $sustituciones[14] = '<strong>'.$solicitud_saldo50.'</strong>';
        $sustituciones[15] = '<strong>'.$solicitud_saldo.'</strong>';
        $sustituciones[16] = '<strong>'.$contrato_dia.'</strong>';
        $sustituciones[17] = '<strong>'.$contrato_mes.'</strong>';
        $sustituciones[18] = '<strong>'.$contrato_anio.'</strong>';
        $sustituciones[19] = '<strong>'.$Solicitud->cuotas_anticipo.'</strong>';
        $sustituciones[20] = '<strong>'.$solicitud_monto_cuotas_anticipo.'</strong>';
        $sustituciones[21] = '<strong>'.$solicitud_cuotas_contrato.'</strong>';
        $sustituciones[22] = '<strong>'.$solicitud_monto_cuotas_contrato.'</strong>';
        $sustituciones[23] = '<strong>'.$Solicitud->sucursal->domicilio.'</strong>';
        $sustituciones[24] = '<strong>'.$Solicitud->sucursal->localidad->localidad.'</strong>';
        $sustituciones[24] = '<ol type="A">';


        $encabezado = '<html>';
        $encabezado .= '<head>';
        $encabezado .= '  <style>';
        $encabezado .= '    @page { margin-top: 80px; font-family: Arial, Helvetica, sans-serif; font-size: 13px}';
        $encabezado .= '    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 100px; margin-top: 70px }';
        $encabezado .= '    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }';
        $encabezado .= '    p { text-align: justify; }';
        //$encabezado .= '    p { page-break-after: always; }';
        //$encabezado .= '    p:last-child { page-break-after: never; }';
        $encabezado .= '  </style>';
        $encabezado .= '</head>';
        $encabezado .= '<body>';
        $encabezado .= '<header><div style="float: left"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></div><div style="float: right; margin-right: 100px; margin-top: 80px; font-size: 15px; font-weight: bold">CONTRATO: '.$Solicitud->cliente_id.'</div></header>';
        //$encabezado .= '<header><p style="text-align: right"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></p></header><footer>info pie</footer>';


        $texto_pdf = $encabezado;

        $texto_pdf .= preg_replace($patrones, $sustituciones, $Reporte->rtf_cuerpo);

        //$texto_pdf .= '<div style="page-break-after: always;"></div>';

        //$Reporte = Reporte::find(2);

        //$texto_pdf .= preg_replace($patrones, $sustituciones, $Reporte->rtf_cuerpo);

        $texto_pdf .= '<footer style="text-align: center; font-size: 10px; font-weight: bold"><a href="http://www.viviendastecnohouse.com.ar" target="_blank">www.viviendastecnohouse.com.ar</a></footer>';

        $texto_pdf .= '</body></html>';

        $pdf->loadHTML($texto_pdf);

        return $pdf->stream();      
        
    }

    public function imprimirAdquiriente($solicitud_id) {

        $Solicitud = Solicitud::find($solicitud_id);
        $Reporte = Reporte::find(8);

        $pdf = \App::make('dompdf.wrapper');


        $encabezado = '<html>';
        $encabezado .= '<head>';
        $encabezado .= '  <style>';
        $encabezado .= '    @page { margin-top: 80px; font-family: Arial, Helvetica, sans-serif; font-size: 13px}';
        $encabezado .= '    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 100px; margin-top: 70px }';
        $encabezado .= '    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }';
        $encabezado .= '    p { text-align: justify; }';
        //$encabezado .= '    p { page-break-after: always; }';
        //$encabezado .= '    p:last-child { page-break-after: never; }';
        $encabezado .= '  </style>';
        $encabezado .= '</head>';
        $encabezado .= '<body>';
        $encabezado .= '<header><div style="float: left"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></div><div style="float: right; margin-right: 100px; margin-top: 80px; font-size: 15px; font-weight: bold">CONTRATO: '.$Solicitud->cliente_id.'</div></header>';
        //$encabezado .= '<header><p style="text-align: right"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></p></header><footer>info pie</footer>';


        $texto_pdf = $encabezado;

        $texto_pdf .= $Reporte->rtf_cuerpo;

        $texto_pdf .= '<footer style="text-align: center; font-size: 10px; font-weight: bold"><a href="http://www.viviendastecnohouse.com.ar" target="_blank">www.viviendastecnohouse.com.ar</a></footer>';

        $texto_pdf .= '</body></html>';

        $pdf->loadHTML($texto_pdf);

        return $pdf->stream();      
        
    }



    public function imprimirCaracteristicasTecnicas($solicitud_id) {

        $Solicitud = Solicitud::find($solicitud_id);
        $Reporte = Reporte::find(9);

        $pdf = \App::make('dompdf.wrapper');


        $encabezado = '<html>';
        $encabezado .= '<head>';
        $encabezado .= '  <style>';
        $encabezado .= '    @page { margin-top: 80px; font-family: Arial, Helvetica, sans-serif; font-size: 13px}';
        $encabezado .= '    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 100px; margin-top: 70px }';
        $encabezado .= '    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }';
        $encabezado .= '    p { text-align: justify; }';
        //$encabezado .= '    p { page-break-after: always; }';
        //$encabezado .= '    p:last-child { page-break-after: never; }';
        $encabezado .= '  </style>';
        $encabezado .= '</head>';
        $encabezado .= '<body>';
        $encabezado .= '<header><div style="float: left"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></div><div style="float: right; margin-right: 100px; margin-top: 80px; font-size: 15px; font-weight: bold">CONTRATO: '.$Solicitud->cliente_id.'</div></header>';
        //$encabezado .= '<header><p style="text-align: right"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></p></header><footer>info pie</footer>';


        $texto_pdf = $encabezado;

        $texto_pdf .= $Reporte->rtf_cuerpo;

        $texto_pdf .= '<footer style="text-align: center; font-size: 10px; font-weight: bold"><a href="http://www.viviendastecnohouse.com.ar" target="_blank">www.viviendastecnohouse.com.ar</a></footer>';

        $texto_pdf .= '</body></html>';

        $pdf->loadHTML($texto_pdf);

        return $pdf->stream();      
        
    }

    public function imprimirNormas($solicitud_id) {

        $Solicitud = Solicitud::find($solicitud_id);
        $Reporte = Reporte::find(10);

        $pdf = \App::make('dompdf.wrapper');


        $encabezado = '<html>';
        $encabezado .= '<head>';
        $encabezado .= '  <style>';
        $encabezado .= '    @page { margin-top: 80px; font-family: Arial, Helvetica, sans-serif; font-size: 13px}';
        $encabezado .= '    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 100px; margin-top: 70px }';
        $encabezado .= '    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }';
        $encabezado .= '    p { text-align: justify; }';
        //$encabezado .= '    p { page-break-after: always; }';
        //$encabezado .= '    p:last-child { page-break-after: never; }';
        $encabezado .= '  </style>';
        $encabezado .= '</head>';
        $encabezado .= '<body>';
        $encabezado .= '<header><div style="float: left"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></div><div style="float: right; margin-right: 100px; margin-top: 80px; font-size: 15px; font-weight: bold">CONTRATO: '.$Solicitud->cliente_id.'</div></header>';
        //$encabezado .= '<header><p style="text-align: right"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></p></header><footer>info pie</footer>';


        $texto_pdf = $encabezado;

        $texto_pdf .= $Reporte->rtf_cuerpo;

        $texto_pdf .= '<footer style="text-align: center; font-size: 10px; font-weight: bold"><a href="http://www.viviendastecnohouse.com.ar" target="_blank">www.viviendastecnohouse.com.ar</a></footer>';

        $texto_pdf .= '</body></html>';

        $pdf->loadHTML($texto_pdf);

        return $pdf->stream();      
        
    }


    public function imprimirBasePlateas($solicitud_id) {

        $Solicitud = Solicitud::find($solicitud_id);
        $Reporte = Reporte::find(11);

        $pdf = \App::make('dompdf.wrapper');


        $encabezado = '<html>';
        $encabezado .= '<head>';
        $encabezado .= '  <style>';
        $encabezado .= '    @page { margin-top: 80px; font-family: Arial, Helvetica, sans-serif; font-size: 13px}';
        $encabezado .= '    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 100px; margin-top: 70px }';
        $encabezado .= '    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }';
        $encabezado .= '    p { text-align: justify; }';
        //$encabezado .= '    p { page-break-after: always; }';
        //$encabezado .= '    p:last-child { page-break-after: never; }';
        $encabezado .= '  </style>';
        $encabezado .= '</head>';
        $encabezado .= '<body>';
        $encabezado .= '<header><div style="float: left"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></div><div style="float: right; margin-right: 100px; margin-top: 80px; font-size: 15px; font-weight: bold">CONTRATO: '.$Solicitud->cliente_id.'</div></header>';
        //$encabezado .= '<header><p style="text-align: right"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></p></header><footer>info pie</footer>';


        $texto_pdf = $encabezado;

        $texto_pdf .= '<div style="text-align: center; font-size: 20px; font-weight: bold">BASE DE PLATEA<br>ANEXO IV</div>';
        $texto_pdf .= '<center><img src="'.env('PATH_PUBLIC').'img/anexo_base_platea.gif" style="width: 435px;"></center><br>';

        $texto_pdf .= $Reporte->rtf_cuerpo;

        $texto_pdf .= '<footer style="text-align: center; font-size: 10px; font-weight: bold"><a href="http://www.viviendastecnohouse.com.ar" target="_blank">www.viviendastecnohouse.com.ar</a></footer>';

        $texto_pdf .= '</body></html>';

        $pdf->loadHTML($texto_pdf);

        return $pdf->stream();      
        
    }



    public function imprimirOrdenDeFabricacion($solicitud_id) {

        $Solicitud = Solicitud::find($solicitud_id);
        $Reporte = Reporte::find(12);

        $pdf = \App::make('dompdf.wrapper');


        $encabezado = '<html>';
        $encabezado .= '<head>';
        $encabezado .= '  <style>';
        $encabezado .= '    @page { margin-top: 80px; font-family: Arial, Helvetica, sans-serif; font-size: 13px}';
        $encabezado .= '    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 100px; margin-top: 70px }';
        $encabezado .= '    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }';
        $encabezado .= '    p { text-align: justify; }';
        //$encabezado .= '    p { page-break-after: always; }';
        //$encabezado .= '    p:last-child { page-break-after: never; }';
        $encabezado .= '  </style>';
        $encabezado .= '</head>';
        $encabezado .= '<body>';
        $encabezado .= '<header><div style="float: left"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></div><div style="float: right; margin-right: 100px; margin-top: 80px; font-size: 15px; font-weight: bold">CONTRATO: '.$Solicitud->cliente_id.'</div></header>';
        //$encabezado .= '<header><p style="text-align: right"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></p></header><footer>info pie</footer>';


        $texto_pdf = $encabezado;

        $texto_pdf .= $Reporte->rtf_cuerpo;
        $texto_pdf .= '<footer style="text-align: center; font-size: 10px; font-weight: bold"><a href="http://www.viviendastecnohouse.com.ar" target="_blank">www.viviendastecnohouse.com.ar</a></footer>';

        $texto_pdf .= '</body></html>';

        $pdf->loadHTML($texto_pdf);

        return $pdf->stream();      
        
    }


    public function imprimirRecibo($solicitud_id) {

        $Solicitud = Solicitud::find($solicitud_id);
        $Reporte = Reporte::find(13);

        $pdf = \App::make('dompdf.wrapper');

        $patrones = array();
        $patrones[0] = '/solicitud_id/';
        $sustituciones = array();
        $sustituciones[0] = $Solicitud->cliente_id;
        $sustituciones[1] = '<strong>'.$Solicitud->id.'</strong>';

        $encabezado = '<html>';
        $encabezado .= '<head>';
        $encabezado .= '  <style>';
        $encabezado .= '    @page { margin-top: 80px; font-family: Arial, Helvetica, sans-serif; font-size: 13px}';
        $encabezado .= '    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 100px; margin-top: 70px }';
        $encabezado .= '    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }';
        $encabezado .= '    p { text-align: justify; }';
        //$encabezado .= '    p { page-break-after: always; }';
        //$encabezado .= '    p:last-child { page-break-after: never; }';
        $encabezado .= '  </style>';
        $encabezado .= '</head>';
        $encabezado .= '<body>';

        $texto_pdf = $encabezado;
        
        $texto_pdf .= preg_replace($patrones, $sustituciones, $Reporte->rtf_cuerpo);
        $texto_pdf .= '<br><br>';
        $texto_pdf .= preg_replace($patrones, $sustituciones, $Reporte->rtf_cuerpo);


        $texto_pdf .= '</body></html>';

        $pdf->loadHTML($texto_pdf);

        return $pdf->stream();      
        
    }


    public function imprimirRequisitos($solicitud_id) {

        $Solicitud = Solicitud::find($solicitud_id);
        $Reporte = Reporte::find(14);

        $pdf = \App::make('dompdf.wrapper');


        $encabezado = '<html>';
        $encabezado .= '<head>';
        $encabezado .= '  <style>';
        $encabezado .= '    @page { margin-top: 80px; font-family: Arial, Helvetica, sans-serif; font-size: 13px}';
        $encabezado .= '    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 100px; margin-top: 70px }';
        $encabezado .= '    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }';
        $encabezado .= '    p { text-align: justify; }';
        //$encabezado .= '    p { page-break-after: always; }';
        //$encabezado .= '    p:last-child { page-break-after: never; }';
        $encabezado .= '  </style>';
        $encabezado .= '</head>';
        $encabezado .= '<body>';
        $encabezado .= '<header><div style="float: left"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></div><div style="float: right; margin-right: 100px; margin-top: 80px; font-size: 15px; font-weight: bold">CONTRATO: '.$Solicitud->cliente_id.'</div></header>';
        //$encabezado .= '<header><p style="text-align: right"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></p></header><footer>info pie</footer>';


        $texto_pdf = $encabezado;

        $texto_pdf .= $Reporte->rtf_cuerpo;
        $texto_pdf .= '<footer style="text-align: center; font-size: 10px; font-weight: bold"><a href="http://www.viviendastecnohouse.com.ar" target="_blank">www.viviendastecnohouse.com.ar</a></footer>';

        $texto_pdf .= '</body></html>';

        $pdf->loadHTML($texto_pdf);

        return $pdf->stream();      
        
    }



    public function imprimirAnexo2($solicitud_id) {

        $Solicitud = Solicitud::find($solicitud_id);
        $Reporte = Reporte::find(15);

        $pdf = \App::make('dompdf.wrapper');


        $encabezado = '<html>';
        $encabezado .= '<head>';
        $encabezado .= '  <style>';
        $encabezado .= '    @page { margin-top: 80px; font-family: Arial, Helvetica, sans-serif; font-size: 13px}';
        $encabezado .= '    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 100px; margin-top: 70px }';
        $encabezado .= '    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }';
        $encabezado .= '    p { text-align: justify; }';
        $encabezado .= '    li { line-height: 1.5; }';
        //$encabezado .= '    p { page-break-after: always; }';
        //$encabezado .= '    p:last-child { page-break-after: never; }';
        $encabezado .= '  </style>';
        $encabezado .= '</head>';
        $encabezado .= '<body>';
        $encabezado .= '<header><div style="float: left"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></div><div style="float: right; margin-right: 100px; margin-top: 80px; font-size: 15px; font-weight: bold">CONTRATO: '.$Solicitud->cliente_id.'</div></header>';
        //$encabezado .= '<header><p style="text-align: right"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></p></header><footer>info pie</footer>';


        $texto_pdf = $encabezado;

        $texto_pdf .= $Reporte->rtf_cuerpo;
        $texto_pdf .= '<footer style="text-align: center; font-size: 10px; font-weight: bold"><a href="http://www.viviendastecnohouse.com.ar" target="_blank">www.viviendastecnohouse.com.ar</a></footer>';

        $texto_pdf .= '</body></html>';

        $pdf->loadHTML($texto_pdf);

        return $pdf->stream();      
        
    }


    public function imprimirAutorizacionFoto($solicitud_id) {

        $Solicitud = Solicitud::find($solicitud_id);
        $Reporte = Reporte::find(16);

        $pdf = \App::make('dompdf.wrapper');


        $encabezado = '<html>';
        $encabezado .= '<head>';
        $encabezado .= '  <style>';
        $encabezado .= '    @page { margin-top: 80px; font-family: Arial, Helvetica, sans-serif; font-size: 13px}';
        $encabezado .= '    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 100px; margin-top: 70px }';
        $encabezado .= '    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }';
        $encabezado .= '    p { text-align: justify; }';
        $encabezado .= '    li { line-height: 1.5; }';
        //$encabezado .= '    p { page-break-after: always; }';
        //$encabezado .= '    p:last-child { page-break-after: never; }';
        $encabezado .= '  </style>';
        $encabezado .= '</head>';
        $encabezado .= '<body>';
        $encabezado .= '<header><div style="float: left"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></div><div style="float: right; margin-right: 100px; margin-top: 80px; font-size: 15px; font-weight: bold">CONTRATO: '.$Solicitud->cliente_id.'</div></header>';
        //$encabezado .= '<header><p style="text-align: right"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></p></header><footer>info pie</footer>';


        $texto_pdf = $encabezado;

        $texto_pdf .= $Reporte->rtf_cuerpo;
        $texto_pdf .= '<footer style="text-align: center; font-size: 10px; font-weight: bold"><a href="http://www.viviendastecnohouse.com.ar" target="_blank">www.viviendastecnohouse.com.ar</a></footer>';

        $texto_pdf .= '</body></html>';

        $pdf->loadHTML($texto_pdf);

        return $pdf->stream();      
        
    }


    public function imprimirPublicidad($solicitud_id) {

        $Solicitud = Solicitud::find($solicitud_id);
        $Reporte = Reporte::find(17);

        $pdf = \App::make('dompdf.wrapper');


        $encabezado = '<html>';
        $encabezado .= '<head>';
        $encabezado .= '  <style>';
        $encabezado .= '    @page { margin-top: 80px; font-family: Arial, Helvetica, sans-serif; font-size: 13px}';
        $encabezado .= '    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 100px; margin-top: 70px }';
        $encabezado .= '    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }';
        $encabezado .= '    p { text-align: justify; }';
        $encabezado .= '    li { line-height: 1.5; }';
        //$encabezado .= '    p { page-break-after: always; }';
        //$encabezado .= '    p:last-child { page-break-after: never; }';
        $encabezado .= '  </style>';
        $encabezado .= '</head>';
        $encabezado .= '<body>';
        $encabezado .= '<header><div style="float: left"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></div><div style="float: right; margin-right: 100px; margin-top: 80px; font-size: 15px; font-weight: bold">CONTRATO: '.$Solicitud->cliente_id.'</div></header>';
        //$encabezado .= '<header><p style="text-align: right"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></p></header><footer>info pie</footer>';


        $texto_pdf = $encabezado;

        $texto_pdf .= $Reporte->rtf_cuerpo;
        $texto_pdf .= '<footer style="text-align: center; font-size: 10px; font-weight: bold"><a href="http://www.viviendastecnohouse.com.ar" target="_blank">www.viviendastecnohouse.com.ar</a></footer>';

        $texto_pdf .= '</body></html>';

        $pdf->loadHTML($texto_pdf);

        return $pdf->stream();      
        
    }


    public function imprimirGarantes($solicitud_id) {

        //PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        //$pdf = PDF::loadView('pdfview');

        //$pdf = PDF::loadView('pruebaparapdf');
        //return $pdf->download();
        $gCon = new GenericController();
        $fcx = new FxC();
        $Solicitud = Solicitud::find($solicitud_id);

        $pdf = \App::make('dompdf.wrapper');


        $encabezado = '<html>';
        $encabezado .= '<head>';
        $encabezado .= '  <style>';
        $encabezado .= '    @page { margin-top: 80px; font-family: Arial, Helvetica, sans-serif; font-size: 13px}';
        $encabezado .= '    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 100px; margin-top: 70px }';
        $encabezado .= '    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }';
        $encabezado .= '    p { text-align: justify; }';
        //$encabezado .= '    p { page-break-after: always; }';
        //$encabezado .= '    p:last-child { page-break-after: never; }';
        $encabezado .= '  </style>';
        $encabezado .= '</head>';
        $encabezado .= '<body>';
        $encabezado .= '<header><div style="float: left"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></div><div style="float: right; margin-right: 100px; margin-top: 80px; font-size: 15px; font-weight: bold">CONTRATO: '.$Solicitud->cliente_id.'</div></header>';
        //$encabezado .= '<header><p style="text-align: right"><img src="'.env('PATH_PUBLIC').'img/logo.jpg" style="width: 200px"></p></header><footer>info pie</footer>';


        $texto_pdf = $encabezado;

        $patrones = array();
        $patrones[0] = '/cliente_id/';
        $patrones[1] = '/cliente_nombre/';
        $patrones[2] = '/cliente_apellido/';
        $patrones[3] = '/cliente_tipo_de_documento/';
        $patrones[4] = '/cliente_nro_de_documento/';
        $patrones[5] = '/cliente_domicilio/';
        $patrones[6] = '/cliente_localidad/';
        $patrones[7] = '/cliente_provincia/';
        $sustituciones = array();
        $sustituciones[0] = $Solicitud->cliente_id;
        $sustituciones[1] = '<strong>'.$Solicitud->Cliente->nombre.'</strong>';
        $sustituciones[2] = '<strong>'.$Solicitud->Cliente->apellido.'</strong>';
        $sustituciones[3] = '<strong>'.$Solicitud->Cliente->Tipo_de_documento->tipo_de_documento.'</strong>';
        $sustituciones[4] = '<strong>'.$Solicitud->Cliente->nro_de_documento.'</strong>';
        $sustituciones[5] = '<strong>'.$Solicitud->Cliente->domicilio.'</strong>';
        $sustituciones[6] = '<strong>'.$Solicitud->Cliente->localidad->localidad.'</strong>';
        $sustituciones[7] = '<strong>'.$Solicitud->Cliente->localidad->provincia->provincia.'</strong>';

        $Reporte = Reporte::find(18);
        $texto_pdf .= preg_replace($patrones, $sustituciones, $Reporte->rtf_cuerpo);



        $Garantes = Garante::where('solicitud_id', $solicitud_id)->get();

        $array_orden_pagador = ['', 'SEGUNDO', 'TERCERO', 'CUARTO', 'QUINTO', 'SEXTO', 'SEPTIMO', 'OCTAVO', 'NOVENO', 'DECIMO'];
        $i = 0;
        foreach ($Garantes as $Garante) {
            $patrones = array();
            $patrones[0] = '/orden_pagador/';
            $patrones[1] = '/garante_apellido/';
            $patrones[2] = '/garante_nombre/';
            $patrones[3] = '/garante_tipo_de_documento/';
            $patrones[4] = '/garante_domicilio/';
            $patrones[5] = '/garante_inmueble/';
            $patrones[6] = '/garante_tomo/';
            $patrones[7] = '/garante_folio/';
            $patrones[8] = '/garante_numero/';
            $patrones[9] = '/garante_departamento/';
            $sustituciones = array();
            $sustituciones[0] = $array_orden_pagador[$i];
            $sustituciones[1] = '<strong>'.$Garante->apellido.'</strong>';
            $sustituciones[2] = '<strong>'.$Garante->nombre.'</strong>';
            $sustituciones[3] = '<strong>'.$Garante->Tipo_de_documento->tipo_de_documento.'</strong>';
            $sustituciones[4] = '<strong>'.$Garante->nro_de_documento.'</strong>';
            $sustituciones[5] = '<strong>'.$Garante->domicilio.'</strong>';
            $sustituciones[6] = '<strong>'.$Garante->inmueble.'</strong>';
            $sustituciones[7] = '<strong>'.$Garante->tomo.'</strong>';
            $sustituciones[8] = '<strong>'.$Garante->folio.'</strong>';
            $sustituciones[9] = '<strong>'.$Garante->departamento.'</strong>';

            $Reporte = Reporte::find(19);
            $texto_pdf .= preg_replace($patrones, $sustituciones, $Reporte->rtf_cuerpo);
            $i++;

        }


        //$texto_pdf .= '<div style="page-break-after: always;"></div>';

        //$Reporte = Reporte::find(2);

        //$texto_pdf .= preg_replace($patrones, $sustituciones, $Reporte->rtf_cuerpo);

        $texto_pdf .= '<footer style="text-align: center; font-size: 10px; font-weight: bold"><a href="http://www.viviendastecnohouse.com.ar" target="_blank">www.viviendastecnohouse.com.ar</a></footer>';

        $texto_pdf .= '</body></html>';

        $pdf->loadHTML($texto_pdf);

        return $pdf->stream();      
        
    }


}

