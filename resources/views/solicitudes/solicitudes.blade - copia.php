@extends('layouts.backend')

@section('contenido')

    
    <br>

      <div class="col-xs-12">

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Solicitudes</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="table" class="table table-bordered table-striped" style="max-width: 500px" >
              <thead>
              <tr>
                  <th>Acci&oacute;n</th>
                  <th>Sucursal</th>
                  <th>Solicitud</th>
                  <th>Cliente</th>
              </tr>
              </thead>
              <tbody>
              <?php foreach ($Solicitudes as $solicitud) { ?>
              <tr>
                  <td>
                    <div class="btn-group">
                      <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/solicitud/<?php echo $solicitud['id']; ?>">
                      <button type="button" class="btn btn-info" alt="editar" title="editar"><i class="fa fa-pencil"></i></button>
                    </a>
                    </div>
                  </td>

                  <td>Sucursal</td>
                  <td>Solicitud</td>
                  <td>Cliente</td>
              </tr>
              <?php } ?>
            </tbody>
            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>

      <div class="col-xs-3">
        <a href="<?php echo env('PATH_PUBLIC')?>Solicitudes/crear"><button type="button" class="btn btn-block btn-info col-xs-3"><i class="fa fa-plus"></i> Crear Solicitud</button></a>
      </div>

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
        

          
          



@endsection
