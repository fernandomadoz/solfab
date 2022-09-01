@extends('layouts.backend')

@section('contenido')

<?php 
use \App\Http\Controllers\GenericController; 
$gCont = new GenericController;

$rol_de_usuario_id = Auth::user()->rol_de_usuario_id;

if ($Solicitud->sino_aprobado_administracion == 'SI' and $rol_de_usuario_id == 3) {
    $modificar_solicitud = 'N';
}
else {
    $modificar_solicitud = 'S';
}

if ($Solicitud->sino_aprobado_administracion == 'SI') {
    $modificar_contrato = 'S';
}
else {
    $modificar_contrato = 'N';
}

$anticipo = 0;
if ($Solicitud->anticipo > 0) {
  $anticipo = $Solicitud->anticipo;
}



$cuotas_anticipo = 0;
if ($Solicitud->cuotas_anticipo > 0) {
  $cuotas_anticipo = $Solicitud->cuotas_anticipo;
}

if ($Solicitud->fecha_de_contrato <> '') {
  $fecha_de_contrato = 'moment("'.$Solicitud->fecha_de_contrato.'").format("DD/MM/YYYY")';
  $fecha_de_contrato_modal = $fecha_de_contrato;
  $habilitar_imprimir_contrato = 'true';
}
else {
  $fecha_de_contrato = "null";
  $fecha_de_contrato_modal = "moment().format('DD/MM/YYYY')";
  $habilitar_imprimir_contrato = 'false';
}


if ($Solicitud->fecha_de_cancelacion_del_anticipo <> '') {
  $fecha_de_cancelacion_del_anticipo = 'moment("'.$Solicitud->fecha_de_cancelacion_del_anticipo.'").toDate()';
}
else {
  $fecha_de_cancelacion_del_anticipo = "null";
}

$max_cant_cuotas_contrato = 72;
if ($Solicitud->Lista_de_precio->Forma_de_pago->id == 1) {
  $mostrar_calcular_cuotas_contrato = 'false';
  $a_distribuir = 0;
}
if ($Solicitud->Lista_de_precio->Forma_de_pago->id == 2) {
  $mostrar_calcular_cuotas_contrato = 'true';
  $max_cant_cuotas_contrato = 12;
  $a_distribuir = $Solicitud->valor_total/2;
}
if ($Solicitud->Lista_de_precio->Forma_de_pago->id == 3) {
  $mostrar_calcular_cuotas_contrato = 'true';
  $a_distribuir = $Solicitud->valor_total;
}
if ($Solicitud->Lista_de_precio->Forma_de_pago->id == 4) {
  $mostrar_calcular_cuotas_contrato = 'true';
  $a_distribuir = $Solicitud->valor_total - $anticipo;
}


$readonly_a_distribuir = 'readonly';
if ($rol_de_usuario_id < 3) {
    $readonly_a_distribuir = '';
}

if (count($Cuotas) > 0) {
  $mostrar_cuotas = 'true';
}
else {
  $mostrar_cuotas = 'false';
}


$mostrar_botonera_contrato = 'false';
if ($Solicitud->Lista_de_precio->Forma_de_pago->id == 1 or count($Cuotas) > 0) {
  $mostrar_botonera_contrato = 'true';
}

// SETEO CONTRACCION DE PANELES

if ($modificar_contrato == 'S') {
  $colapsed_panel_solicitud = 'collapsed-box';
}
else {
  $colapsed_panel_solicitud = '';
}

if ($habilitar_imprimir_contrato == 'true') {
  $colapsed_panel_contrato = 'collapsed-box';
}
else {
  $colapsed_panel_contrato = '';
}


$para_finalizar = 'N';
if ($Solicitud->sino_aprobado_garantes == 'SI') {
  $para_finalizar = 'S';
}

$vendedor_id = 'null';
if ($Solicitud->vendedor_id > 0) {
    $vendedor_id = $Solicitud->vendedor_id;
}




// FIN SETEO CONTRACCION DE PANELES

?>

<!-- LIBRERIAS -->
  <!-- vue.js -->
  <script src="<?php echo env('PATH_PUBLIC')?>js/vue/vue.js"></script>
  <script src="<?php echo env('PATH_PUBLIC')?>js/vee-validate/dist/vee-validate.js"></script>
  <script src="<?php echo env('PATH_PUBLIC')?>js/vee-validate/dist/locale/es.js"></script>
  <script type="text/javascript" src="<?php echo env('PATH_PUBLIC')?>js/vue-form-generator/vfg.js"></script>
  <link rel="stylesheet" type="text/css" href="<?php echo env('PATH_PUBLIC')?>js/vue-form-generator/vfg.css">

  <script src="https://cdn.jsdelivr.net/vue.resource/1.3.1/vue-resource.min.js"></script>

  <link rel="stylesheet" type="text/css" href="<?php echo env('PATH_PUBLIC')?>js/bootstrap-select/css/bootstrap-select.min.css">
  <script type="text/javascript" src="<?php echo env('PATH_PUBLIC')?>js/bootstrap-select/js/bootstrap-select.min.js"></script>

  <!-- bootstrap slider -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>plugins/bootstrap-slider/slider.css">

  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>css/style.css">
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>css/generic.css">

  <!-- moment.min.js -->
  <!-- script src="<?php echo env('PATH_PUBLIC')?>js/Moment/moment-with-locales.min.js"></script -->
  <script src="<?php echo env('PATH_PUBLIC')?>js/Moment/moment.min.js"></script>
  <!-- datetimepicker.js -->
  <script src="<?php echo env('PATH_PUBLIC')?>js/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="<?php echo env('PATH_PUBLIC')?>js/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css">  

<!-- LIBRERIAS -->

<style type="text/css">
  .box-header:before, .box-body:before, .box-footer:before, .box-header:after {
    display: contents;
  }
  .box-body:after, .box-footer:after {
    display: block;
  }
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
<h1>
  Solicitud: <?php echo $Solicitud->id; ?>
  <small>Cliente: <?php echo $Solicitud->Cliente->id; ?> <?php echo $Solicitud->Cliente->apellido; ?> <?php echo $Solicitud->Cliente->nombre; ?></small>
</h1>
<ol class="breadcrumb">
  <li><a href="<?php echo env('PATH_PUBLIC')?>"><i class="fa fa-home"></i> Home</a></li>
  <li><a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/list">Solicitudes</a></li>
  <li class="active">Solicitud </li>
</ol>
</section>

<!-- MAIN CONTENT -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">



        <!-- PANEL SOLICITUD -->
          <div class="box box-primary <?php echo $colapsed_panel_solicitud ?>" style="overflow: auto">
            <div class="box-header with-border" id="box-header-solicitud">
                <i class="fa fa-file-text-o"></i> 
                <h3 class="box-title" style="margin-top: 8px; margin-left: 10px">Datos de la Solicitud</h3>
                <h5 style="margin-top: 8px; margin-left: 10px">Fecha de la Solicitud: <?php echo $gCont->FormatoFecha($Solicitud->fecha_de_firma_de_solicitud); ?></h5>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">


              {!! Form::open(array
                (
                'action' => 'SolicitudController@GuardarFormaDePagoyResumenParaEnvio', 
                'role' => 'form',
                'method' => 'POST',
                'id' => "form_gen_modelo",
                'enctype' => 'multipart/form-data',
                'class' => 'form-vertical',
                'ref' => 'form'
                )) 
              !!}
              <div id="app"> 

                <!-- CLIENTE -->
                  <div class="col-lg-4 col-xs-12">
                    <!-- small box -->
                    <div class="small-box bg-yellow datos-expandle-solicitud">
                      <div class="inner">
                        <h4><?php echo $Solicitud->Cliente->nombre ?> <?php echo $Solicitud->Cliente->apellido ?></h4>

                        <p><?php echo $Solicitud->Cliente->Tipo_de_documento->tipo_de_documento ?>: <?php echo $Solicitud->Cliente->nro_de_documento ?></p>
                      </div>
                      <div class="icon">
                        <i class="ion ion-person"></i>
                      </div>

                      <div class="box box-default">
                        <div class="box-header with-border">
                          <h5>Mas info</h5>

                          <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                          </div>
                          <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body ">
                          <p>Domicilio: <?php echo $Solicitud->Cliente->domicilio ?></p>
                          <p>Localidad: <?php echo $Solicitud->Cliente->Localidad->localidad ?>, <?php echo $Solicitud->Cliente->Localidad->Provincia->provincia ?>, <?php echo $Solicitud->Cliente->Localidad->Provincia->Pais->pais ?></p>
                          <p>Tel Fijo: <?php echo $Solicitud->Cliente->telefono_fijo ?> - Celular: <?php echo $Solicitud->Cliente->telefono_fijo ?></p>
                          <p>Email: <?php echo $Solicitud->Cliente->email_correo ?></p>
                        </div>
                        <!-- /.box-body -->
                      </div>
                      <div class="small-box-footer">
                        <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM('Cliente', 'm', <?php echo $Solicitud->Cliente->id ?>)">Modificar Datos</button>
                        <?php if ($modificar_solicitud == 'S') { ?>
                        <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-solicitud-list" onclick="cambiarCliente()">Cambiar Cliente</button>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                <!-- CLIENTE -->

                <!-- MODELO -->
                  <div class="col-lg-4 col-xs-12">
                    <!-- small box -->
                    <div class="small-box bg-red datos-expandle-solicitud">
                      <div class="inner">
                        <h4><?php echo $Solicitud->Modelo->modelo ?> </h4>

                        <p><?php echo $Solicitud->total_de_metros_cuadrados ?> m<sup>2</sup></p>
                      </div>
                      <div class="icon">
                        <i class="ion ion-home"></i>
                      </div>

                      <div class="box box-default">
                        <div class="box-header with-border">
                          <h5>Mas info</h5>

                          <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                          </div>
                          <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body ">
                            <?php foreach ($ComponentesDeModeloSolicitud as $Componente) { ?> 
                            <?php 
                            $observaciones = $Componente->observaciones;
                            if ($observaciones <> '') {
                              $txt_obs = " ($observaciones)";
                            }
                            else {
                              $txt_obs = "";
                            }
                            ?>
                            <span class="badge datos-finales-asistente"><?php echo $Componente->Articulo->articulo ?> - <?php echo $gCont->formatoNumero($Componente->ancho, 'decimal'); ?> x <?php echo $gCont->formatoNumero($Componente->largo, 'decimal'); ?><?php echo $txt_obs ?></span>
                            <?php if ($modificar_solicitud == 'S') { ?>
                            <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM('Composicion_de_modelo_de_solicitud', 'm', <?php echo  $Componente->id ?>)">Modificar</button>
                            <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM('Composicion_de_modelo_de_solicitud', 'b', <?php echo  $Componente->id ?>)">Eliminar</button>
                            <?php } ?>
                            <br>
                            <?php } ?>
                        </div>
                        <!-- /.box-body -->
                      </div>
                      <div class="small-box-footer">
                        <?php if ($modificar_solicitud == 'S') { ?>
                        <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-solicitud-list" onclick="cambiarModelo()">Cambiar Modelo</button>
                        <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-solicitud-abm" onclick="crearABM('Composicion_de_modelo_de_solicitud', 'a', '')">Agregar Componente</button>   
                        <?php } ?>             
                      </div>
                      
                    </div>
                  </div>
                <!-- MODELO -->

                <!-- FORMA DE PAGO -->
                  <?php 
                  if ($Solicitud->anticipo > 0) {
                    if($Solicitud->sino_contado == 'SI') {
                      $detalle_anticipo = ' al Contado';
                    }
                    else {
                      $detalle_anticipo = ' en '.$Solicitud->cuotas_anticipo.' cuotas';  
                    }
                  }
                  else {
                    $detalle_anticipo = '';
                  }
                  ?>
                  <div class="col-lg-4 col-xs-12">
                    <!-- small box -->
                    <div class="small-box bg-green datos-expandle-solicitud">
                      <div class="inner">
                        <h4>Valor Total: $ <?php echo $gCont->formatoNumero($Solicitud->valor_total, 'decimal') ?> </h4>

                        <p>Forma de Pago: <?php echo $Solicitud->lista_de_precio->lista_de_precio ?></p>
                      </div>
                      <div class="icon">
                        <i class="fa fa-dollar"></i>
                      </div>

                      <div class="box box-default">
                        <div class="box-header with-border">
                          <h5>Mas info</h5>

                          <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                          </div>
                          <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body ">
                          <p>Anticipo: $ <?php echo $gCont->formatoNumero($Solicitud->anticipo, 'decimal').$detalle_anticipo ?></p>
                          <?php if ($Solicitud->vendedor_id > 0) { ?>
                          <p>Vendedor: $ <?php echo $Solicitud->vendedor->nombre ?></p>
                          <?php } ?>
                          <p><?php echo $Solicitud->observaciones ?></p>
                        </div>
                        <!-- /.box-body -->
                      </div>
                      <div class="small-box-footer">
                        <?php if ($modificar_solicitud == 'S') { ?>
                        <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-solicitud-forma-de-pago">Modificar Datos</button>
                        <?php } ?>
                      </div>
                      
                    </div>
                  </div>
                <!-- FORMA DE PAGO -->

                <!-- BOTON IMPRIMIR -->
                <div class="col-lg-4 col-xs-12">
                  <a target="_blank" href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/imprimir-solicitud/<?php echo $Solicitud->id ?>">
                    <button type="button" class="btn btn-block btn-primary btn-lg"><i class=" fa fa-print"></i> Imprimir Solicitud</button>
                  </a>
                </div>

              </div>
              {!! Form::close() !!}




            </div>

            <?php if ($Solicitud->sino_aprobado_administracion == 'NO') { ?>
            <!-- APROBACION SOLICITAR REVISION --> 
              <?php 
              $checked_sino_aprobado_solicitar_revision = '';
              $class_sino_aprobado_solicitar_revision = '';
              if ($Solicitud->sino_aprobado_solicitar_revision == 'SI') {
                $checked_sino_aprobado_solicitar_revision = 'checked="checked"';
                $class_sino_aprobado_solicitar_revision = 'bg-yellow';
                $txt_sino_aprobado_solicitar_revision = 'Solicitada';
              }
              if ($Solicitud->sino_aprobado_solicitar_revision == 'NO') {
                $checked_sino_aprobado_solicitar_revision = '';
                $class_sino_aprobado_solicitar_revision = 'bg-blue';
                $txt_sino_aprobado_solicitar_revision = 'Atendida';
              }
              if ($Solicitud->sino_aprobado_solicitar_revision == '') {
                $checked_sino_aprobado_solicitar_revision = '';
                $class_sino_aprobado_solicitar_revision = 'bg-grey';
                $txt_sino_aprobado_solicitar_revision = '';
              }
              ?>

              <div class="box-footer <?php echo $class_sino_aprobado_solicitar_revision ?>" id="box-footer-solicitud-solicitar_revision">
                <div class="col-xs-6">
                  <!-- Rounded switch -->
                  <div class="pull-left">
                    <span class="label_aprobacion">Revisi&oacute;n</span>
                    <?php if ($rol_de_usuario_id > 0) { ?>
                    <label class="switch">
                      <input id="sino_aprobado_solicitar_revision" type="checkbox" onclick="aprobacionSolicitarRevision(this.checked)" <?php echo $checked_sino_aprobado_solicitar_revision ?>>
                      <span class="slider round"></span>
                    </label>
                    <?php } ?>
                  </div>
                  <span id="estado_sino_aprobado_solicitar_revision" class="badge datos-finales-asistente" style="margin-top: 7px; background-color: #333; margin-left: 20px;"><?php echo $txt_sino_aprobado_solicitar_revision ?></span>
                </div>
                <div class="col-xs-6">
                  <?php if ($rol_de_usuario_id > 0) { ?>
                  <textarea maxlength="250" id="observaciones_aprobado_solicitar_revision" name="observaciones_aprobado_solicitar_revision" class="form-control" placeholder="Indique los motivos de la solicitud de revision" onkeydown="guardarObsSolRev(this.value)"><?php echo $Solicitud->observaciones_aprobado_solicitar_revision ?></textarea>
                  <?php } 
                  else { ?>
                  <?php echo $Solicitud->observaciones_aprobado_solicitar_revision ?>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
            <!-- FIN APROBACION SOLICITAR REVISION --> 

            <!-- APROBACION ADM --> 
              <?php 
              $checked_sino_aprobado_administracion = '';
              $class_sino_aprobado_administracion = '';
              if ($Solicitud->sino_aprobado_administracion == 'SI') {
                $checked_sino_aprobado_administracion = 'checked="checked"';
                $class_sino_aprobado_administracion = 'bg-olive';
                $txt_sino_aprobado_administracion = 'Aprobado';
                $class_observaciones_aprobado_administracion = 'oculto';
              }
              if ($Solicitud->sino_aprobado_administracion == 'NO') {
                $checked_sino_aprobado_administracion = '';
                $class_sino_aprobado_administracion = 'bg-red';
                $txt_sino_aprobado_administracion = 'Desaprobado';
                $class_observaciones_aprobado_administracion = 'visible';
              }
              if ($Solicitud->sino_aprobado_administracion == '') {
                $checked_sino_aprobado_administracion = '';
                $class_sino_aprobado_administracion = 'bg-grey';
                $txt_sino_aprobado_administracion = '';
                $class_observaciones_aprobado_administracion = 'oculto';
              }
              ?>

              <div class="box-footer <?php echo $class_sino_aprobado_administracion ?>" id="box-footer-solicitud-administracion">
                <div class="col-xs-6">
                  <!-- Rounded switch -->
                  <div class="pull-left">
                    <span class="label_aprobacion">Administrativa</span>
                    <?php if ($rol_de_usuario_id < 3) { ?>
                    <label class="switch">
                      <input id="sino_aprobado_administracion" type="checkbox" onclick="aprobacionAdministracion(this.checked)" <?php echo $checked_sino_aprobado_administracion ?>>
                      <span class="slider round"></span>
                    </label>
                    <?php } ?>
                  </div>
                  <span id="estado_sino_aprobado_administracion" class="badge datos-finales-asistente" style="margin-top: 7px; background-color: #333; margin-left: 20px;"><?php echo $txt_sino_aprobado_administracion ?></span>
                </div>
                <div class="col-xs-6">
                  <?php if ($rol_de_usuario_id < 3) { ?>
                  <textarea maxlength="250" id="observaciones_aprobado_administracion" name="observaciones_aprobado_administracion" class="form-control <?php echo $class_observaciones_aprobado_administracion ?>" placeholder="Indique los motivos de la desaprobacion" onkeydown="guardarObsAdm(this.value)"><?php echo $Solicitud->observaciones_aprobado_administracion ?></textarea>
                  <?php } 
                  else { ?>
                  <?php echo $Solicitud->observaciones_aprobado_administracion ?>
                  <?php } ?>
                </div>
              </div>
            <!-- FIN APROBACION ADM --> 



          </div>
        <!-- PANEL SOLICITUD -->

        <!-- PANEL CONTRATO -->
          <!-- INICIO APP CONTRATO 2 -->
            <div id="app-contrato">    
              <?php if ($modificar_contrato == 'S') { ?>
                <div class="box box-primary <?php echo $colapsed_panel_contrato ?>">
                    <div class="box-header with-border" id="box-header-solicitud" style="overflow: auto">
                      <div class="col-lg-4">
                        <i class="fa fa-pencil-square-o"></i> 
                        <h3 class="box-title" style="margin-top: 8px; margin-left: 10px">Datos del Contrato</h3>
                        <h5 style="margin-top: 8px; margin-left: 10px" v-show="fecha_de_contrato">
                          Fecha del Contrato: @{{ fecha_de_contrato }}                            
                          <a class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-solicitud-print-contrato">
                            <i class="fa fa-pencil"></i> 
                          </a>
                        </h5>
                      </div>

                      <div class="col-lg-4" v-show="observaciones_contrato">
                          <blockquote>
                            <small>Observaciones</small>
                            <p>@{{ observaciones_contrato }} <a class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-solicitud-print-contrato">
                        <i class="fa fa-pencil"></i> 
                      </a></p>
                          </blockquote>
                      </div>

                      <div class="col-lg-2" v-show="pagado">
                          <blockquote>
                            <small>Pagado</small>
                            <p>@{{ pagado }} <a class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-solicitud-print-contrato">
                        <i class="fa fa-pencil"></i> 
                      </a></p>
                          </blockquote>
                      </div>

                      <div class="col-lg-2" v-show="pagado">
                          <blockquote>
                            <small>Cuotas</small>
                            <p>@{{ cant_cuotas_contrato }} </p>
                          </blockquote>
                      </div>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                      </div>
                    </div>

                  <!-- /.box-header -->
                  <div class="box-body">
                
                    <div class="col-md-4 col-sm-6 col-xs-12">

                      <div class="vue-form-generator">
                        <fieldset>

                          <div v-show="mostrar_calcular_cuotas_contrato">
                            <div class="form-group required">
                              <label for="a_distribuir">A distribuir</label>                          
                              <input v-validate="'required'" type="text" class="form-control" id="a_distribuir" name="a_distribuir" v-model="a_distribuir" placeholder="importe" required="required" readonly="<?php echo $readonly_a_distribuir ?>">       
                              <span v-show="errors.has('a_distribuir')" class="text-danger">@{{ errors.first('a_distribuir') }}</span>
                            </div>

                            <div class="form-group required">
                              <label for="cant_cuotas_contrato">Cantidad de Cuotas (no mayor a <?php echo $max_cant_cuotas_contrato ?>)</label>                          
                              <input v-validate="'required'" type="text" class="form-control" id="cant_cuotas_contrato" name="cant_cuotas_contrato" v-model="cant_cuotas_contrato" placeholder="cuotas" required="required" max="<?php echo $max_cant_cuotas_contrato ?>">       
                              <span v-show="errors.has('cant_cuotas_contrato')" class="text-danger">@{{ errors.first('cant_cuotas_contrato') }}</span>
                            </div>

                            <div class="form-group required">
                              <label for="primer_vencimiento">Primer Vencimiento</label>
                              <div class="input-group date datetimepicker_class">
                                  <input v-validate="'required'" type="text" id="primer_vencimiento" name="primer_vencimiento" v-model="primer_vencimiento" class="form-control"/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                              </div>
                              <span v-show="errors.has('primer_vencimiento')" class="text-danger">@{{ errors.first('primer_vencimiento') }}</span>
                            </div>

                            <div class="form-group required">
                              <label for="cant_periodo">Cantidad de Periodos</label>                          
                              <input v-validate="'required'" type="text" class="form-control" id="cant_periodo" name="cant_periodo" v-model="cant_periodo" placeholder="cuotas" required="required">       
                              <span v-show="errors.has('cant_periodo')" class="text-danger">@{{ errors.first('cant_periodo') }}</span>      
                            </div>


                            <div class="form-group required">
                              <label for="periodo">Periodo</label>     
                              <select  v-validate="'required'" class="form-control" id="periodo" name="periodo" v-model="periodo" required="required">
                                <option value="d">Dias</option>
                                <option value="w">Semanas</option>
                                <option value="M">Mes</option>
                              </select>    
                              <span v-show="errors.has('periodo')" class="text-danger">@{{ errors.first('periodo') }}</span>      
                            </div>

                            <button type="button" class="btn btn-lg form-control" v-on:click="calcular_cuotas_contrato">
                              <i class="fa fa-fw fa-calculator"></i> 
                              Calcular Cuotas
                            </button>     
                            <br>

                            <div class="alert alert-danger alert-dismissible" v-show="mensaje_error != ''">
                              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                              <h4><i class="icon fa fa-ban"></i> Error!</h4>
                               @{{ mensaje_error }} 
                            </div>                       
                          </div>

                        </fieldset>
                      </div>
                    </div>
                    <!-- INICIO FORM CONTRATO -->
                      {!! Form::open(array
                        (
                        'action' => 'SolicitudController@GuardarDistribucionDeCuotas', 
                        'role' => 'form',
                        'method' => 'POST',
                        'id' => "form_gen_modelo",
                        'enctype' => 'multipart/form-data',
                        'class' => 'form-vertical',
                        'ref' => 'form'
                        )) 
                      !!}
                      <div class="col-lg-8 col-sm-12 col-xs-12" v-show="mostrar_cuotas">
                        <div class="box">
                          <div class="box-header">
                            <h3 class="box-title">Distribuci&oacute;n de cuotas</h3>
                          </div>
                          <div class="box-body no-padding">                      
                            <table id="table_cuotas" class="table table-bordered table-striped" style="max-width: 500px; width: 100%">
                              <thead>
                              <tr>
                                <th>Nro</th>
                                <th>Importe</th>
                                <th>%</th>
                                <th>Vencimiento</th>
                              </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($Cuotas as $Cuota) { ?> 
                                <tr>
                                  <td><?php echo $Cuota->numero_de_cuota ?></td>
                                  <td><?php echo $Cuota->importe ?></td>
                                  <td><?php echo $Cuota->porcentaje ?></td>
                                  <td><?php echo $Cuota->fecha_de_vencimiento ?></td>
                                </tr>  
                                <?php } ?>
                              </tbody>
                            </table>
                          </div>
                          <div class="box-footer">
                            <input type="hidden" name="solicitud_id" value="<?php echo $Solicitud->id ?>">
                            <input type="hidden" name="cant_cuotas_calculadas" v-model="cant_cuotas_calculadas">
                            <input type="hidden" name="td_fields" id="td_fields">
                            <button type="submit" class="btn btn-success btn-lg" style="width: 100%" v-show="mostrar_btn_guardar_cuotas"><i class="fa fa-save"></i> Guardar distribuci&oacute;n de cuotas</button>
                          </div>
                        </div>   
                      </div>


                      {!! Form::close() !!}
                    <!-- FIN FORM CONTRATO -->

                  </div>

                </div>


                <!-- MODAL PRINT CONTRATO -->
                  <div class="modal modal fade" id="modal-solicitud-print-contrato">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                          <h4 class="modal-title"><div id="modal-titulo">Fecha de Contrato y Observaciones Generales</div></h4>
                        </div>
                        <div class="modal-body" id="modal-bodi-abm">


                          <div class="vue-form-generator">
                            <fieldset>

                              <div class="form-group required">
                                <label for="fecha_de_contrato">Fecha de Contrato</label>
                                <div class="input-group date datetimepicker_class">
                                    <input v-validate="'required'" type="text" id="fecha_de_contrato" name="fecha_de_contrato" v-model="fecha_de_contrato_modal" class="form-control" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                                <span v-show="errors.has('fecha_de_contrato')" class="text-danger">@{{ errors.first('fecha_de_contrato') }}</span>
                              </div>

                              <div class="form-group">
                                <label for="pagado">Pagado</label>
                                <input type="number" class="form-control" name="pagado" id="pagado" placeholder="importe" v-model="pagado_modal">
                              </div>

                              <div class="form-group">
                                <label for="observaciones_contrato">Observaciones</label>
                                <textarea class="form-control" name="observaciones_contrato" id="observaciones_contrato" placeholder="Observaciones" v-model="observaciones_contrato_modal"></textarea>
                              </div>

                              <br><br>
                              <div class="form-group">
                                <div class="pull-left">
                                  <span class="label_aprobacion">Imprimir contrato</span>
                                  <label class="switch">
                                    <input id="sino_imprimir_contrato" type="checkbox" checked="checked">
                                    <span class="slider round"></span>
                                  </label>
                                </div>
                              </div>
                              <br><br>


                            </fieldset>
                          </div>

                          <button type="button" class="btn btn-success btn-lg" style="width: 100%" v-on:click="guardar_fecha_de_contrato(<?php echo $Solicitud->id; ?>)"><i class="fa fa-save"></i> Guardar </button>
                         
                        </div>

                      </div>
                      <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                  </div>
                <!-- MODAL PRINT CONTRATO -->
                        
                <!-- BOTONERA CONTRATO -->

                  <div class="box box-primary">

                    <div class="box-header with-border">
                      <a class="btn btn-app" data-toggle="modal" data-target="#modal-solicitud-print-contrato" v-show="!habilitar_imprimir_contrato">
                        <i class="fa fa-edit"></i> Generar Contrato
                      </a>

                      <a class="btn btn-app" v-show="habilitar_imprimir_contrato" href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/imprimir-contrato/<?php echo $Solicitud->id ?>" target="_blank">
                        <i class="fa fa-edit"></i> Imprimir Contrato
                      </a>

                      <a class="btn btn-app" v-show="habilitar_imprimir_contrato" target="_blank" v-on:click="func_garantes">
                        <i class="fa fa-users"></i> Garantes
                      </a>     
                                
                      <a class="btn btn-app" v-show="habilitar_imprimir_contrato" href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/imprimir-adquiriente/<?php echo $Solicitud->id ?>" target="_blank">
                        <i class="fa fa-user"></i> Adquiriente
                      </a>     
                                
                      <a class="btn btn-app" v-show="habilitar_imprimir_contrato" href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/imprimir-caracteristicas-tecnicas/<?php echo $Solicitud->id ?>" target="_blank">
                        <i class="fa fa-gear"></i> Características Técnicas
                      </a>     
                                
                      <a class="btn btn-app" v-show="habilitar_imprimir_contrato" href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/imprimir-normas/<?php echo $Solicitud->id ?>" target="_blank">
                        <i class="fa fa-tasks"></i> Normas
                      </a>     
                                
                      <a class="btn btn-app" v-show="habilitar_imprimir_contrato" href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/imprimir-base-plateas/<?php echo $Solicitud->id ?>" target="_blank">
                        <i class="fa fa-square-o"></i> Base Plateas
                      </a>     
                                
                      <a class="btn btn-app" v-show="habilitar_imprimir_contrato" href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/imprimir-orden-de-fabricacion/<?php echo $Solicitud->id ?>" target="_blank">
                        <i class="fa fa-legal"></i> Orden de Fabricaci&oacute;n
                      </a>   
                                
                      <?php if ($rol_de_usuario_id < 3) { ?>
                      <a class="btn btn-app" v-show="habilitar_imprimir_contrato" href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/imprimir-recibo/<?php echo $Solicitud->id ?>" target="_blank">
                        <i class="fa fa-sticky-note-o"></i> Recibo
                      </a>   
                      <?php } ?>  
                                
                      <a class="btn btn-app" v-show="habilitar_imprimir_contrato" href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/imprimir-requisitos/<?php echo $Solicitud->id ?>" target="_blank">
                        <i class="fa fa-check-circle-o"></i> Requisitos
                      </a>     
                      
                      <?php if ($rol_de_usuario_id < 3) { ?>
                      <a class="btn btn-app" v-show="habilitar_imprimir_contrato" href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/imprimir-anexo2/<?php echo $Solicitud->id ?>" target="_blank">
                        <i class="fa fa-clone"></i> Anexo II
                      </a>     
                      <?php } ?>
                                
                      <a class="btn btn-app" v-show="habilitar_imprimir_contrato" href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/imprimir-autorizacion-foto/<?php echo $Solicitud->id ?>" target="_blank">
                        <i class="fa fa-camera"></i> Autorizaci&oacute;n Foto
                      </a>     

                      <a class="btn btn-app" v-show="habilitar_imprimir_contrato" href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/imprimir-publicidad/<?php echo $Solicitud->id ?>" target="_blank">
                        <i class="fa fa-bullhorn"></i> Publicidad
                      </a>     

                    </div>
                  </div>
                <!-- BOTONERA CONTRATO -->

                <!-- PANEL GARANTES -->

                    <div class="box box-primary" v-show="mostrar_garantes">
                      <div class="box-header with-border">
                        <h3 class="box-title">GARANTES</h3>
                        <div class="col-lg-4 col-lg-2" style="float: right; margin-right: 40px;">
                          <a target="_blank" href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/imprimir-garantes/<?php echo $Solicitud->id ?>">
                            <button type="button" class="btn btn-block btn-primary btn-md"><i class=" fa fa-print"></i> Imprimir Garantes</button>
                          </a>
                        </div>

                        <div class="box-tools pull-right">
                          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                          </button>
                        </div>
                        <!-- /.box-tools -->
                      </div>
                      <!-- /.box-header -->
                      <div class="box-body" style="" id="body_garantes">
                      </div>
                      <!-- /.box-body -->


                      <!-- APROBACION GARANTES --> 
                        <?php 
                        $checked_sino_aprobado_garantes = '';
                        $class_sino_aprobado_garantes = '';
                        if ($Solicitud->sino_aprobado_garantes == 'SI') {
                          $checked_sino_aprobado_garantes = 'checked="checked"';
                          $class_sino_aprobado_garantes = 'bg-olive';
                          $txt_sino_aprobado_garantes = 'Aprobado';
                          $observaciones_aprobado_garantes = 'oculto';
                        }
                        if ($Solicitud->sino_aprobado_garantes == 'NO') {
                          $checked_sino_aprobado_garantes = '';
                          $class_sino_aprobado_garantes = 'bg-red';
                          $txt_sino_aprobado_garantes = 'Desaprobado';
                          $observaciones_aprobado_garantes = 'visible';
                        }
                        if ($Solicitud->sino_aprobado_garantes == '') {
                          $checked_sino_aprobado_garantes = '';
                          $class_sino_aprobado_garantes = 'bg-grey';
                          $txt_sino_aprobado_garantes = '';
                          $observaciones_aprobado_garantes = 'oculto';
                        }
                        ?>

                        <div class="box-footer <?php echo $class_sino_aprobado_garantes ?>" id="box-footer-solicitud-garantes">
                          <div class="col-xs-6">
                            <!-- Rounded switch -->
                            <div class="pull-left">
                              <span class="label_aprobacion">Garantes</span>
                              <?php if ($rol_de_usuario_id < 3) { ?>
                              <label class="switch">
                                <input id="sino_aprobado_garantes" type="checkbox" onclick="aprobacionGarantes(this.checked)" <?php echo $checked_sino_aprobado_garantes ?>>
                                <span class="slider round"></span>
                              </label>
                              <?php } ?>
                            </div>
                            <span id="estado_sino_aprobado_garantes" class="badge datos-finales-asistente" style="margin-top: 7px; background-color: #333; margin-left: 20px;"><?php echo $txt_sino_aprobado_garantes ?></span>
                          </div>
                          <div class="col-xs-6">
                            <?php if ($rol_de_usuario_id < 3) { ?>
                            <textarea maxlength="250" id="observaciones_aprobado_garantes" name="observaciones_aprobado_garantes" class="form-control <?php echo $observaciones_aprobado_garantes ?>" placeholder="Indique los motivos de la desaprobacion" onkeydown="guardarObsGar(this.value)"><?php echo $Solicitud->observaciones_aprobado_garantes ?></textarea>
                            <?php } 
                            else { ?>
                            <?php echo $Solicitud->observaciones_aprobado_garantes ?>
                            <?php } ?>
                          </div>        
                        </div>
                      <!-- FIN APROBACION GARANTES --> 

                    </div>
                    <!-- /.box -->
                <!-- FIN PANEL GARANTES -->
                
                <!--div class="col-lg-12">            
                  <pre>@{{ $data }}</pre>
                </div-->                   

              <?php } ?>
            </div>        
          <!-- FIN APP CONTRATO 2 -->
        <!-- PANEL CONTRATO -->
      </div>

      <!-- PANEL SOLICITUD -->
      <div class="col-lg-12" style="overflow: auto;">
        <div class="box ">
          <div class="box-header with-border" id="box-header-solicitud">



            <!-- APROBACION FINALIZADA --> 
              <?php 
              if ($para_finalizar == 'S') {
                $checked_sino_aprobado_finalizada = '';
                $class_sino_aprobado_finalizada = '';
                if ($Solicitud->sino_aprobado_finalizada == 'SI') {
                  $checked_sino_aprobado_finalizada = 'checked="checked"';
                  $class_sino_aprobado_finalizada = 'bg-olive';
                  $txt_sino_aprobado_finalizada = 'SI';
                  $observaciones_aprobado_finalizada = '';
                }
                if ($Solicitud->sino_aprobado_finalizada == 'NO') {
                  $checked_sino_aprobado_finalizada = '';
                  $class_sino_aprobado_finalizada = 'bg-grey';
                  $txt_sino_aprobado_finalizada = 'NO';
                  $observaciones_aprobado_finalizada = '';
                }
                if ($Solicitud->sino_aprobado_finalizada == '') {
                  $checked_sino_aprobado_finalizada = '';
                  $class_sino_aprobado_finalizada = 'bg-grey';
                  $txt_sino_aprobado_finalizada = '';
                  $observaciones_aprobado_finalizada = 'oculto';
                }
              ?>

                <div class="box-footer <?php echo $class_sino_aprobado_finalizada ?>" id="box-footer-solicitud-finalizada">
                  <div class="col-xs-6">
                    <!-- Rounded switch -->
                    <div class="pull-left">
                      <span class="label_aprobacion">finalizada</span>
                      <?php if ($rol_de_usuario_id < 3) { ?>
                      <label class="switch">
                        <input id="sino_aprobado_finalizada" type="checkbox" onclick="aprobacionfinalizada(this.checked)" <?php echo $checked_sino_aprobado_finalizada ?>>
                        <span class="slider round"></span>
                      </label>
                      <?php } ?>
                    </div>
                    <span id="estado_sino_aprobado_finalizada" class="badge datos-finales-asistente" style="margin-top: 7px; background-color: #333; margin-left: 20px;"><?php echo $txt_sino_aprobado_finalizada ?></span>
                  </div>
                  <div class="col-xs-6">
                    <?php if ($rol_de_usuario_id < 3) { ?>
                    <textarea maxlength="250" id="observaciones_aprobado_finalizada" name="observaciones_aprobado_finalizada" class="form-control <?php echo $observaciones_aprobado_finalizada ?>" placeholder="Indique las observaciones" onkeydown="guardarObsFin(this.value)"><?php echo $Solicitud->observaciones_aprobado_finalizada ?></textarea>
                    <?php } 
                    else { ?>
                    <?php echo $Solicitud->observaciones_aprobado_finalizada ?>
                    <?php } ?>
                  </div>        
                </div>
              <?php } ?>
            <!-- FIN APROBACION FINALIZADA --> 



          </div>
        </div>
      </div>

  </section>
<!-- MAIN CONTENT -->

<!-- MODAL ABM -->
  <div class="modal modal fade" id="modal-solicitud-abm">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="modal-titulo">Info Modal</div></h4>
        </div>
        <div class="modal-body" id="modal-bodi-abm">

        </div>

      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
<!-- MODAL ABM -->

<!-- MODAL LIST -->
  <div class="modal modal fade" id="modal-solicitud-list">
    <div class="modal-dialog" style="width: 900px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="modal-titulo">Seleccionar</div></h4>
        </div>
        <div class="modal-body" id="modal-bodi-list">
         
        </div>

      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
<!-- MODAL LIST -->

<!-- MODAL FORM FORMA DE PAGO -->
  <div class="modal modal fade" id="modal-solicitud-forma-de-pago">
    <div class="modal-dialog">
      <div class="modal-content" style="overflow: auto;">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="modal-titulo">Info Modal</div></h4>
        </div>
        <div class="modal-body" id="modal-bodi-forma-de-pago">

          <!-- INICIO FORM FORMA DE PAGO -->
            {!! Form::open(array
              (
              'action' => 'SolicitudController@GuardarFormaDePagoyResumenParaEnvio', 
              'role' => 'form',
              'method' => 'POST',
              'id' => "form_gen_modelo",
              'enctype' => 'multipart/form-data',
              'class' => 'form-vertical',
              'ref' => 'form'
              )) 
            !!}
            <div id="app-forma-de-pago">                  
              <div class="col-md-4 col-sm-6 col-xs-12">

                <div class="vue-form-generator">
                  <fieldset>
                    <div class="form-group required field-selectEx">
                        <label for="lista_de_precio_id">Fecha de vencimiento de la solicitud</label>
                        <div class="input-group date datetimepicker_class">
                            <input type="text" id="fecha_de_vencimiento_de_la_solicitud" name="fecha_de_vencimiento_de_la_solicitud" class="form-control" value="<?php echo $gCont->FormatoFecha($Solicitud->fecha_de_vencimiento_de_la_solicitud); ?>" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <div class="vue-form-generator">
                          <div class="form-group error">
                            <div class="errors help-block"><span id="fecha_de_vencimiento_de_la_solicitud_error"></span></div>
                          </div>
                        </div>
                    </div>
                  </fieldset>
                  </div>

                <vue-form-generator @validated="onValidated" :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
                <input type="hidden" name="solicitud_id" value="<?php echo $Solicitud->id ?>">

                <fieldset>
                  <div class="form-group required field-selectEx">
                      <div class="vue-form-generator">
                        <div class="form-group error">
                          <div class="errors help-block"><span id="general_error"></span></div>
                        </div>
                      </div>
                  </div>
                </fieldset>

              </div>

                    <!--div class="col-lg-12">            
                      <pre>@{{ $data }}</pre>
                    </div-->   

            </div>
              <div class="col-md-1">
              </div>
            <div id="app2">
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="info-box sombra bg-green">
                  <span class="info-box-icon"><i class="fa fa-dollar"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Precio Total</span>
                    <span class="info-box-number">
                      <?php 
                      $readonly = 'readonly="readonly"';
                      if ($rol_de_usuario_id < 3) {
                        $readonly = '';
                      }
                      ?>
                        <input class="form-control" type="number" min="0" name="valor_total" id="valor_total" <?php echo $readonly ?> value="<?php echo $Solicitud->valor_total ?>" step="0.01" min="0">
                        <input type="hidden" name="total_de_metros_cuadrados" id="total_de_metros_cuadrados" value="<?php echo $Solicitud->total_de_metros_cuadrados ?>">
                        <input type="hidden" name="valor_total_calculado" id="valor_total_calculado" value="<?php echo $Solicitud->valor_total ?>">
                        <input type="hidden" name="origen" id="origen" value="edicion">
                    </span>
                  </div>
                  <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
                <div class="vue-form-generator">
                  <div class="form-group error">
                    <div class="errors help-block"><span id="valor_total_error"></span></div>
                  </div>
                </div>
              </div>
            </div>
            {!! Form::close() !!}
          <!-- FIN FORM FORMA DE PAGO -->
         
        </div>

      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
<!-- MODAL FORM FORMA DE PAGO -->




<!-- FUNCIONES ABM Y CAMBIAR CLIENTE Y MODELOS -->
  <?php 
  $gen_url_siguiente = env('PATH_PUBLIC').'Solicitudes/solicitud/ver/'.$Solicitud->id;
  $gen_seteo = array('gen_url_siguiente' => $gen_url_siguiente);
  $gen_seteo_componente = array(
    'gen_url_siguiente' => $gen_url_siguiente, 
    'filtros_por_campo' => array(
        'solicitud_id' => $Solicitud->id, 
        'modelo_id' => $Solicitud->Modelo->id, 
        )
      );
  ?>   
       
  <script type="text/javascript">

    function crearABM(gen_modelo, gen_accion, gen_id = null) {

      if (gen_modelo == 'Composicion_de_modelo_de_solicitud') {
        gen_seteo = '<?php echo serialize($gen_seteo_componente) ?>'
        titulo_de_modal = 'Componente de Modelo'
      }
      else  {
        gen_seteo = '<?php echo serialize($gen_seteo) ?>'
        titulo_de_modal = ''
      }

      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>crearabm',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          gen_modelo: gen_modelo,
          gen_seteo: gen_seteo,
          gen_opcion: '',
          gen_accion: gen_accion,
          gen_id: gen_id
        },
        success: function success(data, status) {        
          $("#modal-bodi-abm").html(data);
          if (gen_accion == 'a') {
            $("#modal-titulo").html('Insertar '+titulo_de_modal);
          }
          if (gen_accion == 'm') {
            $("#modal-titulo").html('Modificar '+titulo_de_modal);
          }
          if (gen_accion == 'b') {
            $("#modal-titulo").html('Borrar '+titulo_de_modal);
          }

        },
        error: function error(xhr, textStatus, errorThrown) {
            alert(errorThrown);
        }
      });
    }

    <?php 
    $gen_seteo_listarCliente = array(
      'gen_url_siguiente' => 'back', 
      'gen_permisos' => ['R'],
      'acciones_extra' => array('Seleccionar,fa fa-hand-pointer-o,Solicitudes/solicitud/cambiar-cliente/'.$Solicitud->id),
      'gen_campos_a_ocultar' => 'email_correo|provincia|pais|tipo_de_documento_id|telefono_fijo|telefono_celular|observaciones|user_id|situacion_de_iva_id|zona_local_id',
      'filtro_where' => array('id', '<>', $Solicitud->cliente_id),
      'tabla_condensada' => 'SI'
    );

    ?>

    function cambiarCliente() {
      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>crearlista',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          gen_modelo: 'Cliente',
          gen_seteo: '<?php echo serialize($gen_seteo_listarCliente) ?>',
          gen_opcion: ''
        },
        success: function success(data, status) {        
          $("#modal-bodi-list").html(data);
        },
        error: function error(xhr, textStatus, errorThrown) {
            alert(errorThrown);
        }
      });
    }


    <?php 
    $gen_seteo_listarModelos = array(
      'gen_url_siguiente' => 'back', 
      'gen_permisos' => ['R'],
      'acciones_extra' => array('Seleccionar,fa fa-hand-pointer-o,Solicitudes/solicitud/cambiar-modelo/'.$Solicitud->id),
      'filtro_where' => array('id', '<>', $Solicitud->modelo_id),
      'tabla_condensada' => 'SI'
    );

    ?>

    function cambiarModelo() {
      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>crearlista',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          gen_modelo: 'Modelo',
          gen_seteo: '<?php echo serialize($gen_seteo_listarModelos) ?>',
          gen_opcion: ''
        },
        success: function success(data, status) {        
          $("#modal-bodi-list").html(data);
        },
        error: function error(xhr, textStatus, errorThrown) {
            alert(errorThrown);
        }
      });
    }



    /*
      function crearLista(gen_modelo, gen_opcion) {
        $.ajax({
          url: '<?php echo env('PATH_PUBLIC')?>crearlista',
          type: 'POST',
          dataType: 'html',
          async: true,
          data:{
            _token: "{{ csrf_token() }}",
            gen_modelo: gen_modelo,
            gen_seteo: '<?php echo serialize($gen_seteo) ?>',
            gen_opcion: gen_opcion
          },
          success: function success(data, status) {        
            $("#modal-bodi-list").html(data);
          },
          error: function error(xhr, textStatus, errorThrown) {
              alert(errorThrown);
          }
        });
      }
    */
  </script>
<!-- FUNCIONES ABM Y CAMBIAR CLIENTE Y MODELOS -->      


<!-- APP app-forma-de-pago -->
  <script type="text/javascript">

    var VueFormGenerator = window.VueFormGenerator;

    var vm = new Vue({
      el: "#app-forma-de-pago",
      components: {
        "vue-form-generator": VueFormGenerator.component
      },

      methods: {
        prettyJSON: function (json) {
          if (json) {
            json = JSON.stringify(json, undefined, 4);
            json = json.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
            return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
              var cls = "number";
              if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                  cls = "key";
                } else {
                  cls = "string";
                }
              } else if (/true|false/.test(match)) {
                cls = "boolean";
              } else if (/null/.test(match)) {
                cls = "null";
              }
              return "<span class=\"" + cls + "\">" + match + "</span>";
            });
          }
        },
        onValidated(isValid, errors) {

          //isValid = true
          var_app = vm["_data"]["model"]
          var general_error = ''
          $("#fecha_de_vencimiento_de_la_solicitud_error").html('');
          $("#valor_total_error").html('');
          $("#general_error").html('');


          console.log('isValid: '+isValid)
          console.log(errors.length)
          if (var_app.fecha_de_cancelacion_del_anticipo == null && errors.length == 1) {
            isValid = true
          }
          console.log('isValid: '+isValid)

          var fecha_de_vencimiento_de_la_solicitud = $("#fecha_de_vencimiento_de_la_solicitud").val();
          var valor_total = Number($("#valor_total").val());
          var valor_total_calculado = Number($("#valor_total_calculado").val());
          //event.preventDefault();  

          
          if(valor_total < valor_total_calculado) {
            console.log('error 1')
            $("#valor_total_error").html('El valor ingresado es menor al valor calculado por m<sup>2</sup>, valor m&iacute;nimo: '+valor_total_calculado);
            isValid = false;
          }

          if (var_app.mostrar_anticipo) {

            if(var_app.anticipo > valor_total) {
              console.log('error 2')
              $("#valor_total_error").html('El valor del anticipo no puede ser mayor al valor total de la propiedad');
              isValid = false;
            }

            if(var_app.anticipo < var_app.anticipo_minimo) {
              console.log('error 4')
              general_error = general_error + 'El valor del anticipo debe ser mayor a: '+var_app.anticipo_minimo+' <br>';
              isValid = false;
            }

            if(var_app.fecha_de_cancelacion_del_anticipo == null || !var_app.fecha_de_cancelacion_del_anticipo) {
              console.log('error 4')
              general_error = general_error + 'Debe completar la fecha de cancelación del anticipo<br>';
              isValid = false;
            }
          }

          if(fecha_de_vencimiento_de_la_solicitud == '') {
            console.log('error 3')
            $("#fecha_de_vencimiento_de_la_solicitud_error").html('Este campo es obligatorio');
            isValid = false;
          }

          if(general_error != '') {
            $("#general_error").html(general_error);
          }

          if (!isValid) {
              event.preventDefault();  
          }     
   
        }
      },

      data: {
        model: {
          anticipo: <?php echo $anticipo ?>,
          anticipo_minimo: <?php echo $parametro_anticipo_minimo ?>,
          observaciones: '<?php echo $Solicitud->observaciones ?>',
          sino_contado: '<?php echo $Solicitud->sino_contado ?>',
          cuotas_anticipo: <?php echo $cuotas_anticipo ?>,
          mostrar_anticipo : false,
          lista_de_precio_id: <?php echo $Solicitud->lista_de_precio_id ?>,
          vendedor_id: <?php echo $vendedor_id ?>,
          forma_de_pago_id: <?php echo $Solicitud->Lista_de_precio->Forma_de_pago->id ?>,
          fecha_de_cancelacion_del_anticipo: <?php echo $fecha_de_cancelacion_del_anticipo ?>
          
        },
        schema: {
          fields: [

            {
              type: "selectEx",
              label: "Forma de Pago",
              model: "lista_de_precio_id",
              id: "lista_de_precio_id",
              required: true,
              disabled: false,
              inputName: "lista_de_precio_id",
              multi: "true",
              multiSelect: false,
              multiSelect: false,
              selectOptions: { 
                liveSearch: false, 
                size: 'auto' 
              },
              values: function() { 
                return [ 
                  <?php 
                  echo $valoresSchemaVFG_lista_de_precios;
                  ?>            
                  ] 
              },
              validator: VueFormGenerator.validators.required,
              onChanged(model, schema, event) {
                console.log('"'+$('[name="lista_de_precio_id"]').val()+'"');
                $.ajax({
                  url: '<?php echo env('PATH_PUBLIC')?>traerValoresPrecio',
                  type: 'POST',
                  dataType: 'html',
                  async: true,
                  data:{
                    _token: "{{ csrf_token() }}",
                    lista_de_precio_id: Number($('[name="lista_de_precio_id"]').val()),
                    solicitud_id: <?php echo $Solicitud->id ?>
                  },
                  success: function success(data, status) {     
                    var resultado = data;
                    var array_resultado = resultado.split('|');
                    valor_total = Number(array_resultado[0]);
                    model.forma_de_pago_id = Number(array_resultado[1]);

                    $("#valor_total").val(valor_total);
                    $("#valor_total_calculado").val(valor_total);
                  },
                  error: function error(xhr, textStatus, errorThrown) {
                      alert(errorThrown);
                  }
                });


              },
            },

            {
              type: "input",       
              inputType: "number",     
              model: "anticipo",    
              label: "Anticipo",    
              required: true,    
              inputName: "anticipo",
              id: "anticipo",
              min: <?php echo $parametro_anticipo_minimo ?>,
              step: 1,
              visible(model) {
                    var mostrar;
                    var forma_de_pago_id = model.forma_de_pago_id;
                    if (forma_de_pago_id == 1 || forma_de_pago_id == 3) {
                        model.mostrar_anticipo = false;
                        model.anticipo = 0;
                        $(".field-dateTimePicker").css("display", 'none');
                    }
                    if (forma_de_pago_id == 2 || forma_de_pago_id == 4) {
                        model.mostrar_anticipo = true;
                        $(".field-dateTimePicker").css("display", '');
                    }
                    return model.mostrar_anticipo;
                },
              validator: VueFormGenerator.validators.required
            },

            {
              type: "dateTimePicker",
              label: "Fecha de cancelacion del anticipo",
              model: "fecha_de_cancelacion_del_anticipo",
              id: "fecha_de_cancelacion_del_anticipo",
              inputName: "fecha_de_cancelacion_del_anticipo",
              required: true,
              placeholder: "",
              min: moment("2019-01-01").toDate(),
              max: moment("2050-01-01").toDate(),
              validator: VueFormGenerator.validators.date,

              dateTimePickerOptions: {
                  format: "DD/MM/YYYY"
              },            

              onChanged: function(model, newVal, oldVal, field) {
                  model.age = moment().year() - moment(newVal).year();
              }

            },
            {
              type: "switch", 
              model: "sino_contado",     
              label: "Contado",   
              id: "sino_contado_switch",  
              inputName: "sino_contado_switch",          
              textOn: "SI", textOff: "NO", valueOn: "SI", valueOff: "NO",
              visible(model) {
                    if (model.anticipo > 0) {
                        mostrar = true;
                    }
                    else {
                        mostrar = false;
                    }
                    return mostrar;
              },
            },
            {
              type: "input", 
              inputType: "hidden", 
              model: "sino_contado",
              inputName: "sino_contado",          
              textOn: "SI", textOff: "NO", valueOn: "SI", valueOff: "NO",
              visible(model) {
                    if (model.anticipo > 0) {
                        mostrar = true;
                    }
                    else {
                        mostrar = false;
                    }
                    return mostrar;
                },
            },
            {
              type: "input",       
              inputType: "number",     
              model: "cuotas_anticipo",    
              label: "Cantidad de Cuotas",    
              required: true,    
              inputName: "cuotas_anticipo",
              min: 0,
              id: "cuotas_anticipo",
              visible(model) {
                  if (model.sino_contado == 'SI' || model.sino_contado == '' || model.anticipo <= 0) {
                      mostrar = false;
                  }
                  else {
                      mostrar = true;
                  }
                  return mostrar;
              },
              onChanged(model, schema, event) {
                if (model.cuotas_anticipo > 0) {
                  var monto_cuota_anticipo = Number(model.anticipo/model.cuotas_anticipo)
                  if(!$("#monto_cuota_anticipo").length) {
                    $("#cuotas_anticipo").after('<div id="monto_cuota_anticipo"></div>')
                  }            
                  $("#monto_cuota_anticipo").html("<p>Valor de la cuota: "+monto_cuota_anticipo.toFixed(2)+"</p>")
                }
                else {
                   $("#monto_cuota_anticipo").html("")
                }
              },
              validator: VueFormGenerator.validators.required
            },

            {
              type: "selectEx",
              label: "Vendedor",
              model: "vendedor_id",
              id: "vendedor_id",
              required: true,
              disabled: false,
              inputName: "vendedor_id",
              multi: "true",
              multiSelect: false,
              multiSelect: false,
              selectOptions: { 
                liveSearch: false, 
                size: 'auto' 
              },
              values: function() { 
                return [ 
                  <?php 
                  echo $valoresSchemaVFG_vendedores;
                  ?>            
                  ] 
              },
              validator: VueFormGenerator.validators.required,
            },

            {         
              type: "textArea",       
              model: "observaciones",  
              id: "observaciones",  
              label: "Observaciones",   
              inputName: "observaciones", 
              required: false,    
              hint: "Max 250 caracteres",
              max: 250,
              placeholder: "",
              required: false,    
              rows: 4,
              validator: VueFormGenerator.validators.string
            },  

            {
              type: "submit",
              label: "",
              buttonText: "Continuar",
              validateBeforeSubmit: true
            }
          ]
        },


        formOptions: {
          validateAfterLoad: false,
          validateAfterChanged: false
        }
      }
    });
  </script>
<!-- FIN APP app-forma-de-pago -->



<!-- INICIO APP app-contrato -->
  <script type="text/javascript">
    const config = {
      locale: 'es', 
    };
    //moment.locale('es');
    //console.log(moment());
    Vue.use(VeeValidate, config);

    var app = new Vue({
      el: '#app-contrato',

      data: {
        fecha_de_contrato: <?php echo $fecha_de_contrato ?>,
        fecha_de_contrato_modal: <?php echo $fecha_de_contrato_modal ?>,
        a_distribuir: <?php echo $a_distribuir ?>,
        cant_cuotas_contrato: <?php echo $max_cant_cuotas_contrato ?>,
        cant_cuotas_calculadas: null,
        primer_vencimiento: moment().format('DD/MM/YYYY'),
        cant_periodo: 1,
        periodo: 'M',
        mostrar_cuotas: <?php echo $mostrar_cuotas ?>,
        mostrar_botonera_contrato: <?php echo $mostrar_botonera_contrato ?>,
        mostrar_btn_guardar_cuotas: false,
        visibility: 'visibility',
        max_cant_cuotas_contrato: <?php echo $max_cant_cuotas_contrato ?>,
        mostrar_calcular_cuotas_contrato: <?php echo $mostrar_calcular_cuotas_contrato ?>,
        habilitar_imprimir_contrato: <?php echo $habilitar_imprimir_contrato ?>,
        observaciones_contrato: '<?php echo $Solicitud->observaciones_contrato ?>',
        observaciones_contrato_modal: '<?php echo $Solicitud->observaciones_contrato ?>',
        pagado: '<?php echo $Solicitud->pagado ?>',
        pagado_modal: '<?php echo $Solicitud->pagado ?>',
        mostrar_garantes: false,
        mensaje_error: ''
      },

      methods: {
        calcular_cuotas_contrato: function () {

          this.cuotas_contrato = []
          this.cant_cuotas_calculadas = this.cant_cuotas_contrato
          importe_de_cuota = this.a_distribuir/this.cant_cuotas_contrato
          porcentaje_de_cuota = 100/this.cant_cuotas_contrato
          prox_vencimiento = moment(this.primer_vencimiento, "DD/MM/YYYY")
          //console.log(this.primer_vencimiento);

          this.mensaje_error = ''
          if (this.cant_cuotas_contrato > this.max_cant_cuotas_contrato) {
            this.mensaje_error = 'La cantidad de cuotas no puede ser mayor a '+this.max_cant_cuotas_contrato
          }

          if (this.fecha_de_contrato_modal > this.primer_vencimiento) {
            this.mensaje_error = 'La fecha del primer vencimiento('+this.primer_vencimiento+') no puede ser menor a la fecha de firma del contrato ('+this.fecha_de_contrato_modal+')'
          }
          else {
            console.log('La fecha del primer vencimiento('+this.primer_vencimiento+') no puede ser menor a la fecha de firma del contrato ('+this.fecha_de_contrato_modal+')')
          }

          if (this.mensaje_error == '') {
            this.mostrar_cuotas = true
            this.mostrar_btn_guardar_cuotas = true
            var table = $('#table_cuotas').DataTable()
            table.rows().remove()
            td_fields = ''
            for (var i = 1, cant = this.cant_cuotas_contrato; i <= cant; i++) {
                //console.log(moment(prox_vencimiento).format('DD/MM/YYYY'));         

                prox_vencimiento = prox_vencimiento.add(this.cant_periodo, this.periodo);

                td_importe_de_cuota = this.formatoMoneda_m(importe_de_cuota)
                td_porcentaje_de_cuota = this.formatoMoneda_m(porcentaje_de_cuota)
                td_prox_vencimiento = prox_vencimiento.format('DD/MM/YYYY')

                td_fields = td_fields+i+'#'+importe_de_cuota.toFixed(2)+'#'+porcentaje_de_cuota.toFixed(2)+'#'+prox_vencimiento.format('YYYY-MM-DD')+'|';

                table.row.add( [i, td_importe_de_cuota, td_porcentaje_de_cuota, td_prox_vencimiento ] ).draw()
              }   
              
              $('#td_fields').val(td_fields)
              //console.log( $('#table_cuotas').DataTable().ajax.reload() );
              
            }            
        },
        formatoMoneda_m: function (value) {
          let val = (value/1).toFixed(2).replace('.', ',')
          return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        guardar_fecha_de_contrato: function (solicitud_id) {
              this.habilitar_imprimir_contrato = true
              this.fecha_de_contrato =  this.fecha_de_contrato_modal
              this.observaciones_contrato = this.observaciones_contrato_modal
              this.pagado = this.pagado_modal

          $.ajax({
            url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/guardar-fecha-de-contrato',
            type: 'POST',
            dataType: 'html',
            async: true,
            data:{
              _token: "{{ csrf_token() }}",
              solicitud_id: solicitud_id,
              fecha_de_contrato: $("#fecha_de_contrato").val(),
              observaciones_contrato: $("#observaciones_contrato").val(),
              pagado: $("#pagado").val(),
            },
            success: function success(data, status) {        
              $('#modal-solicitud-print-contrato').modal('hide')
              if ($('#sino_imprimir_contrato').is(':checked')) {
                window.open('<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/imprimir-contrato/<?php echo $Solicitud->id ?>', '_blank')
              }
            },
            error: function error(xhr, textStatus, errorThrown) {
                alert(errorThrown);
            }
          });



        },

        <?php 
        $gen_seteo_listarModelos = array(
          'gen_url_siguiente' => env('PATH_PUBLIC').'Solicitudes/solicitud/ver/'.$Solicitud->id, 
          'gen_permisos' => ['C', 'R', 'U', 'D'],
          //'acciones_extra' => array('Seleccionar,fa fa-hand-pointer-o,Solicitudes/solicitud/cambiar-modelo/'.$Solicitud->id),
          'filtros_por_campo' => array('solicitud_id' => $Solicitud->id),
          'filtro_where' => ['solicitud_id', '=', $Solicitud->id],
          'gen_campos_a_ocultar' => 'solicitud_id',
          'mostrar_titulo' => 'NO',
          'titulo' => '',
          'tabla_condensada' => 'SI',
          'table' => [
            'searching' => 'false',
            'paging' => 'false',
            'pageLength' => 50
            ]
        );
        ?>

        traer_garantes: function () {

          $.ajax({
            url: '<?php echo env('PATH_PUBLIC')?>crearlista',
            type: 'POST',
            dataType: 'html',
            async: true,
            data:{
              _token: "{{ csrf_token() }}",
              gen_modelo: 'Garante',
              gen_seteo: '<?php echo serialize($gen_seteo_listarModelos) ?>',
              gen_opcion: ''
            },
            success: function success(data, status) { 
              $("#body_garantes").html(data);
            },
            error: function error(xhr, textStatus, errorThrown) {
                alert(errorThrown);
            }
          });
        },
        
        func_garantes: function () {
          if (this.mostrar_garantes) {
            this.mostrar_garantes = false
          }
          else {
            this.mostrar_garantes = true
            this.traer_garantes()
          }
          
        },

        
          
      },

      filters: {
        formatoMoneda: function (value) {
          let val = (value/1).toFixed(2).replace('.', ',')
          return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }
      }

    })
  </script>
<!-- FIN APP app-contrato -->

<!-- FUNCIONES APROBACIONES Y REVISION -->
  <script type="text/javascript">
    function aprobacionAdministracion(estado) {

      if (estado) {
        sino_aprobado_administracion = 'SI';
        $("#observaciones_aprobado_administracion").attr('class', 'form-control oculto');
        $("#observaciones_aprobado_administracion").val('');
      }
      else {
        sino_aprobado_administracion = 'NO';
        $("#observaciones_aprobado_administracion").attr('class', 'form-control visible');
      }

      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/aprobacion-administracion/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          sino_aprobado_administracion: sino_aprobado_administracion
        },
        success: function success(data, status) {    

          if (data == 'SI') {
            var txt_sino_aprobado_administracion = 'Aprobado';
            $("#box-footer-solicitud-administracion").attr('class', 'box-footer bg-olive');
          }
          else {
              var txt_sino_aprobado_administracion = 'Desaprobado';
            $("#box-footer-solicitud-administracion").attr('class', 'box-footer bg-red');
          }

          $("#estado_sino_aprobado_administracion").html(txt_sino_aprobado_administracion);

          
        },
        error: function error(xhr, textStatus, errorThrown) {
            alert(errorThrown);
        }
      });
    }


    function aprobacionGarantes(estado) {

      if (estado) {
        sino_aprobado_garantes = 'SI';
        $("#observaciones_aprobado_garantes").attr('class', 'form-control oculto');
        $("#observaciones_aprobado_garantes").val('');
      }
      else {
        sino_aprobado_garantes = 'NO';
        $("#observaciones_aprobado_garantes").attr('class', 'form-control visible');
      }

      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/aprobacion-garantes/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          sino_aprobado_garantes: sino_aprobado_garantes
        },
        success: function success(data, status) {    

          if (data == 'SI') {
            var txt_sino_aprobado_garantes = 'Aprobado';
            $("#box-footer-solicitud-garantes").attr('class', 'box-footer bg-olive');
          }
          else {
              var txt_sino_aprobado_garantes = 'Desaprobado';
            $("#box-footer-solicitud-garantes").attr('class', 'box-footer bg-red');
          }

          $("#estado_sino_aprobado_garantes").html(txt_sino_aprobado_garantes);

          
        },
        error: function error(xhr, textStatus, errorThrown) {
            alert(errorThrown);
        }
      });
    }

    function aprobacionfinalizada(estado) {

      if (estado) {
        sino_aprobado_finalizada = 'SI';
        //$("#observaciones_aprobado_finalizada").attr('class', 'form-control oculto');
        $("#observaciones_aprobado_finalizada").val('');
      }
      else {
        sino_aprobado_finalizada = 'NO';
        $("#observaciones_aprobado_finalizada").attr('class', 'form-control visible');
      }

      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/aprobacion-finalizada/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          sino_aprobado_finalizada: sino_aprobado_finalizada
        },
        success: function success(data, status) {    

          if (data == 'SI') {
            var txt_sino_aprobado_finalizada = 'SI';
            $("#box-footer-solicitud-finalizada").attr('class', 'box-footer bg-olive');
          }
          else {
              var txt_sino_aprobado_finalizada = 'NO';
            $("#box-footer-solicitud-finalizada").attr('class', 'box-footer bg-grey');
          }

          $("#estado_sino_aprobado_finalizada").html(txt_sino_aprobado_finalizada);

          
        },
        error: function error(xhr, textStatus, errorThrown) {
            alert(errorThrown);
        }
      });
    }

    function aprobacionSolicitarRevision(estado) {

      if (estado) {
        sino_aprobado_solicitar_revision = 'SI';
        //$("#observaciones_aprobado_solicitar_revision").attr('class', 'form-control visible');
        //$("#observaciones_aprobado_solicitar_revision").val('');
      }
      else {
        sino_aprobado_solicitar_revision = 'NO';
        //$("#observaciones_aprobado_solicitar_revision").attr('class', 'form-control oculto');
      }

      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/aprobacion-solicitar-revision/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          sino_aprobado_solicitar_revision: sino_aprobado_solicitar_revision
        },
        success: function success(data, status) {    

          if (data == 'SI') {
            var txt_sino_aprobado_solicitar_revision = 'Solicitada';
            $("#box-footer-solicitud-solicitar_revision").attr('class', 'box-footer bg-yellow');
          }
          else {
              var txt_sino_aprobado_solicitar_revision = 'Atendida';
            $("#box-footer-solicitud-solicitar_revision").attr('class', 'box-footer bg-blue');
          }

          $("#estado_sino_aprobado_solicitar_revision").html(txt_sino_aprobado_solicitar_revision);

          
        },
        error: function error(xhr, textStatus, errorThrown) {
            alert(errorThrown);
        }
      });
    }

    function guardarObsAdm(observaciones_aprobado_administracion) {
      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/guardar-obs-adm/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          observaciones_aprobado_administracion: observaciones_aprobado_administracion
        },
        success: function success(data, status) {  
          //$("#observaciones_aprobado_administracion_mensaje").html('guardado');     
        }
      });  
    }

    function guardarObsGar(observaciones_aprobado_garantes) {
      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/guardar-obs-gar/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          observaciones_aprobado_garantes: observaciones_aprobado_garantes
        },
        success: function success(data, status) {  
          //$("#observaciones_aprobado_garantes_mensaje").html('guardado');        
        }
      });  
    }


    function guardarObsFin(observaciones_aprobado_finalizada) {
      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/guardar-obs-fin/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          observaciones_aprobado_finalizada: observaciones_aprobado_finalizada
        },
        success: function success(data, status) {  
          //$("#observaciones_aprobado_finalizada_mensaje").html('guardado');        
        }
      });  
    }

    function guardarObsSolRev(observaciones_aprobado_solicitar_revision) {
      $.ajax({
        url: '<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/guardar-obs-sol-rev/<?php echo $Solicitud->id ?>',
        type: 'POST',
        dataType: 'html',
        async: true,
        data:{
          _token: "{{ csrf_token() }}",
          observaciones_aprobado_solicitar_revision: observaciones_aprobado_solicitar_revision
        },
        success: function success(data, status) {  
          //$("#observaciones_aprobado_garantes_mensaje").html('guardado');        
        }
      });  
    }

  </script>
<!-- FUNCIONES APROBACIONES Y REVISION -->

<?php 
$gen_seteo['gen_campos_a_ocultar'] = ['id'];
?>

<script type="text/javascript">

$( document ).ready(function() {

  <?php if ($Solicitud->Lista_de_precio->Forma_de_pago->id == '1' or $Solicitud->Lista_de_precio->Forma_de_pago->id == '3') { ?>
  $(".field-dateTimePicker").css("display", 'none');
  <?php } ?>

  $(".datetimepicker_class").datepicker({
      format: "dd/mm/yyyy"
  });


  <?php if ($modificar_contrato == 'S') { ?>
    
    $("#fecha_de_contrato").on('change.dp', function () {
        app["_data"]["fecha_de_contrato_modal"] = moment($("#fecha_de_contrato").val(), "DD/MM/YYYY").format("DD/MM/YYYY")
    });
    
    $("#primer_vencimiento").on('change.dp', function () {
        app["_data"]["primer_vencimiento"] = moment($("#primer_vencimiento").val(), "DD/MM/YYYY").format("DD/MM/YYYY")
    });
  <?php } ?>

/*
  if ( $("#fecha_de_contrato_modal").length ) {
    $("#fecha_de_contrato_modal").val(<?php echo $fecha_de_contrato; ?>)
    
  }
*/
});


</script>

<script>
  $(function () {
    $('#table_cuotas').DataTable({
      'language': {
        'autoWidth': true,
            'lengthMenu': 'Mostrar _MENU_ Registros por pagina',
            'search': 'Buscar',
            'zeroRecords': 'No hay resultados para la busqueda',
            'info': 'Mostrando Pagina _PAGE_ de _PAGES_',
            'infoEmpty': 'No hay registros',
            'paginate': {
                'first':      'Primero',
                'last':       'Ultimo',
                'next':       'Siguiente',
                'previous':   'Anterior'
            },
            'infoFiltered': '(filtrado en _MAX_ registros totales)'
        },
        'order': [[ 1, 'asc' ]],
        'columnDefs': [{ "width": "100px", "targets": 0 }], 
    })
  })
</script>
@endsection

