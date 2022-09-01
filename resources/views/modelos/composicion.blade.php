@extends('layouts.backend')

@section('contenido')


<div id="modelo" style="padding: 15px;">
  <!-- Default box -->
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Modelo</h3>

    </div>
    <div class="box-body" style="">       
    <!-- /.box-body -->
      <div class="col-lg-6">
        <div class="col-lg-12">
         <div class="callout callout-info">
            <h4><?php echo $Modelo[0]['modelo'] ?></h4><br>
            <h5>Total de Metros cuadrados: <?php echo $Modelo[0]['total_de_metros_cuadrados'] ?></h5><br>

            <p><strong>Observaciones:</strong><br><br><?php echo $Modelo[0]['Observaciones'] ?></p>
            <?php if ($Modelo[0]['file_plano'] <> '') { ?>
              <p><a target="_blank" href="<?php echo env('PATH_PUBLIC') ?>storage/<?php echo $Modelo[0]['file_plano'] ?>"><button type="button" class="btn btn-default btn-lg" style="width: 100%"><i class="fa fa-map-o"></i> Plano del modelo</button></a></p>
            <?php } ?>
          </div>
        </div>
        <?php if (count($Imagenes_de_modelo) > 0) { ?>        
          <div class="col-lg-12">
            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="999999999">
              <ol class="carousel-indicators">
                <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                <?php for ($i=1; $i<count($Imagenes_de_modelo); $i++) { ?>
                <li data-target="#carousel-example-generic" data-slide-to="<?php echo $i ?>" class=""></li>
                <?php } ?>
              </ol>
              <div class="carousel-inner">
                <?php 
                $active = 'active';
                foreach ($Imagenes_de_modelo as $Imagen_de_modelo) { 
                ?>
                <div class="item <?php echo $active ?>">
                  <img src="<?php echo $Imagen_de_modelo['img_imagen'] ?>">
                </div>
                <?php 
                  if ($active == 'active') {
                    $active = '';
                  }
                } 
                ?>
              </div>
              <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                <span class="fa fa-angle-left"></span>
              </a>
              <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                <span class="fa fa-angle-right"></span>
              </a>
            </div>
          </div>
        <?php } ?>
      </div>   

    <div class="col-lg-6">
      <div id="composicion"></div>
    </div>

    </div>
    <!-- /.box -->

  </div>
</div>


          
          




<script type="text/javascript">



$.ajax({
  url: '<?php echo env('PATH_PUBLIC')?>crearlistamodelo',
  type: 'POST',
  dataType: 'html',
  async: true,
  data:{
    _token: "{{ csrf_token() }}",
    modelo_id: '<?php echo $modelo_id ?>'
  },
  success: function success(data, status) {        
    $("#composicion").html(data);
  },
  error: function error(xhr, textStatus, errorThrown) {
      alert(errorThrown);
  }
});
</script>

@endsection
