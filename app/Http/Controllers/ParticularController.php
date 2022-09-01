<?php

namespace App\Http\Controllers;

//accionesPosteriores
use App\Modelo;
use App\Solicitud;
use App\Composicion_de_modelo;
use App\Cliente;
use App\User;
use Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ParticularController extends Controller
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


    public function accionesAnteriores($modelo, $accion, $id) {

        $acc_ant_errorInfo = array();
        $acc_ant_mensaje = array();
        $acc_ant_mensaje['error'] = false;

        $id = intval($id);

        // INICIO Composicion_de_modelo
        if($modelo == 'Composicion_de_modelo') {
            if ($accion == 'b') {
                $Composicion_de_modelo = Composicion_de_modelo::find($id);
                $modelo_id = $Composicion_de_modelo->modelo_id;
                $ComponentesDeModelo = Composicion_de_modelo::
                    where('modelo_id', $modelo_id)
                    ->get();
                $total_de_metros_cuadrados = 0;
                foreach ($ComponentesDeModelo as $Componente) {
                    if ($Componente->id <> $id) {
                        $total_de_metros_cuadrados = $total_de_metros_cuadrados +($Componente->ancho*$Componente->largo);
                    }
                }

                $Modelo = Modelo::find($modelo_id);
                $Modelo->total_de_metros_cuadrados = $total_de_metros_cuadrados;
                $Modelo->save();
            }
        }
        // FIN Composicion_de_modelo

        // INICIO composicion_de_modelo_de_solicitudes
        if($modelo == 'composicion_de_modelo_de_solicitudes') {
            if ($accion == 'b') {
                $composicion_de_modelo_de_solicitudes = composicion_de_modelo_de_solicitudes::find($id);
                $Solicitud = Solicitud::find($composicion_de_modelo_de_solicitudes->solicitud_id);
                $Solicitud->contarSuperficieSolicitud($id);
            }
        }
        // FIN composicion_de_modelo_de_solicitudes

        if($modelo == 'User') {
            if ($accion == 'b') {
                
                if ($id == Auth::user()->id) {
                    $acc_ant_mensaje['error'] = true;
                    $acc_ant_mensaje['detalle'] = 'Error! No puede eliminar su propio usuario';
                    $acc_ant_mensaje['class'] = 'alert-danger';                    
                    
                    /*
                    $acc_ant_errorInfo[0] = -1;
                    $acc_ant_errorInfo[1] = 999001;
                    $acc_ant_errorInfo[2] = 'Error! No puede eliminar su propio usuario';
                    $acc_ant_mensaje = $this->MensajeErrorDB($acc_ant_errorInfo, $modelo);
                    */
                }

            }
        }
    
    return $acc_ant_mensaje;

    }

    public function accionesPosteriores($modelo, $accion, $id) {
        $id = intval($id);

        // INICIO Composicion_de_modelo
        if($modelo == 'Composicion_de_modelo') {
            if (($accion == 'a' and $id <> '-1') or $accion == 'm') {
                $Composicion_de_modelo = Composicion_de_modelo::find($id);
                $modelo_id = $Composicion_de_modelo->modelo_id;
                $ComponentesDeModelo = Composicion_de_modelo::
                    where('modelo_id', $modelo_id)
                    ->get();
                $total_de_metros_cuadrados = 0;
                foreach ($ComponentesDeModelo as $Componente) {
                    $total_de_metros_cuadrados = $total_de_metros_cuadrados +($Componente->ancho*$Componente->largo);
                }

                $Modelo = Modelo::find($modelo_id);
                $Modelo->total_de_metros_cuadrados = $total_de_metros_cuadrados;
                $Modelo->save();
            }
        }
        // FIN Composicion_de_modelo

        // INICIO composicion_de_modelo_de_solicitudes
        if($modelo == 'composicion_de_modelo_de_solicitudes') {
            if (($accion == 'a' and $id <> '-1') or $accion == 'm') {
                $composicion_de_modelo_de_solicitudes = composicion_de_modelo_de_solicitudes::find($id);
                $Solicitud = Solicitud::find($composicion_de_modelo_de_solicitudes->solicitud_id);
                $Solicitud->contarSuperficieSolicitud();
            }
        }
        // FIN composicion_de_modelo_de_solicitudes

        // INICIO Cliente
        if($modelo == 'Cliente') {
            if (($accion == 'a' and $id <> '-1') and $id > 0) {
                $Cliente = Cliente::find($id);
                $Cliente->sucursal_id = Auth::user()->sucursal_id;
                $Cliente->user_id = Auth::user()->id;
                $Cliente->save();
            }
        }
        // FIN Cliente

        // INICIO User
        if($modelo == 'User') {
            if (($accion == 'a' and $id <> '-1') or $accion == 'm') {
                $User = User::find($id);
                $User->password = bcrypt($User->clave);
                //dd($User->password);
                $User->save();
                
            }
        }
        // FIN User




    }

}
