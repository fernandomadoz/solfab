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




<!-- INICIO APP vue.js -->
<div id="app">

  <div class="box box-warning">

    <!-- /.box-header -->
    <div class="box-body">
        <!-- text input -->
          <?php 
          foreach ($gen_campos as $campo) {
            if (!in_array($campo['nombre'], $gen_campos_a_ocultar)) { 

              // ASIGNO EL VALOR DEL CAMPO SI ES ALTA O BAJA
              if ($gen_accion == 'm' or $gen_accion == 'b') {
                $valor_del_campo = $gen_fila[$campo['nombre']];
              }
              else {
                $valor_del_campo = '';
              }

              // SI EL CAMPOS ES NULO
              $required = '';
              if ($campo['nulo'] == 'SI') {
                $required = 'required';
              }
          ?>
          <th></th>              
          <div class="form-group">
            <label for="<?php echo $campo['nombre']; ?>"><?php echo $campo['nombre_a_mostrar']; ?></label>
            <input v-validate="'required'"  id="<?php echo $campo['nombre']; ?>" name="<?php echo $campo['nombre']; ?>" type="text" class="form-control" placeholder="<?php echo $campo['nombre']; ?>" v-model="<?php echo $campo['nombre']; ?>">
            <span v-show="errors.has('<?php echo $campo['nombre']; ?>')" class="text-danger">{{errors.first('<?php echo $campo['nombre']; ?>')}}</span>
            <!--input type="text" name="<?php echo $campo['nombre']; ?>" id="<?php echo $campo['nombre']; ?>" value="<?php echo $valor_del_campo; ?>" class="form-control" placeholder="<?php echo $campo['nombre_a_mostrar']; ?>" <?php echo $disabled; ?> <?php echo $required; ?>-->
          </div>
          <?php
            } 
          } 
          ?>
    <!-- /.box-body -->
  </div>
  <!-- /.box -->
  <div class="modal-footer">
    <button type="button" class="btn pull-left" data-dismiss="modal">Cerrar</button>
    <input type="hidden" name="gen_modelo" value="<?php echo $gen_modelo; ?>">
    <input type="hidden" name="gen_accion" value="<?php echo $gen_accion; ?>">
    <input type="hidden" name="gen_id" value="<?php echo $gen_id; ?>">
    <input type="hidden" name="empresa_id" value="1">
    <button type="button" class="btn" v-on:click="validar_errores"><?php echo $etiqueta_btn_gen_accion; ?></button>
  </div>
  {!! Form::close() !!}

<!--select v-model="selected">
  <option v-for="option in options" v-bind:value="option.value">
    @{{ option.text }}
  </option>
</select>
<span>Selected: @{{ selected }}</span-->

<transition name="fade">
  <div v-show="mostrar_mensaje_error" class="alert alert-danger" role="alert">
    @{{ mensaje_error }}
  </div>              
</transition>

<div class="col-lg-12">            
  <pre>@{{ $data }}</pre>
</div>  

</div>
<!-- FIN APP vue.js -->

<script type="text/javascript">

const config = {
  locale: 'es', 
};


Vue.use(VeeValidate, config);

var app = new Vue({
  el: '#app',
  data: {
      mostrar_mensaje_error: false,
      mensaje_error: '',
      guardar: false,

  <?php 
  foreach ($gen_campos as $campo) {
    if (!in_array($campo['nombre'], $gen_campos_a_ocultar)) { 

      // ASIGNO EL VALOR DEL CAMPO SI ES ALTA O BAJA
      if ($gen_accion == 'm' or $gen_accion == 'b') {
        echo $campo['nombre'].": '".$gen_fila[$campo['nombre']]."',";
      }
      else {
        echo $campo['nombre'].": '',";
      }
  ?>

  <?php
    }
  }
  ?>
  },
  methods: {
    validar_errores: function(){
      // VALIDO SI HAY ERRORES
      this.$validator.validateAll().then(() => {
          if (this.errors.any()) {
            // SI HAY ERRORES
            this.guardar = false
            this.mostrar_mensaje_error = true
            this.mensaje_error = 'Hay campos que corrergir'
            console.log('error')
          }
          else {
            // SI NO HAY ERRORES
            this.mostrar_mensaje_error = false
            this.guardar = true
            document.getElementById('form_gen_modelo').submit()
            console.log('enviar')

          }
      }).catch(() => {
          this.title = this.errors;
      });
    }
  },  

})
</script>