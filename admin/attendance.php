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
          Registro de Asistencia
          <small>Control y gestión de asistencia</small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
          <li class="active">Asistencia</li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <?php
        if (isset($_SESSION['error'])) {
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-ban'></i> Error!</h4>
              " . $_SESSION['error'] . "
            </div>
          ";
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check-circle'></i> Éxito!</h4>
              " . $_SESSION['success'] . "
            </div>
          ";
          unset($_SESSION['success']);
        }
        ?>

        <div class="row">
          <div class="col-xs-12">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">Registros de Asistencia</h3>
                <div class="box-tools pull-right">
                  <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat">
                    <i class="fa fa-plus"></i> Nuevo Registro
                  </a>
                </div>
              </div>

              <div class="box-body">
                <div class="table-responsive">
                  <table id="example1" class="table table-bordered table-striped table-hover">
                    <thead>
                      <tr class="bg-primary">
                        <th class="hidden">ID</th>
                        <th>Fecha</th>
                        <th>ID Empleado</th>
                        <th>Nombre Completo</th>
                        <th>Hora Entrada</th>
                        <th>Hora Salida</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT *, employees.employee_id AS empid, attendance.id AS attid FROM attendance LEFT JOIN employees ON employees.id=attendance.employee_id ORDER BY attendance.date DESC, attendance.time_in DESC";
                      $query = $conn->query($sql);
                      while ($row = $query->fetch_assoc()) {
                        $status = ($row['status']) ?
                          '<span class="label label-success">A tiempo</span>' :
                          '<span class="label label-danger">Tardanza</span>';

                        echo "
                          <tr>
                            <td class='hidden'>" . $row['attid'] . "</td>
                            <td>" . date('d M Y', strtotime($row['date'])) . "</td>
                            <td>" . $row['empid'] . "</td>
                            <td>" . $row['firstname'] . ' ' . $row['lastname'] . "</td>
                            <td>" . date('h:i A', strtotime($row['time_in'])) . "</td>
                            <td>" . date('h:i A', strtotime($row['time_out'])) . "</td>
                            <td>" . $status . "</td>
                            <td class='text-center'>
                              <div class='btn-group'>
                                <button class='btn btn-warning btn-sm btn-flat edit' data-id='" . $row['attid'] . "' title='Editar'>
                                  <i class='fa fa-edit'></i>
                                </button>
                                <button class='btn btn-danger btn-sm btn-flat delete' data-id='" . $row['attid'] . "' title='Eliminar'>
                                  <i class='fa fa-trash'></i>
                                </button>
                              </div>
                            </td>
                          </tr>
                        ";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="box-footer clearfix">
                <div class="pull-right">
                  <small>Mostrando <?php echo $query->num_rows; ?> registros</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/attendance_modal.php'; ?>
  </div>

  <?php include 'includes/scripts.php'; ?>
  <script>
    $(function() {
      // Evitar doble inicialización de DataTable
      if ( $.fn.DataTable.isDataTable('#example1') ) {
        $('#example1').DataTable().destroy();
      }
      $('#example1').DataTable({
        "language": {
          "lengthMenu": "Mostrar _MENU_ registros por página",
          "zeroRecords": "No se encontraron registros",
          "info": "Mostrando página _PAGE_ de _PAGES_",
          "infoEmpty": "No hay registros disponibles",
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
          [1, 'desc']
        ]
      });

      // Edit button click handler
      $('.edit').click(function(e) {
        e.preventDefault();
        $('#edit').modal('show');
        var id = $(this).data('id');
        getRow(id);
      });

      // Delete button click handler
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
        url: 'attendance_row.php',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(response) {
          $('#datepicker_edit').val(response.date);
          $('#attendance_date').html(response.date);
          $('#edit_time_in').val(response.time_in);
          $('#edit_time_out').val(response.time_out);
          $('#attid').val(response.attid);
          $('#employee_name').html(response.firstname + ' ' + response.lastname);
          $('#del_attid').val(response.attid);
          $('#del_employee_name').html(response.firstname + ' ' + response.lastname);
        },
        error: function(xhr, status, error) {
          console.error(xhr.responseText);
          alert('Error al obtener los datos del registro.');
        }
      });
    }
  </script>
</body>

</html>