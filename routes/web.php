<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => 'auth'], function () {
	Route::get('/', 'HomeController@index');


	// RUTAS GENERICAS
	Route::get('/list/{gen_modelo}/{gen_opcion}', 'GenericController@index');
	Route::post('/crearlista', 'GenericController@crearLista');
	Route::post('/crearabm', 'GenericController@crearABM');
	//Route::get('/abm/{accion}/{gen_modelo}/{id}', 'GenericController@show');
	//Route::get('/crearlista/{gen_modelo}', 'GenericController@crearLista');
	Route::post('/enviarabm/{gen_modelo}', 'GenericController@crearABM');
	Route::post('/store', 'GenericController@store');
	// FIN RUTAS GENERICAS

	Route::get('/composicion/{modelo_id}', 'ModeloController@composicionDeModelo');
	Route::post('/crearlistamodelo', 'ModeloController@crearListaModelo');
	Route::get('/precios/{lista_de_precio_id}', 'PreciosController@preciosDeLista');
	Route::post('/crear-lista-de-precios', 'PreciosController@crearlistaDePrecios');


	//SOLICITUDES	
	Route::get('/Solicitudes/list/{estado}', 'SolicitudController@index');
	//Route::get('/Solicitudes/list/{estado}', 'SolicitudController@index');	
	Route::get('/Solicitudes/solicitud/ver/{solicitud_id}', 'SolicitudController@editarSolicitud');
	Route::get('/Solicitudes/solicitud/cambiar-cliente/{solicitud_id}/{cliente_id}', 'SolicitudController@cambiarClienteSolicitud');
	Route::get('/Solicitudes/solicitud/cambiar-modelo/{solicitud_id}/{modelo_id}', 'SolicitudController@cambiarModeloSolicitud');
	Route::post('/Solicitudes/solicitud/modificar-forma-de-pago/{solicitud_id}', 'SolicitudController@cambiarFormaDePago');
	Route::post('/Solicitudes/solicitud/aprobacion-administracion/{solicitud_id}', 'SolicitudController@aprobacionAdministracion');
	Route::post('/Solicitudes/solicitud/aprobacion-garantes/{solicitud_id}', 'SolicitudController@aprobacionGarantes');
	Route::post('/Solicitudes/solicitud/aprobacion-finalizada/{solicitud_id}', 'SolicitudController@aprobacionFinalizada');
	Route::post('/Solicitudes/solicitud/aprobacion-solicitar-revision/{solicitud_id}', 'SolicitudController@aprobacionSolicitarRevision');
	Route::post('/Solicitudes/solicitud/guardar-obs-adm/{solicitud_id}', 'SolicitudController@guardarObsAdm');
	Route::post('/Solicitudes/solicitud/guardar-obs-gar/{solicitud_id}', 'SolicitudController@guardarObsGar');
	Route::post('/Solicitudes/solicitud/guardar-obs-sol-rev/{solicitud_id}', 'SolicitudController@guardarObsSolRev');
	Route::post('/Solicitudes/solicitud/guardar-obs-fin/{solicitud_id}', 'SolicitudController@guardarObsFin');
	Route::get('/Solicitudes/solicitud/imprimir-solicitud/{solicitud_id}', 'SolicitudController@imprimirSolicitud');
	Route::post('/Solicitudes/solicitud/guardar-distribucion-de-cuotas', 'SolicitudController@GuardarDistribucionDeCuotas');
	Route::post('/Solicitudes/solicitud/guardar-fecha-de-contrato', 'SolicitudController@guardarFechaDeContrato');
	Route::get('/Solicitudes/solicitud/imprimir-contrato/{solicitud_id}', 'SolicitudController@imprimirContrato');
	Route::get('/Solicitudes/solicitud/imprimir-adquiriente/{solicitud_id}', 'SolicitudController@imprimirAdquiriente');
	Route::get('/Solicitudes/solicitud/imprimir-caracteristicas-tecnicas/{solicitud_id}', 'SolicitudController@imprimirCaracteristicasTecnicas');
	Route::get('/Solicitudes/solicitud/imprimir-normas/{solicitud_id}', 'SolicitudController@imprimirNormas');
	Route::get('/Solicitudes/solicitud/imprimir-base-plateas/{solicitud_id}', 'SolicitudController@imprimirBasePlateas');
	Route::get('/Solicitudes/solicitud/imprimir-orden-de-fabricacion/{solicitud_id}', 'SolicitudController@imprimirOrdenDeFabricacion');
	Route::get('/Solicitudes/solicitud/imprimir-recibo/{solicitud_id}', 'SolicitudController@imprimirRecibo');
	Route::get('/Solicitudes/solicitud/imprimir-requisitos/{solicitud_id}', 'SolicitudController@imprimirRequisitos');
	Route::get('/Solicitudes/solicitud/imprimir-anexo2/{solicitud_id}', 'SolicitudController@imprimirAnexo2');
	Route::get('/Solicitudes/solicitud/imprimir-anexo5/{solicitud_id}', 'SolicitudController@imprimirAnexo5');
	Route::get('/Solicitudes/solicitud/imprimir-autorizacion-foto/{solicitud_id}', 'SolicitudController@imprimirAutorizacionFoto');
	Route::get('/Solicitudes/solicitud/imprimir-publicidad/{solicitud_id}', 'SolicitudController@imprimirPublicidad');
	Route::get('/Solicitudes/solicitud/imprimir-garantes/{solicitud_id}', 'SolicitudController@imprimirGarantes');
	Route::get('/clientes-por-sucursal', 'ClienteController@CientesPorSucursal');
	Route::post('/Solicitudes/solicitud/aprobacion-contrato/{solicitud_id}', 'SolicitudController@aprobacionContrato');



	//ASISTENTE CREACION DE SOLICITUD
	Route::get('/Solicitudes/crear', function () {
	    return view('solicitudes/solicitud-asistente');
	});	
	Route::post('/Solicitudes/crear/listar-clientes-para-seleccion/{solicitud_id}', 'SolicitudController@listarClientesParaSeleccion');
	Route::get('/Solicitudes/crear/elegir-cliente/{cliente_id}', 'SolicitudController@crearSolicitudElegirCliente');
	Route::post('/Solicitudes/crear/listar-modelos-para-seleccion/{solicitud_id}', 'SolicitudController@listarModelosParaSeleccion');
	Route::get('/Solicitudes/crear/elegir-modelo/{solicitud_id}/{modelo_id}', 'SolicitudController@crearSolicitudElegirModelo');
	Route::post('/Solicitudes/crear/determinar-composicion-de-modelo-para-seleccion/{solicitud_id}', 'SolicitudController@listarComposicionModeloParaSeleccion');
	Route::get('/Solicitudes/crear/elegir-forma-de-pago/{solicitud_id}', 'SolicitudController@crearSolicitudElegirFormaDePago');
	Route::post('/Solicitudes/crear/resumen-para-envio', 'SolicitudController@GuardarFormaDePagoyResumenParaEnvio');
	Route::get('/Solicitudes/crear/elegir-forma-de-pago/{solicitud_id}', 'SolicitudController@crearSolicitudElegirFormaDePago');
	Route::post('/traerValoresPrecio', 'SolicitudController@traerValoresPrecio');
	Route::get('/Solicitudes/crear/enviar-solicitud/{solicitud_id}', 'SolicitudController@enviarSolicitud');

	Route::get('importar/{tb}', 'ImpExpController@importar');
	Route::get('export/{tb}/{type}', 'ImpExpController@export');
	Route::get('import/{tb}', 'ImpExpController@import');
	Route::post('import/{tb}', 'ImpExpController@import');
	

});

Route::get('/prueba', function () {
    return view('prueba');
});	


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('/delcache', function () {
    $exitCode = Artisan::call('cache:clear');
    echo 'Cache eliminada!';
});

