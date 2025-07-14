<?php include 'includes/session.php'; ?>
<?php
include '../timezone.php';
$range_to = date('m/d/Y');
$range_from = date('m/d/Y', strtotime('-30 day', strtotime($range_to)));
?>
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
          <i class="fa fa-money"></i> Nómina
        </h1>
        <ol class="breadcrumb">
          <li><a href="home.php"><i class="fa fa-home"></i> Inicio</a></li>
          <li class="active">Nómina</li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <?php
        if (isset($_SESSION['error'])) {
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              " . $_SESSION['error'] . "
            </div>
          ";
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Éxito!</h4>
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
                <h3 class="box-title">Filtros de Nómina</h3>
                <div class="box-tools pull-right">
                  <form method="POST" class="form-inline" id="payForm">
                    <div class="input-group">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control pull-right" id="reservation" name="date_range" value="<?php echo (isset($_GET['range'])) ? $_GET['range'] : $range_from . ' - ' . $range_to; ?>">
                    </div>
                    <div class="btn-group">
                      <button type="button" class="btn btn-success btn-flat" id="payroll">
                        <i class="fa fa-file-text-o"></i> Nómina Completa
                      </button>
                      <button type="button" class="btn btn-info btn-flat" id="payslip">
                        <i class="fa fa-file-pdf-o"></i> Recibos Individuales
                      </button>
                    </div>
                  </form>
                </div>
              </div>

              <div class="box-body">
                <table id="payrollTable" class="table table-bordered table-striped table-hover">
                  <thead>
                    <tr class="bg-blue">
                      <th>Empleado</th>
                      <th>ID</th>
                      <th>Salario Bruto</th>
                      <th>Deducciones</th>
                      <th>Adelantos</th>
                      <th>Salario Neto</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql = "SELECT *, SUM(amount) as total_amount FROM deductions";
                    $query = $conn->query($sql);
                    $drow = $query->fetch_assoc();
                    $deduction = $drow['total_amount'];

                    $to = date('Y-m-d');
                    $from = date('Y-m-d', strtotime('-30 day', strtotime($to)));

                    if (isset($_GET['range'])) {
                      $range = $_GET['range'];
                      $ex = explode(' - ', $range);
                      $from = date('Y-m-d', strtotime($ex[0]));
                      $to = date('Y-m-d', strtotime($ex[1]));
                    }

                    $sql = "SELECT *, SUM(num_hr) AS total_hr, attendance.employee_id AS empid FROM attendance LEFT JOIN employees ON employees.id=attendance.employee_id LEFT JOIN position ON position.id=employees.position_id WHERE date BETWEEN '$from' AND '$to' GROUP BY attendance.employee_id ORDER BY employees.lastname ASC, employees.firstname ASC";

                    $query = $conn->query($sql);
                    $total = 0;
                    while ($row = $query->fetch_assoc()) {
                      $empid = $row['empid'];

                      $casql = "SELECT *, SUM(amount) AS cashamount FROM cashadvance WHERE employee_id='$empid' AND date_advance BETWEEN '$from' AND '$to'";

                      $caquery = $conn->query($casql);
                      $carow = $caquery->fetch_assoc();
                      $cashadvance = $carow['cashamount'];

                      $gross = $row['rate'] * $row['total_hr'];
                      $total_deduction = $deduction + $cashadvance;
                      $net = $gross - $total_deduction;

                      echo "
                        <tr>
                          <td>" . $row['lastname'] . ", " . $row['firstname'] . "</td>
                          <td>" . $row['employee_id'] . "</td>
                          <td class='text-right'>" . number_format($gross, 2) . "</td>
                          <td class='text-right'>" . number_format($deduction, 2) . "</td>
                          <td class='text-right'>" . number_format($cashadvance, 2) . "</td>
                          <td class='text-right'><strong>" . number_format($net, 2) . "</strong></td>
                        </tr>
                      ";
                    }
                    ?>
                  </tbody>
                </table>
              </div>

              <div class="box-footer">
                <small class="text-muted">Período: <?php echo isset($_GET['range']) ? $_GET['range'] : $range_from . ' - ' . $range_to; ?></small>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <?php include 'includes/footer.php'; ?>
  </div>
  <?php include 'includes/scripts.php'; ?>

  <script>
    $(function() {
      // DataTable initialization with Bootstrap 3 compatibility
      $('#payrollTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "language": {
          "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
      });

      // Date range picker
      $('#reservation').daterangepicker({
        locale: {
          format: 'MM/DD/YYYY',
          applyLabel: 'Aplicar',
          cancelLabel: 'Cancelar',
          fromLabel: 'Desde',
          toLabel: 'Hasta',
          customRangeLabel: 'Personalizado',
          daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
          monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
          firstDay: 1
        },
        opens: 'right',
        autoUpdateInput: true
      });

      // Handle date range change
      $("#reservation").on('apply.daterangepicker', function(ev, picker) {
        var range = encodeURI(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        window.location = 'payroll.php?range=' + range;
      });

      // Payroll buttons
      $('#payroll').click(function(e) {
        e.preventDefault();
        $('#payForm').attr('action', 'payroll_generate.php');
        $('#payForm').submit();
      });

      $('#payslip').click(function(e) {
        e.preventDefault();
        $('#payForm').attr('action', 'payslip_generate.php');
        $('#payForm').submit();
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
</body>

</html>