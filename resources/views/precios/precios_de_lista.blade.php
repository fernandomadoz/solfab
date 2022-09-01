@extends('layouts.backend')

@section('contenido')


<div id="Lista_de_precio" style="padding: 15px;">
  <!-- Default box -->
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Lista de Precios: <?php echo $Lista_de_precio->lista_de_precio ?></h3>

    </div>
    <div class="box-body" style="">       
    <!-- /.box-body -->
    <div class="col-lg-12">
      <div id="precios"></div>
    </div>

    </div>
    <!-- /.box -->

  </div>
</div>


          
          




<script type="text/javascript">



$.ajax({
  url: '<?php echo env('PATH_PUBLIC')?>crear-lista-de-precios',
  type: 'POST',
  dataType: 'html',
  async: true,
  data:{
    _token: "{{ csrf_token() }}",
    lista_de_precio_id: '<?php echo $Lista_de_precio->id ?>'
  },
  success: function success(data, status) {        
    $("#precios").html(data);
  },
  error: function error(xhr, textStatus, errorThrown) {
      alert(errorThrown);
  }
});
</script>

@endsection
