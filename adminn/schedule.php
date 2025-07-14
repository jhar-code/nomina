<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Gestión de Horarios
          <small>Configuración de turnos laborales</small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-home"></i> Inicio</a></li>
          <li class="active">Horarios</li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <!-- Notificaciones -->
        <?php
        if (isset($_SESSION['error'])) {
          echo '
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h4><i class="icon fa fa-ban"></i> Alerta!</h4>
              ' . $_SESSION['error'] . '
            </div>
          ';
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
          echo '
            <div class="alert alert-success alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h4><i class="icon fa fa-check-circle"></i> Éxito!</h4>
              ' . $_SESSION['success'] . '
            </div>
          ';
          unset($_SESSION['success']);
        }
        ?>

        <div class="row">
          <div class="col-xs-12">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">Listado de Horarios</h3>
                <div class="box-tools pull-right">
                  <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat">
                    <i class="fa fa-plus"></i> Nuevo Horario
                  </a>
                </div>
              </div>

              <div class="box-body">
                <div class="table-responsive">
                  <table id="example1" class="table table-bordered table-striped table-hover">
                    <thead class="bg-blue">
                      <tr>
                        <th width="35%">Hora de Entrada</th>
                        <th width="35%">Hora de Salida</th>
                        <th width="30%">Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT * FROM schedules";
                      $query = $conn->query($sql);
                      while ($row = $query->fetch_assoc()) {
                        echo '
                          <tr>
                            <td class="text-center">' . date('h:i A', strtotime($row['time_in'])) . '</td>
                            <td class="text-center">' . date('h:i A', strtotime($row['time_out'])) . '</td>
                            <td class="text-center">
                              <div class="btn-group">
                                <button class="btn btn-sm btn-warning edit btn-flat" data-id="' . $row['id'] . '">
                                  <i class="fa fa-edit"></i> Editar
                                </button>
                                <button class="btn btn-sm btn-danger delete btn-flat" data-id="' . $row['id'] . '">
                                  <i class="fa fa-trash"></i> Eliminar
                                </button>
                              </div>
                            </td>
                          </tr>
                        ';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/schedule_modal.php'; ?>
  </div>

  <?php include 'includes/scripts.php'; ?>

  <script>
    $(function() {
      // Inicializar DataTable con opciones.
      if ( $.fn.DataTable.isDataTable('#example1') ) {
        $('#example1').DataTable().destroy();
      }
      $('#example1').DataTable({
        "language": {
          "lengthMenu": "Mostrar _MENU_ registros por página",
          "zeroRecords": "No se encontraron horarios",
          "info": "Mostrando página _PAGE_ de _PAGES_",
          "infoEmpty": "No hay horarios registrados",
          "infoFiltered": "(filtrado de _MAX_ registros totales)",
          "search": "Buscar:",
          "paginate": {
            "first": "Primero",
            "last": "Último",
            "next": "Siguiente",
            "previous": "Anterior"
          }
        },
        "responsive": true,
        "autoWidth": false,
        "order": [
          [0, "asc"]
        ]
      });

      // Funciones originales mantenidas
      $('.edit').click(function(e) {
        e.preventDefault();
        $('#edit').modal('show');
        var id = $(this).data('id');
        getRow(id);
      });

      $('.delete').click(function(e) {
        e.preventDefault();
        $('#delete').modal('show');
        var id = $(this).data('id');
        getRow(id);
      });
    });

    function getRow(id) {
      $.ajax({
        type: 'POST',
        url: 'schedule_row.php',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(response) {
          $('#timeid').val(response.id);
          $('#edit_time_in').val(response.time_in);
          $('#edit_time_out').val(response.time_out);
          $('#del_timeid').val(response.id);
          $('#del_schedule').html(response.time_in + ' - ' + response.time_out);
        }
      });
    }
  </script>

  <!-- Estilos adicionales -->
  <style>
    .box-primary {
      border-top-color: #3c8dbc;
      box-shadow: 0 1px 10px rgba(0, 0, 0, 0.1);
    }

    .table th {
      background-color: #3c8dbc !important;
      color: white;
    }

    .btn-group .btn {
      margin-right: 5px;
      border-radius: 3px !important;
    }

    .table-hover tbody tr:hover {
      background-color: #f5f5f5;
    }

    .content-header h1 small {
      font-size: 14px;
      color: #666;
      display: block;
      margin-top: 5px;
    }

    .table-responsive {
      border-radius: 5px;
      overflow: hidden;
    }
  </style>
</body>

</html>