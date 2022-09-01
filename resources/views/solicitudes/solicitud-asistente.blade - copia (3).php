@extends('layouts.backend')

@section('contenido')

<?php 
if (!isset($paso)) {
  $paso = 1;
}

$steps = 7;
$width_step = 15; 
$width_progress_bar = $width_step*($paso-1);

if ($paso == 1) {
  $url = env('PATH_PUBLIC').'Solicitudes/crear/listar-clientes-para-seleccion';
  $titulo = 'Seleccione el Cliente';
}
if ($paso == 2) {
  $url = env('PATH_PUBLIC').'Solicitudes/crear/listar-modelos-para-seleccion/'.$solicitud_id;
  $titulo = 'Seleccione el Modelo';
}
if ($paso == 3) {
  $url = env('PATH_PUBLIC').'Solicitudes/crear/determinar-composicion-de-modelo-para-seleccion/'.$solicitud_id;
  $titulo = 'Determine los Componentes';
}
if ($paso == 4) {
  $titulo = 'Indique la Forma de Pago';
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
<script type="text/javascript" src="<?php echo env('PATH_PUBLIC')?>js/vue-form-generator/vfg.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo env('PATH_PUBLIC')?>js/vue-form-generator/vfg.css">

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
              <div class="slider-handle max-slider-handle round <?php echo $class_slider_gris; ?>" style="left: <?php echo $left_step; ?>%"><?php echo $i; ?>
                <div class="txt_paso_info"><br>
                  <?php 
                  if (isset($pasos_info[$i-1])) {
                    echo $pasos_info[$i-1];
                  }
                  ?>                    
                </div>
              </div>
              <?php } ?>
            </div>
          </div>
  <br>
  <br>

          <div id="div-contenedor"></div>
            <div id="app">
                <div class="panel-body">
                  {!! Form::open(array
                    (
                    'action' => 'GenericController@store', 
                    'role' => 'form',
                    'method' => 'POST',
                    'id' => "form_gen_modelo",
                    'enctype' => 'multipart/form-data',
                    'class' => 'form-vertical',
                    'ref' => 'form'
                    )) 
                  !!}
                   <div class="col-md-3 col-sm-6 col-xs-12">
                    <vue-form-generator :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
                    <!--input type="hidden" name="solicitud_id" :model="solicitud_id"-->
                  </div>
                  {!! Form::close() !!}
                </div>
            </div>


        </div>

        <?php if ($paso == 3) { ?>

          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box sombra">
              <span class="info-box-icon"><i class="fa fa-home"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Superficie Total</span>
                <span class="info-box-number"><?php echo $total_de_metros_cuadrados?> m<sup>2</sup></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>

          <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/crear/elegir-forma-de-pago/<?php echo $solicitud_id?>">
            <button type="button" class="btn btn-success btn-lg center-block">Continuar</button>
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
<script type="text/javascript">
var VueFormGenerator = window.VueFormGenerator;

var vm = new Vue({
  el: "#app",
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
    }
  },

  data: {
    model: {
      solicitud_id: '<?php echo $solicitud_id ?>',
      anticipo: 0,
      observaciones: '',
      sino_contado: 'SI',
      cuotas_anticipo: 0
    },
    schema: {
      fields: [

        <?php 
        echo $schemaVFG_lista_de_precios;
        ?>

        {
          type: "input",       
          inputType: "number",     
          model: "anticipo",    
          label: "Anticipo",    
          required: true,    
          inputName: "anticipo",
          id: "anticipo",
          validator: VueFormGenerator.validators.required
        },
        {
          type: "switch", 
          model: "sino_contado",     
          label: "Contado",   
          id: "sino_contado",  
          inputName: "sino_contado",          
          textOn: "SI", textOff: "NO", valueOn: "SI", valueOff: "NO"
        },
        {
          type: "input",       
          inputType: "number",     
          model: "cuotas_anticipo",    
          label: "Cantidad de Cuotas",    
          required: true,    
          inputName: "cuotas_anticipo",
          id: "cuotas_anticipo",
          visible(model) {
                if (model.sino_contado == 'SI') {
                    mostrar = false;
                }
                else {
                    mostrar = true;
                }
                return mostrar;
            },
          validator: VueFormGenerator.validators.required
        },
        {         
          type: "textArea",       
          model: "observaciones",  
          id: "observaciones",  
          label: "Observaciones",    
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


<?php } ?>

@endsection



