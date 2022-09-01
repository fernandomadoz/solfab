@extends('layouts.backend')

@section('contenido')

<div class="col-xs-12">
<?php if(isset($mensaje)) { ?>
  <br>
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h4><i class="icon fa fa-check"></i> <?php echo $mensaje; ?></h4>  
  </div>
<?php } ?>
</div>

<div id="tabla"></div>

<?php 
$gen_seteo['gen_campos_a_ocultar'] = 'id';
?>

<script type="text/javascript">
$.ajax({
  url: '<?php echo env('PATH_PUBLIC')?>crearlista',
  type: 'POST',
  dataType: 'html',
  async: true,
  data:{
    _token: "{{ csrf_token() }}",
    gen_modelo: '<?php echo $gen_modelo ?>',
    gen_seteo: '<?php echo serialize($gen_seteo) ?>',
    gen_opcion: '<?php echo $gen_opcion ?>'
  },
  success: function success(data, status) {        
    $("#tabla").html(data);
  },
  error: function error(xhr, textStatus, errorThrown) {
      alert(errorThrown);
  }
});
</script>

@endsection
