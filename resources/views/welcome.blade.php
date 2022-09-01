<?php
$rol_de_usuario_id = Auth::user()->rol_de_usuario_id;
?>


@extends('layouts.backend')



@section('contenido')

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>dist/css/skins/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Inicio
        <small>Tecnohouse</small>
      </h1>
      <ol class="breadcrumb">
        <li class="active">Inicio</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <?php if ($rol_de_usuario_id <> 4) { ?>
          <div class="col-xs-12">

            <br>
            <!-- SOLICITUDES  -->
            <?php if (count($Solicitudes) > 0) { ?>
              <div class="col-xs-12">
                <div class="box">
                  <div class="box-header">
                    <h3 class="box-title">
                      <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="3 New Messages"><?php echo count($Solicitudes) ?></span>
                      <?php echo $titulo; ?></h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                      </div>

                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                    <table id="table" class="table table-bordered table-striped" style="max-width: 500px" >
                      <thead>
                      <tr>
                          <th>Acci&oacute;n</th>
                          <th>Sucursal</th>
                          <th>Solicitud</th>
                          <th>C&oacute;digo</th>
                          <th>Cliente</th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php foreach ($Solicitudes as $solicitud) { ?>
                      <tr>
                          <td>
                            <div class="btn-group">
                              <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/ver/<?php echo $solicitud['id']; ?>">
                              <button type="button" class="btn btn-info" alt="editar" title="editar"><i class="fa fa-pencil"></i></button>
                            </a>
                            </div>
                          </td>

                          <td>
                            <?php 
                            $sucursal = '';
                            if ($solicitud->sucursal_id <> null) {
                              $sucursal = $solicitud->Sucursal->sucursal;
                            }
                            echo $sucursal;
                            ?>                            
                          </td>                            
                          <td><?php echo $solicitud->id; ?></td>
                          <td><?php echo $solicitud->Cliente->id; ?></td>
                          <td><?php echo $solicitud->Cliente->apellido; ?> <?php echo $solicitud->Cliente->nombre; ?></td>
                      </tr>
                      <?php } ?>
                    </tbody>
                    </table>
                  </div>
                  <!-- /.box-body -->
                </div>
                <!-- /.box -->
              </div>
            <?php } ?>



            <!-- SOLICITUDES 2  -->
            <?php if (count($Solicitudes_2) > 0) { ?>
              <div class="col-xs-12">
                <div class="box">
                  <div class="box-header">
                    <h3 class="box-title">
                      <span data-toggle="tooltip" title="" class="badge bg-green" data-original-title="3 New Messages"><?php echo count($Solicitudes_2) ?></span>
                      <?php echo $titulo_2; ?></h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                      </div>

                  </div>
                  <!-- /.box-header -->
                  <div class="box-body">
                    <table id="table_2" class="table table-bordered table-striped" style="max-width: 500px" >
                      <thead>
                      <tr>
                          <th>Acci&oacute;n</th>
                          <th>Sucursal</th>
                          <th>Solicitud</th>
                          <th>C&oacute;digo</th>
                          <th>Cliente</th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php foreach ($Solicitudes_2 as $solicitud) { ?>
                      <tr>
                          <td>
                            <div class="btn-group">
                              <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/ver/<?php echo $solicitud['id']; ?>">
                              <button type="button" class="btn btn-info" alt="editar" title="editar"><i class="fa fa-pencil"></i></button>
                            </a>
                            </div>
                          </td>

                          <td>
                            <?php 
                            $sucursal = '';
                            if ($solicitud->sucursal_id <> null) {
                              $sucursal = $solicitud->Sucursal->sucursal;
                            }
                            echo $sucursal;
                            ?>                            
                          </td>                            
                          <td><?php echo $solicitud->id; ?></td>
                          <td><?php echo $solicitud->Cliente->id; ?></td>
                          <td><?php echo $solicitud->Cliente->apellido; ?> <?php echo $solicitud->Cliente->nombre; ?></td>
                      </tr>
                      <?php } ?>
                    </tbody>
                    </table>
                  </div>
                  <!-- /.box-body -->
                </div>
                <!-- /.box -->
              </div>
            <?php } ?>



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

              <script>
                $(function () {
                  $('#table_2').DataTable({
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
            <!-- SOLICITUDES  -->




            <!-- AUTORIZACIONES USUARIOS -->
            <?php if (isset($Autorizaciones) > 0) { ?>
              <div class="col-xs-12" style="margin-top: 40px;">
                <div class="box">
                  <div class="box-header">
                    <h3 class="box-title">Autorizaciones a nuevos usuarios</h3>

                      <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                      </div>

                  </div>
                  <!-- /.box-header -->
                  <div class="box-body" id="body-usuarios">

                  </div>
                  <!-- /.box-body -->
                </div>
                <!-- /.box -->
              </div>

              <!-- DataTables -->

              <?php 

                $gen_seteo = array(
                  'gen_url_siguiente' => 'back', 
                  'gen_permisos' => ['U', 'D'],
                  //'acciones_extra' => array('Seleccionar,fa fa-hand-pointer-o,Solicitudes/solicitud/cambiar-modelo/'.$Solicitud->id),
                  //'filtro_where' => ['rol_de_usuario_id', 'IS NULL', ''],
                  
                  'filtro_where' => [
                    ['rol_de_usuario_id', '=', '4'], 
                    //['sucursal_id', 'IS NULL', '']
                  ],
                  
                  //'filtro_where' => ['rol_de_usuario_id', 'IS NULL', ''],                  
                  'gen_campos_a_ocultar' => 'password|remember_token|sucursal_id|rol_de_usuario_id|img_avatar',
                  'no_mostrar_campos_abm' => 'password|remember_token|img_avatar',
                  'mostrar_titulo' => 'NO',
                  'titulo' => '',
                  'table' => [
                    'searching' => 'false',
                    'paging' => 'false'
                    ]
                );
              ?>
                <script type="text/javascript">


                    $.ajax({
                      url: '<?php echo env('PATH_PUBLIC')?>crearlista',
                      type: 'POST',
                      dataType: 'html',
                      async: true,
                      data:{
                        _token: "{{ csrf_token() }}",
                        gen_modelo: 'User',
                        gen_seteo: '<?php echo serialize($gen_seteo) ?>',
                        gen_opcion: ''
                      },
                      success: function success(data, status) { 
                        $("#body-usuarios").html(data);
                      },
                      error: function error(xhr, textStatus, errorThrown) {
                          alert(errorThrown);
                      }
                    });

                </script>
              <?php } ?>
            <!-- AUTORIZACIONES USUARIOS -->




          <?php } 
          else { ?>

              <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-warning"></i> Alerta!</h4>
                <p><strong>Su usuario a&uacute;n no ha sido autorizado. Debe solicitarlo a su supervisor.</strong></p>
              </div>

          <?php } ?>

        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->

<!-- DataTables -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>



@endsection
