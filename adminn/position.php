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
          Gestión de Cargos
          <small>Administración de puestos y salarios</small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-home"></i> Inicio</a></li>
          <li class="active">Cargos</li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
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
                <h3 class="box-title">Listado de Cargos</h3>
                <div class="box-tools pull-right">
                  <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat">
                    <i class="fa fa-plus"></i> Nuevo Cargo
                  </a>
                </div>
              </div>

              <div class="box-body">
                <div class="table-responsive">
                  <table id="example1" class="table table-bordered table-striped table-hover">
                    <thead class="bg-blue">
                      <tr>
                        <th width="50%">Título del Puesto</th>
                        <th width="20%">Tarifa por Hora</th>
                        <th width="30%">Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT * FROM position";
                      $query = $conn->query($sql);
                      while ($row = $query->fetch_assoc()) {
                        echo '
                          <tr>
                            <td>
                              <i class="fa fa-briefcase" style="color: #3498db; margin-right: 10px;"></i>
                              ' . $row['description'] . '
                            </td>
                            <td class="text-right">$' . number_format($row['rate'], 2) . '</td>
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
    <?php include 'includes/position_modal.php'; ?>
  </div>

  <?php include 'includes/scripts.php'; ?>

  <script>
    $(function() {
      // Inicializar DataTable con opciones mejoradas
      if ( $.fn.DataTable.isDataTable('#example1') ) {
        $('#example1').DataTable().destroy();
      }
      $('#example1').DataTable({
        "language": {
          "lengthMenu": "Mostrar _MENU_ registros por página",
          "zeroRecords": "No se encontraron cargos",
          "info": "Mostrando página _PAGE_ de _PAGES_",
          "infoEmpty": "No hay cargos registrados",
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
        url: 'position_row.php',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(response) {
          $('#posid').val(response.id);
          $('#edit_title').val(response.description);
          $('#edit_rate').val(response.rate);
          $('#del_posid').val(response.id);
          $('#del_position').html(response.description);
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

    .fa-briefcase {
      font-size: 16px;
    }

    .table-responsive {
      border-radius: 5px;
      overflow: hidden;
    }
  </style>
</body>

</html>