@extends('layouts.backend')

@section('contenido')

      <div class="col-xs-12">

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Exportar <?php echo $tb ?></h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">

            <br>
            <div class="container">
              <a href="{{ URL::to('export/'.$tb.'/xls') }}"><button class="btn btn-success">Download Excel xls</button></a>
              <a href="{{ URL::to('export/'.$tb.'/xlsx') }}"><button class="btn btn-success">Download Excel xlsx</button></a>
              <a href="{{ URL::to('export/'.$tb.'/csv') }}"><button class="btn btn-success">Download CSV</button></a>
            </div>

          </div>
        </div>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Importar <?php echo $tb ?></h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">

              {!! Form::open(array
                (
                'url' => URL::to('import/'.$tb), 
                'role' => 'form',
                'method' => 'POST',
                'id' => "form_gen_modelo",
                'enctype' => 'multipart/form-data',
                'class' => 'form-horizontal',
                'ref' => 'form'
                )) 
              !!}
                <input type="hidden" name="tb" value="<?php echo $tb ?>" />
                <input type="file" name="import_file" />
                <br>
                <button class="btn btn-primary">Procesar Archivo</button>

              {!! Form::close() !!}

            </div>

            <?php if (isset($Resultados)) { ?>
              <div class="box-footer">

                <h3 class="box-title">Registros importados</h3>
                <table id="table_ok" class="table table-bordered table-striped" style="max-width: 500px" >
                  <thead>
                    <tr>
                        <th>Registro</th>
                        <th>Detalle</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($Resultados as $Resultado) { ?>
                    <?php if ($Resultado['importacion']) { ?>
                        <tr>
                          <td><?php echo $Resultado['valor']; ?></td>
                          <td><?php echo $Resultado['detalle']; ?></td>
                        </tr>
                    <?php } ?>
                  <?php } ?>
                  </tbody>
                </table>

                <script>
                  $(function () {
                    $('#table_ok').DataTable({
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
                            'infoFiltered': '(filtrado _MAX_ registros totales)'
                        },
                        'order': [[ 1, 'asc' ]],
                        'columnDefs': [{ "width": "100px", "targets": 0 }], 
                    })
                  })
                </script>

              </div>
              <div class="box-footer">

                <h3 class="box-title">Registros con Errores (No importados)</h3>
                <table id="table_error" class="table table-bordered table-striped" style="max-width: 500px" >
                  <thead>
                    <tr>
                        <th>Registro</th>
                        <th>Detalle</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($Resultados as $Resultado) { ?>
                    <?php if (!$Resultado['importacion']) { ?>
                        <tr>
                          <td><?php echo $Resultado['valor']; ?></td>
                          <td><?php echo $Resultado['detalle']; ?></td>
                        </tr>
                    <?php } ?>
                  <?php } ?>
                  </tbody>
                </table>

                <script>
                  $(function () {
                    $('#table_error').DataTable({
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
                            'infoFiltered': '(filtrado _MAX_ registros totales)'
                        },
                        'order': [[ 1, 'asc' ]],
                        'columnDefs': [{ "width": "100px", "targets": 0 }], 
                    })
                  })
                </script>

              </div>              
            <?php } ?>

          </div>
        </div>
      </div>



  

  


@endsection
