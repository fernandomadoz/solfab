@extends('layouts.backend')

@section('contenido')

<?php 
if (!isset($paso)) {
  $paso = 1;
}

$steps = 4;
$width_step = 20; 
$width_progress_bar = $width_step*($paso-1);

if ($paso == 1) {
  $url = env('PATH_PUBLIC').'listar-clientes-para-seleccion';
  $titulo = 'Seleccione el Cliente';
}
if ($paso == 2) {
  $url = env('PATH_PUBLIC').'listar-modelos-para-seleccion/'.$solicitud_id;
  $titulo = 'Seleccione el Modelo';
}
if ($paso == 3) {
  $url = env('PATH_PUBLIC').'determinar-composicion-de-modelo-para-seleccion/'.$solicitud_id;
  $titulo = 'Determine los Componentes';
}
if ($paso == 4) {
  $titulo = 'Seleccione la Forma de Pago';
}

?>

<style>
.wrapper {
background-color: white !important;
}
</style>

<!-- vue.js -->
<script src="<?php echo env('PATH_PUBLIC')?>js/vue/vue.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>js/vee-validate/dist/vee-validate.js"></script>
<script src="<?php echo env('PATH_PUBLIC')?>js/vee-validate/dist/locale/es.js"></script>

<script src="https://cdn.jsdelivr.net/vue.resource/1.3.1/vue-resource.min.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo env('PATH_PUBLIC')?>js/bootstrap-select/css/bootstrap-select.min.css">
<script type="text/javascript" src="<?php echo env('PATH_PUBLIC')?>js/bootstrap-select/js/bootstrap-select.min.js"></script>

<!-- bootstrap slider -->
<link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>plugins/bootstrap-slider/slider.css">

<link rel="stylesheet" href="<?php echo env('PATH_PUBLIC')?>css/style.css">


<!-- Content Header (Page header) -->
<section class="content-header">
<h1>
  Nueva Soliciud
  <small>Asistente</small>
</h1>
<ol class="breadcrumb">
  <li><a href="<?php echo env('PATH_PUBLIC')?>"><i class="fa fa-home"></i> Home</a></li>
  <li><a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/list">Solicitudes</a></li>
  <li class="active">Nueva Solicitud</li>
</ol>
</section>

<!-- Main content -->
<section class="content">
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-header">
        <h3 class="box-title"><?php echo $titulo; ?></h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="row margin">
          <div class="slider slider-horizontal" id="green">
            <div class="slider-track">
              <div class="slider-selection" style="left: 0%; width: <?php echo $width_progress_bar; ?>%;"></div>
              <!--div class="slider-handle min-slider-handle round">0</div-->
              <?php 
              $left_step = 0;
              for($i=1; $i<=$steps; $i++) { 
                if ($i > 1) {
                  $left_step = $left_step+$width_step;  
                }
                
                if ($i <= $paso) {
                  $class_slider_gris = '';
                }
                else {
                  $class_slider_gris = 'slider-gris';
                }

              ?>
              <div class="slider-handle max-slider-handle round <?php echo $class_slider_gris; ?>" style="left: <?php echo $left_step; ?>%"><?php echo $i; ?></div>
              <?php } ?>
            </div>
          </div>

          <div id="div-contenedor"></div>
          <div class="container" id="app">
            <form action="https://httpbin.org/post" method="POST" enctype="application/x-www-form-urlencoded">
              <vue-form-generator :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
            </form>
          </div>
        </div>

        <?php if ($paso == 3) { ?>
          <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/crear/elegir-forma-de-pago/<?php echo $solicitud_id?>">
            <button type="button" class="btn btn-success btn-lg center-block">Siguiente</button>
          </a>
        <?php } ?>

      </div>
    </div>
  </div>
</div>
</section>


     
          
<?php if (isset($url)) { ?>
<script type="text/javascript">
$.ajax({
  url: '<?php echo $url?>',
  type: 'POST',
  dataType: 'html',
  async: true,
  data:{
    _token: "{{ csrf_token() }}"
  },
  success: function success(data, status) {        
    $("#div-contenedor").html(data);
  },
  error: function error(xhr, textStatus, errorThrown) {
      alert(errorThrown);
  }
});
</script>
<?php } ?>


<?php if ($paso == 4) { ?>



<?php } ?>

@endsection



