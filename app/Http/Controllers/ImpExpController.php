<?php

namespace App\Http\Controllers;
use App\Solicitud;
use App\User;
use Auth;
use Excel;
use App\Cliente;
use App\Localidad;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ImpExpController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function importar($tb)
    {
        return view('imp-exp/imp-exp')->with('tb', $tb);
    }
    public function export($tb, $type)
    {
        if ($tb == 'clientes') {
            $data = Cliente::get()->toArray();
        }

        return Excel::create('Tabla '.$tb, function($excel) use ($tb, $data) {
            $excel->sheet($tb, function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download($type);
    }

    public function import(Request $request)
    {

        $tb = $request->tb;
        if(Input::hasFile('import_file')){
            $path = Input::file('import_file')->getRealPath();
            $data = Excel::load($path, function($reader) {
            })->get();
            if(!empty($data) && $data->count()){
                foreach ($data as $key => $value) {
                    if ($tb == 'clientes') {
                        
                        $cliente = Cliente::where('id_de_importacion', $value->id)->get();
                        
                        if (count($cliente) == 0) {
                            $importar = true;

                            if ($value->nro_de_documento == '') {
                                $importar = false;
                                $detalle_error = 'Nro de Documento Nulo';
                            }

                            if ($value->tipo_de_documento_id == '') {
                                $importar = false;
                                $detalle_error = 'Tipo de Documento Nulo';
                            }
                            else {
                                $Doc_duplicados = Cliente::where('tipo_de_documento_id', $value->tipo_de_documento_id)->where('nro_de_documento', $value->nro_de_documento)->count();
                                if ($Doc_duplicados > 0) {
                                    $importar = false;
                                    $detalle_error = 'Documento Duplicado';
                                }

                            }
                            if ($value->localidad_id == '') {
                                $importar = false;
                                $detalle_error = 'Localidad Nula';
                            }
                            else {
                                $Localidades = Localidad::where('id', $value->localidad_id)->count();
                                if ($Localidades == 0) {
                                    $importar = false;
                                    $detalle_error = 'ID de Localidad no encontrado';
                                }
                            }

                            

                            if ($importar) {


                                if ($value->email_correo == '') {
                                    $value->email_correo = 'sin valor';
                                }
                                if ($value->telefono_fijo == '') {
                                    $value->telefono_fijo = 'sin valor';
                                }
                                if ($value->telefono_celular == '') {
                                    $value->telefono_celular = 'sin valor';
                                }
                                if ($value->domicilio == '') {
                                    $value->domicilio = 'sin valor';
                                }

                                $Cliente = new Cliente;
                                $Cliente->nombre = $value->nombre;
                                $Cliente->apellido = $value->apellido;
                                $Cliente->tipo_de_documento_id = $value->tipo_de_documento_id;
                                $Cliente->nro_de_documento = $value->nro_de_documento;
                                $Cliente->domicilio = $value->domicilio;
                                $Cliente->localidad_id = $value->localidad_id;
                                $Cliente->situacion_de_iva_id = $value->situacion_de_iva_id;
                                $Cliente->telefono_fijo = $value->telefono_fijo;
                                $Cliente->telefono_celular = $value->telefono_celular;
                                $Cliente->observaciones = $value->observaciones;
                                $Cliente->email_correo = $value->email_correo;
                                $Cliente->id_de_importacion = $value->id;

                                try { 
                                    $Cliente->save();
                                    $Resultados[] = ['valor' => $value->nombre, 'importacion' => true, 'detalle' => '', 'mostrar' => true];
                                } catch(\Illuminate\Database\QueryException $ex){ 
                                    $Resultados[] = ['valor' => $value->nombre, 'importacion' => true, 'detalle' => $ex->getMessage(), 'mostrar' => true];
                                } 
                            }
                            else {
                                $Resultados[] = ['valor' => $value->nombre, 'importacion' => false, 'detalle' => $detalle_error, 'mostrar' => true];
                            }

                        }
                    }
                }
            }

            $mensaje['error'] = false;
            $mensaje['detalle'] = 'Archivo Procesado';

        }
        
        return view('imp-exp/imp-exp')
        ->with('tb', $tb)
        ->with('mensaje', $mensaje)
        ->with('Resultados', $Resultados);
    }



}
