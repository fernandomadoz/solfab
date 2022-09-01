<?php

namespace App\Http\Controllers;
use App\Solicitud;
use App\User;
use Auth;
use Excel;
use App\Cliente;
use App\Http\Controllers\SolicitudController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
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
    public function index()
    {   
        //$Solicitudes = Solicitud::all();

        $titulo = '';
        $titulo_2 = '';
        $Solicitudes = NULL;
        $Solicitudes_2 = NULL;
        $Autorizaciones = NULL;

        $SolicitudController = new SolicitudController();

        if (Auth::user()->rol_de_usuario_id == 1 or Auth::user()->rol_de_usuario_id == 2) {
            $titulo = 'Mis Solicitudes Pendientes de Aprobaci&oacute;n';
            $traerSolicitudes = $SolicitudController->traerSolicitudes('p');
            $Solicitudes = $traerSolicitudes['Solicitudes'];
            
            $titulo_2 = 'Mis Solicitudes para Revisar';
            $traerSolicitudes = $SolicitudController->traerSolicitudes('r');
            $Solicitudes_2 = $traerSolicitudes['Solicitudes'];

            $Autorizaciones = User::whereRaw('(rol_de_usuario_id = 4)')->get();
            //dd($Autorizaciones);
        }
        if (Auth::user()->rol_de_usuario_id == 3) {
            $titulo = 'Mis Solicitudes Pendientes de Aprobaci&oacute;n';
            $traerSolicitudes = $SolicitudController->traerSolicitudes('p');
            $Solicitudes = $traerSolicitudes['Solicitudes'];

            $titulo_2 = 'Mis Solicitudes para Revisar';
            $traerSolicitudes = $SolicitudController->traerSolicitudes('r');
            $Solicitudes_2 = $traerSolicitudes['Solicitudes'];
        }

        return View('welcome')
        ->with('titulo', $titulo)
        ->with('Solicitudes', $Solicitudes)
        ->with('titulo_2', $titulo_2)
        ->with('Solicitudes_2', $Solicitudes_2)
        ->with('Autorizaciones', $Autorizaciones);
    }


    public function notificaciones()
    {   
        $Solicitudes = NULL;
        $cant_solicitudes = 0;
        $cant_notificaciones = 0;

        $SolicitudController = new SolicitudController();
        
        if (Auth::user()->rol_de_usuario_id == 1 or Auth::user()->rol_de_usuario_id == 2) {
            $traerSolicitudes = $SolicitudController->traerSolicitudes('p');
            $cant_solicitudes = count($traerSolicitudes['Solicitudes']);

            $traerSolicitudes = $SolicitudController->traerSolicitudes('r');
            $cant_solicitudes_2 = count($traerSolicitudes['Solicitudes']);

            $cant_autorizaciones = User::whereRaw('(rol_de_usuario_id = 4)')->count();

            $cant_notificaciones = $cant_solicitudes + $cant_solicitudes_2 + $cant_autorizaciones;
        }
        if (Auth::user()->rol_de_usuario_id == 3) {
            $traerSolicitudes = $SolicitudController->traerSolicitudes('p');
            $cant_solicitudes = count($traerSolicitudes['Solicitudes']);

            $traerSolicitudes = $SolicitudController->traerSolicitudes('r');
            $cant_solicitudes_2 = count($traerSolicitudes['Solicitudes']);
            
            $cant_notificaciones = $cant_solicitudes + $cant_solicitudes_2;
        }


        return $cant_notificaciones;
    }


}
