<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TablasEnPlurarl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function tablasEnPlural() {

        $tb_plural_distintas = [
            "Forma_de_pago" => "formas_de_pago", 
            "Sucursal" => "sucursales", 
            "Pais" => "paises", 
            "Localidad" => "localidades",
            "Tipo_de_documento" => "tipos_de_documentos",
            "Situacion_de_iva" => "situaciones_de_iva",
            "Opcion" => "opciones",
            "Rol_de_usuario" => "roles_de_usuario",
            "Lista_de_precio" => "listas_de_precio",
            "Imagen_de_modelo" => "imagenes_de_modelos",
            "Solicitud" => "solicitudes",
            "Composicion_de_modelo_de_solicitud" => "composicion_de_modelo_de_solicitudes",
            "Zona_local" => "zonas_locales",
            "Vendedor" => "vendedores"
        ];
        
        return $tb_plural_distintas;

    }


}
