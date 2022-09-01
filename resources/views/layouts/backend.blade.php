<?php 

$rol_de_usuario_id = Auth::user()->rol_de_usuario_id;

$permiso_todos = array(1,2,3);
$permiso_administrador = array(1);
$permiso_supervisor_gral = array(1,4);
$permiso_supervisor = array(1,3);
$permiso_vendedor = array(1,2,3);

$cant_solicitudes = App::make('App\Http\Controllers\HomeController')->notificaciones();

if (Auth::user()->img_avatar <> '') {
  $img_avatar = Auth::user()->img_avatar;
}
else {
  $img_avatar = env('PATH_PUBLIC').'img/avatar-sin-imagen.png';
}

use \App\Http\Controllers\SolicitudController; 
$SolicitudController = new SolicitudController;

use \App\Parametro; 

$habilitar_abm_tb_ref = Parametro::find(3);
if ($habilitar_abm_tb_ref->valor == 'SI') {
    $opcion_para_tb_ref = 0;
    $opcion_td = 5;
    $opcion_si = 7;
}
else {
    $opcion_para_tb_ref = 9;
    $opcion_td = 9;
    $opcion_si = 9;
}




?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Tecnohouse | Solicitudes de Fabricaci&oacute;n</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>dist/css/skins/_all-skins.min.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/morris.js/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">


  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <!-- jQuery 3 -->
  <script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery/dist/jquery.min.js"></script>

  <link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>css/generic.css">

</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo env('PATH_PUBLIC')?>" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b><i class="fa fa-home"></i></b></span>
      <!-- logo for regular state and mobile devices -->
      <i class="fa fa-home"></i> Tecnohouse</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Notifications: style can be found in dropdown.less -->
          <li class="dropdown notifications-menu">
            <a href="<?php echo env('PATH_PUBLIC')?>">
              <i class="fa fa-bell-o"></i>
              <span class="label label-danger"><?php echo $cant_solicitudes ?></span>
            </a>
          </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="{{{ $img_avatar }}}" class="user-image" alt="User Image">
              <span class="hidden-xs">{{{ Auth::user()->name }}}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="{{{ $img_avatar }}}" class="img-circle" alt="User Image">

                <p>
                  {{{ Auth::user()->name }}} <br> {{{ Auth::user()->email }}}
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Mi Cuenta</a>
                </div>
                <div class="pull-right">
                  <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-default btn-flat">Salir</a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{{ $img_avatar }}}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{{ Auth::user()->name }}}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU</li>

        <?php if (in_array($rol_de_usuario_id, $permiso_todos)) { ?>
          <?php
          $pendientes = $SolicitudController->traerSolicitudes('p');
          $cant_pendientes = count($pendientes['Solicitudes']);

          $revisar = $SolicitudController->traerSolicitudes('r');
          $cant_revisar = count($revisar['Solicitudes']);

          $aprobadas = $SolicitudController->traerSolicitudes('a');
          $cant_aprobadas = count($aprobadas['Solicitudes']);

          $desaprobadas = $SolicitudController->traerSolicitudes('d');
          $cant_desaprobadas = count($desaprobadas['Solicitudes']);
          ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-file-text-o"></i>
            <span>Solicitudes</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/crear"><i class="fa fa-file-text-o"></i> <span>Agregar Solicitud</span></a></li> 
            <li><a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/list/p"><i class="fa fa-file-text-o"></i> <span>Pendientes 
              <?php if ($cant_pendientes > 0) { ?>
                <span data-toggle="tooltip" title="" class="badge bg-yellow" data-original-title="3 New Messages"><?php echo $cant_pendientes ?></span>
              <?php } ?>
              </span></a></li> 
            <li><a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/list/r"><i class="fa fa-file-text-o"></i> <span>Revisar 
              <?php if ($cant_revisar > 0) { ?>
                <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="3 New Messages"><?php echo $cant_revisar ?></span>
              <?php } ?>
              </span></a>
            </li>
            <li><a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/list/a"><i class="fa fa-file-text-o"></i> <span>Aprobadas
              <?php if ($cant_aprobadas > 0) { ?>
                <span data-toggle="tooltip" title="" class="badge bg-blue" data-original-title="3 New Messages"><?php echo $cant_aprobadas ?></span>
              <?php } ?>
              </span></a></li> 
            <li><a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/list/d"><i class="fa fa-file-text-o"></i> <span>Desaprobadas
              <?php if ($cant_desaprobadas > 0) { ?>
                <span data-toggle="tooltip" title="" class="badge bg-red" data-original-title="3 New Messages"><?php echo $cant_desaprobadas ?></span>
              <?php } ?>
              </span></a></li>
            <li><a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/list/f"><i class="fa fa-file-text-o"></i> <span>Finalizadas</span></a></li> 
            <li><a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/list/t"><i class="fa fa-file-text-o"></i> <span>Todas</span></a></li> 
          </ul>
        </li>
        <?php } ?>

        <?php if (in_array($rol_de_usuario_id, $permiso_supervisor_gral)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-home"></i>
            <span>Modelos</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/Modelo/3"><i class="fa fa-home"></i> <span>Modelos</span></a></li>
          <li><a href="<?php echo env('PATH_PUBLIC')?>list/Imagen_de_modelo/0"><i class="fa fa-photo"></i> <span>Imagenes de modelos</span></a></li>
          <li><a href="<?php echo env('PATH_PUBLIC')?>list/Articulo/0"><i class="fa fa-object-ungroup"></i> <span>Articulos</span></a></li>
          </ul>
        </li>
        <?php } ?>
        <?php if (in_array($rol_de_usuario_id, $permiso_todos)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-home"></i>
            <span>Clientes</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">                      
            <li><a href="<?php echo env('PATH_PUBLIC')?>clientes-por-sucursal"><i class="fa fa-users"></i> <span>Clientes de Sucursal</span></a></li>
            <?php if (in_array($rol_de_usuario_id, $permiso_supervisor_gral)) { ?>
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/Cliente/1"><i class="fa fa-users"></i> <span>Todos los Clientes</span></a></li>
            <li><a href="<?php echo env('PATH_PUBLIC')?>importar/clientes"><i class="fa fa-users"></i> <span>Importar/Exportar</span></a></li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>
        <?php if (in_array($rol_de_usuario_id, $permiso_supervisor_gral)) { ?>
          <li><a href="<?php echo env('PATH_PUBLIC')?>list/Lista_de_precio/8"><i class="fa fa-list"></i> <span>Listas de precio</span></a></li>
        <?php } ?>

        <?php if (in_array($rol_de_usuario_id, $permiso_supervisor_gral)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-map-o"></i>
            <span>Regi&oacute;n</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/Localidad/<?php echo $opcion_para_tb_ref ?>"><i class="fa fa-circle-thin"></i> <span>Localidades</span></a></li>  
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/Provincia/<?php echo $opcion_para_tb_ref ?>"><i class="fa fa-circle-thin"></i> <span>Provincias</span></a></li>    
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/Pais/<?php echo $opcion_para_tb_ref ?>"><i class="fa fa-circle-thin"></i> <span>Paises</span></a></li>      
          <li><a href="<?php echo env('PATH_PUBLIC')?>list/Zona/<?php echo $opcion_para_tb_ref ?>"><i class="fa fa-map-marker"></i> <span>Zonas</span></a></li>
          <li><a href="<?php echo env('PATH_PUBLIC')?>list/Zona_local/0"><i class="fa fa-map-marker"></i> <span>Zonas Locales</span></a></li>
          <li><a href="<?php echo env('PATH_PUBLIC')?>list/Sucursal/<?php echo $opcion_para_tb_ref ?>"><i class="fa fa-building-o"></i> <span>Sucursales</span></a></li>
          </ul>
        </li>
        <?php } ?>

        <?php if (in_array($rol_de_usuario_id, $permiso_supervisor_gral)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-database"></i>
            <span>Referencias</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/Moneda/<?php echo $opcion_para_tb_ref ?>"><i class="fa fa-dollar"></i> Monedas</a></li>
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/Forma_de_pago/0"><i class="fa fa-money"></i> Formas de Pago</a></li> 
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/Tipo_de_documento/<?php echo $opcion_td ?>"><i class="fa fa-newspaper-o"></i> Tipos de Documentos</a></li>
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/Situacion_de_iva/<?php echo $opcion_si ?>"><i class="fa fa-newspaper-o"></i> Situaciones de IVA</a></li>
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/Vendedor/0"><i class="fa fa-key"></i> Vendedores</a></li>       
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/User/2"><i class="fa fa-key"></i> Usuarios</a></li>       
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/Rol_de_usuario/0"><i class="fa fa-key"></i> Roles de Usuarios</a></li>       
          </ul>
        </li>
        <?php } ?>


        <?php if (in_array($rol_de_usuario_id, $permiso_supervisor_gral)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-print"></i>
            <span>Reportes</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/Reporte/0"><i class="fa fa-print"></i> Modelos de Reportes</a></li>
          </ul>
        </li>
        <?php } ?>


        <?php if (in_array($rol_de_usuario_id, $permiso_administrador)) { ?>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-edit"></i> <span>Parametrizaci&oacute;n</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/Empresa/0"><i class="fa fa-industry"></i> Empresas</a></li>
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/Parametro/0"><i class="fa fa-check-square-o"></i> Parametros</a></li>
            <li><a href="<?php echo env('PATH_PUBLIC')?>list/Opcion/0"><i class="fa fa-circle"></i> Opciones</a></li>
          </ul>
        </li>
        <?php } ?>

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <?php 

    if(isset($mensaje) or $errors->any()) {

      $mensaje_class = 'alert-success';
      $mensaje_icon = 'fa-check';
      $mensaje_detalle = '';

      if ($errors->any()) {
        if($errors->first()) {
          $mensaje_icon = 'fa fa-ban';
          $mensaje_class = 'alert-warning';
          foreach ($errors->all() as $error) {
            $mensaje_detalle .= "<p>$error</p>";
          }          
        }
        else {
          $errors_array = $errors->all();
          $mensaje_detalle = $errors_array[1];
        }
      }
      else {
        if (isset($mensaje['class'])) {
          $mensaje_class = $mensaje['class'];
        }

        if (isset($mensaje['detalle'])) {
          $mensaje_detalle = $mensaje['detalle'];
        }
        else {
          $mensaje_detalle = $mensaje;  
        }

        if (isset($mensaje['error']) and $mensaje['error']) {
          $mensaje_icon = 'fa fa-ban';
        }        
      }

    ?>
      <section class="content-header">
        <div class="row">    
          <div class="col-xs-12">
            <br>
            <div class="alert <?php echo $mensaje_class; ?> alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <h4><i class="icon fa <?php echo $mensaje_icon; ?>"></i> <?php echo $mensaje_detalle; ?></h4>  
            </div>
          </div>   
        </div>
      </section> 
    <?php } ?>


    @yield('contenido')
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.4.0
    </div>
    <strong>Copyright &copy; 2014-2016 <a href="https://adminlte.io">Almsaeed Studio</a>.</strong> All rights
    reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
      <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
      <!-- Home tab content -->
      <div class="tab-pane" id="control-sidebar-home-tab">
        <h3 class="control-sidebar-heading">Recent Activity</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-birthday-cake bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                <p>Will be 23 on April 24th</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-user bg-yellow"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

                <p>New phone +1(800)555-1234</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

                <p>nora@example.com</p>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <i class="menu-icon fa fa-file-code-o bg-green"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

                <p>Execution time 5 seconds</p>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

        <h3 class="control-sidebar-heading">Tasks Progress</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Custom Template Design
                <span class="label label-danger pull-right">70%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Update Resume
                <span class="label label-success pull-right">95%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-success" style="width: 95%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Laravel Integration
                <span class="label label-warning pull-right">50%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
              </div>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)">
              <h4 class="control-sidebar-subheading">
                Back End Framework
                <span class="label label-primary pull-right">68%</span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

      </div>
      <!-- /.tab-pane -->
      <!-- Stats tab content -->
      <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
      <!-- /.tab-pane -->
      <!-- Settings tab content -->
      <div class="tab-pane" id="control-sidebar-settings-tab">
        <form method="post">
          <h3 class="control-sidebar-heading">General Settings</h3>

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Report panel usage
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Some information about this general settings option
            </p>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Allow mail redirect
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Other sets of options are available
            </p>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Expose author name in posts
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Allow the user to show his name in blog posts
            </p>
          </div>
          <!-- /.form-group -->

          <h3 class="control-sidebar-heading">Chat Settings</h3>

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Show me as online
              <input type="checkbox" class="pull-right" checked>
            </label>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Turn off notifications
              <input type="checkbox" class="pull-right">
            </label>
          </div>
          <!-- /.form-group -->

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Delete chat history
              <a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
            </label>
          </div>
          <!-- /.form-group -->
        </form>
      </div>
      <!-- /.tab-pane -->
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/raphael/raphael.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="<?php echo env('PATH_PUBLIC')?>plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/moment/min/moment.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo env('PATH_PUBLIC')?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo env('PATH_PUBLIC')?>dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo env('PATH_PUBLIC')?>dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo env('PATH_PUBLIC')?>dist/js/demo.js"></script>
<!-- DataTables -->
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>


</body>
</html>
