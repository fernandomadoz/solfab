<?php

namespace App\Http\Controllers;
use Auth;
use App\Cliente;

use Illuminate\Support\Facades\DB;



class AppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }


    public function checkToken($token) {

        
        $auth = false;

        if ('CLASEXTECNO' == $token) {
            $auth = true;
        }

        return $auth;
    }

    public function sql($referencia_de_origen, $abm, $token) {

        $auth = $this->checkToken($token);
        $array_usuario = null;

        if ($auth) {


            try {
            
                $sql = $_POST['sql'];

                if ($abm == 'A' or $abm == 'B' or $abm == 'M') {
                    //$resultado_sql = DB::select($sql);
                    if($abm == 'A') {
                        $filas_afectadas = DB::insert($sql);    
                    }
                    if($abm == 'B') {
                        $filas_afectadas = DB::delete($sql);    
                    }
                    if($abm == 'M') {
                        $filas_afectadas = DB::update($sql);    
                    }
                    //$resultado_sql = DB::statement($sql);

                    $mensaje = 'OK, Filas afectadas: '.$filas_afectadas;
                    $error = false;
                }
                else {
                    $mensaje = 'accion abm no identificada';
                    $error = true;
                }

            } catch (\Exception $e) {            
                $mensaje = 'Excepcion capturada: '.$e->getMessage();
                $error = true;
            }

        }
        else {            
            $mensaje = 'Error de Token';
            $error = true;
        }


        $array_resultado = [
            "referencia_de_origen" => $referencia_de_origen,
            "mensaje" => $mensaje,
            "error" => $error
        ];
        $resultado = json_encode($array_resultado);
        return response($resultado,200);
    }

    public function batchCliente($referencia_de_origen, $abm, $token) {



        $auth = $this->checkToken($token);
        $array_usuario = null;

        if ($auth) {


            try {
            

                if ($abm == 'A' or $abm == 'B' or $abm == 'M') {
                    //$resultado_sql = DB::select($sql);
                    if($abm == 'B') {
                        $Registros = Cliente::where('id_externo', $_POST['id_externo'])->get();
                        $Registro = $Registros[0];
                        $Registro->delete(); 
                    }
                    else {
                        if($abm == 'A') {
                            $Registro = new Cliente;                        
                        }
                        if($abm == 'M') {
                            $Registros = Cliente::where('id_externo', $_POST['id_externo'])->get();
                            $Registro = $Registros[0];
                        }
                        //$resultado_sql = DB::statement($sql);


                        $Registro->id_externo = $_POST['id_externo'];
                        $Registro->nombre = $_POST['nombre'];
                        $Registro->apellido = $_POST['apellido'];
                        $Registro->tipo_de_documento_id = $_POST['tipo_de_documento_id'];
                        $Registro->nro_de_documento = $_POST['nro_de_documento'];
                        $Registro->domicilio = $_POST['domicilio'];
                        $Registro->localidad_id = $_POST['localidad_id'];
                        $Registro->situacion_de_iva_id = $_POST['situacion_de_iva_id'];
                        $Registro->telefono_fijo = $_POST['telefono_fijo'];
                        $Registro->telefono_celular = $_POST['telefono_celular'];
                        $Registro->email_correo = $_POST['email_correo'];
                        $Registro->observaciones = $_POST['observaciones'];
                        $Registro->created_at = $_POST['created_at'];
                        $Registro->updated_at = $_POST['updated_at'];
                        $Registro->user_id = $_POST['user_id'];
                        $Registro->zona_local_id = $_POST['zona_local_id'];
                        $Registro->sucursal_id = $_POST['sucursal_id'];
                        $Registro->id_de_importacion = $_POST['id_de_importacion'];
                        $Registro->fecha_de_baja = $_POST['fecha_de_baja'];
                        $tado = $Registro->save(); 
                    }

                    $mensaje = 'OK';
                    $error = false;
                }
                else {
                    $mensaje = 'accion abm no identificada';
                    $error = true;
                }

            } catch (\Exception $e) {            
                $mensaje = 'Excepcion capturada: '.$e->getMessage();
                $error = true;
            }

        }
        else {            
            $mensaje = 'Error de Token';
            $error = true;
        }


        $array_resultado = [
            "referencia_de_origen" => $referencia_de_origen,
            "mensaje" => $mensaje,
            "error" => $error
        ];
        $resultado = json_encode($array_resultado);
        return response($resultado,200);
    }




    public function webhook(Request $request) {

        $challenge = $_REQUEST['hub_challenge'];
        $verify_token = $_REQUEST['hub_verify_token'];

        if ($verify_token === 'abc123') {
          echo $challenge;
        }

        return response($resultado,200);
        
    }



}

