<?php 

// CAMPOS A OCULTAR
if (isset($gen_seteo['gen_campos_a_ocultar'])) {
  $gen_campos_a_ocultar = $gen_seteo['gen_campos_a_ocultar'];
}
else {
  $gen_campos_a_ocultar = [];  
}
//var_dump($gen_seteo);

// DESHABILITO CAMPOS SI ES BORRAR
if ($gen_accion == 'b') {
  $disabled = 'disabled="disabled"';
}
else {
  $disabled = '';  
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
<script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
<link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">



<div id="app">
    <div class="panel-body">
      {!! Form::open(array
        (
        'action' => 'GenericController@store', 
        'role' => 'form',
        'method' => 'POST',
        'id' => "form_gen_modelo",
        'enctype' => 'multipart/form-data',
        'class' => 'form-horizontal',
        'ref' => 'form'
        )) 
      !!}
        <vue-form-generator :schema="schema" :model="model" :options="formOptions"></vue-form-generator>
      {!! Form::close() !!}
    </div>

  <div class="panel panel-default">
    <div class="panel-heading">Model</div>
    <div class="panel-body">
      <pre v-if="model" v-html="prettyJSON(model)"></pre>
    </div>
  </div>

</div>


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
      library: '',
    <?php 
    foreach ($gen_campos as $campo) {
      if (!in_array($campo['nombre'], $gen_campos_a_ocultar)) { 
        // ASIGNO EL VALOR DEL CAMPO SI ES MODIFICACION O BAJA
        if ($gen_accion == 'm' or $gen_accion == 'b') {
          echo $campo['nombre'].": '".$gen_fila[$campo['nombre']]."',";
        }
        else {
          echo $campo['nombre'].": '',";
        }
      }
    }        
    ?>
      gen_modelo: '<?php echo $gen_modelo ?>',
      gen_accion: '<?php echo $gen_accion ?>',
      gen_id: '<?php echo $gen_id ?>',
      empresa_id: '1'    
    },
    schema: {
      fields: [
      <?php echo $schema_vfg; ?>
        {
          type: "input",
          inputType: "hidden",
          model: "gen_modelo",
          inputName: "gen_modelo"      },
        {
          type: "input",
          inputType: "hidden",
          model: "gen_accion",
          inputName: "gen_accion"
        },
        {
          type: "input",
          inputType: "hidden",
          model: "gen_id",
          inputName: "gen_id"
        },
        {
          type: "input",
          inputType: "hidden",
          model: "empresa_id",
          inputName: "empresa_id"
        },

        {
          type: "submit",
          label: "",
          buttonText: "Submit",
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