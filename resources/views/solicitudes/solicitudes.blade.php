@extends('layouts.backend')

@section('contenido')

<?php
$rol_de_usuario_id = Auth::user()->rol_de_usuario_id;

use \App\Http\Controllers\GenericController; 
$gCont = new GenericController;

?>
    
    <br>

      <div class="col-xs-12" style="overflow: auto">

        <div class="box" style="display: inline-table;">
          <div class="box-header">
            <h3 class="box-title"><?php echo $titulo; ?></h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="table" class="table " style="max-width: 500px;">
              <thead>
              <tr>
                  <th>Acci&oacute;n</th>
                  <th>Sucursal</th>
                  <th>Solicitud</th>
                  <th>C&oacute;digo</th>
                  <th>Cliente</th>
                  <?php if ($estado_letra == 'f') { ?>
                  <th>Finalizada</th>
                  <?php } ?>
                  <th>Estado</th>
              </tr>
              </thead>
              <tbody>

                <?php 
                if ($Solicitudes <> null) { 
                  foreach ($Solicitudes as $solicitud) { 
                    $sucursal = '';
                    if ($solicitud->sucursal_id <> null) {
                      $sucursal = $solicitud->Sucursal->sucursal;
                    }

                ?>

                  <tr>
                      <td>
                        <div class="btn-group">
                          <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/ver/<?php echo $solicitud['id']; ?>">
                          <button type="button" class="btn btn-info" alt="editar" title="editar"><i class="fa fa-pencil"></i></button>
                        </a>
                        </div>
                      </td>

                      <td><?php echo $sucursal; ?></td>
                      <td><?php echo $solicitud->id; ?></td>
                      <td><?php echo $solicitud->Cliente->id; ?></td>
                      <td><?php echo $solicitud->Cliente->apellido; ?> <?php echo $solicitud->Cliente->nombre; ?></td>  
                      <?php if ($estado_letra == 'f') { ?>  
                        <td><?php echo $gCont->FormatoFecha($solicitud->fecha_de_finalizacion); ?></td>
                      <?php } ?>
                      <?php

                      $estado = '';
                      $class_estado = '';
                      if ($solicitud->sino_aprobado_finalizada == 'SI') {
                        $estado = 'Finalizada';
                        $class_estado = 'bg-light-blue';
                      }
                      if ($solicitud->sino_aprobado_administracion == 'SI' and ($solicitud->sino_aprobado_finalizada == '' or $solicitud->sino_aprobado_finalizada == 'NO') ) {
                        $estado = 'Aprobada';
                        $class_estado = 'bg-green';
                      }
                      if ($rol_de_usuario_id < 3 ) {
                        if (($solicitud->sino_aprobado_administracion == 'NO' or $solicitud->sino_aprobado_garantes == 'NO') and ($solicitud->sino_aprobado_solicitar_revision == 'NO' or $solicitud->sino_aprobado_solicitar_revision == '')) {
                          $estado = 'Desaprobada';
                          $class_estado = 'bg-red';
                        }
                      }
                      else {
                        if ((($solicitud->sino_aprobado_administracion == 'NO' or $solicitud->sino_aprobado_garantes == 'NO') and $solicitud->sino_aprobado_solicitar_revision == 'NO') or $solicitud->sino_aprobado_contrato == 'NO') {
                          $estado = 'Desaprobada';
                          $class_estado = 'bg-red';
                        }
                      }
                      if ($rol_de_usuario_id < 3 ) {
                        if ($solicitud->sino_aprobado_administracion == '') {
                          $estado = 'Pendiente';
                          $class_estado = 'bg-yellow';
                        }
                      }
                      else {
                        if (($solicitud->sino_aprobado_administracion == '' and $solicitud->sino_aprobado_contrato == '') or ($solicitud->sino_aprobado_administracion == 'NO' and $solicitud->sino_aprobado_solicitar_revision == "SI" and $solicitud->sino_aprobado_contrato == '')) {
                          $estado = 'Pendiente';
                          $class_estado = 'bg-yellow';
                        }
                      }
                      if ($rol_de_usuario_id < 3 ) {
                        if ($solicitud->sino_aprobado_administracion == "NO" AND $solicitud->sino_aprobado_solicitar_revision == 'SI') {
                          $estado = 'Revisar';
                          $class_estado = 'bg-yellow';
                        }
                      }
                      else {
                        if ($solicitud->sino_aprobado_administracion == "NO" AND $solicitud->sino_aprobado_solicitar_revision == '') {
                          $estado = 'Revisar';
                          $class_estado = 'bg-yellow';
                        }
                      }
                      ?>                
                      <td><span class="badge <?php echo $class_estado; ?> datos-finales-asistente"><?php echo $estado; ?></span></td>
                  </tr>
                  <?php } ?>
                <?php } ?>
            </tbody>
            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>

      <div class="col-xs-3">
        <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/crear"><button type="button" class="btn btn-block btn-info col-xs-3"><i class="fa fa-plus"></i> Crear Solicitud</button></a>
      </div>

      <!-- DataTables -->

      <script>
        $(function () {
          $('#table').DataTable({
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
        

<script type="text/javascript">

$( document ).ready(function() {
  $("#table").css("overflow", 'auto');
});


</script>
          
          



@endsection
