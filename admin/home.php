<?php include 'includes/session.php'; ?>
<?php
include '../timezone.php';
$today = date('Y-m-d');
$year = date('Y');
if (isset($_GET['year'])) {
  $year = $_GET['year'];
}
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
          Panel de Control
          <small>Resumen de actividad</small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
          <li class="active">Panel de Control</li>
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
              <h4><i class='icon fa fa-check'></i> ¡Éxito!</h4>
              " . $_SESSION['success'] . "
            </div>
          ";
          unset($_SESSION['success']);
        }
        ?>

        <!-- Info boxes -->
        <div class="row">
          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-aqua"><i class="ion ion-ios-people-outline"></i></span>
              <div class="info-box-content">
                <?php
                $sql = "SELECT * FROM employees";
                $query = $conn->query($sql);
                $total_employees = $query->num_rows;
                ?>
                <span class="info-box-text">Total Empleados</span>
                <span class="info-box-number"><?php echo $total_employees; ?></span>
                <div class="progress">
                  <div class="progress-bar bg-aqua" style="width: 100%"></div>
                </div>
                <span class="progress-description">
                  <a href="employee.php" style="color: #555;">Ver detalles</a>
                </span>
              </div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-green"><i class="ion ion-checkmark-circled"></i></span>
              <div class="info-box-content">
                <?php
                $sql = "SELECT * FROM attendance";
                $query = $conn->query($sql);
                $total = $query->num_rows;

                $sql = "SELECT * FROM attendance WHERE status = 1";
                $query = $conn->query($sql);
                $early = $query->num_rows;

                $percentage = ($early / $total) * 100;
                ?>
                <span class="info-box-text">Puntualidad</span>
                <span class="info-box-number"><?php echo number_format($percentage, 2); ?>%</span>
                <div class="progress">
                  <div class="progress-bar bg-green" style="width: <?php echo $percentage; ?>%"></div>
                </div>
                <span class="progress-description">
                  <a href="attendance.php" style="color: #555;">Ver historial</a>
                </span>
              </div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-yellow"><i class="ion ion-clock"></i></span>
              <div class="info-box-content">
                <?php
                $sql = "SELECT * FROM attendance WHERE date = '$today' AND status = 1";
                $query = $conn->query($sql);
                $ontime_today = $query->num_rows;
                ?>
                <span class="info-box-text">Puntuales hoy</span>
                <span class="info-box-number"><?php echo $ontime_today; ?></span>
                <div class="progress">
                  <div class="progress-bar bg-yellow" style="width: <?php echo ($total_employees > 0) ? ($ontime_today / $total_employees) * 100 : 0; ?>%"></div>
                </div>
                <span class="progress-description">
                  <?php echo date('d M Y'); ?>
                </span>
              </div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-red"><i class="ion ion-alert-circled"></i></span>
              <div class="info-box-content">
                <?php
                $sql = "SELECT * FROM attendance WHERE date = '$today' AND status = 0";
                $query = $conn->query($sql);
                $late_today = $query->num_rows;
                ?>
                <span class="info-box-text">Tardíos hoy</span>
                <span class="info-box-number"><?php echo $late_today; ?></span>
                <div class="progress">
                  <div class="progress-bar bg-red" style="width: <?php echo ($total_employees > 0) ? ($late_today / $total_employees) * 100 : 0; ?>%"></div>
                </div>
                <span class="progress-description">
                  <?php echo ($late_today > 0) ? 'Necesita atención' : 'Todo en orden'; ?>
                </span>
              </div>
            </div>
          </div>
        </div>
        <!-- /.row -->

        <div class="row">
          <div class="col-md-12">
            <div class="box box-solid">
              <div class="box-header with-border">
                <h3 class="box-title">Informe de Asistencia Mensual</h3>
                <div class="box-tools pull-right">
                  <form class="form-inline">
                    <div class="form-group">
                      <label>Año: </label>
                      <select class="form-control input-sm" id="select_year">
                        <?php
                        for ($i = 2015; $i <= 2065; $i++) {
                          $selected = ($i == $year) ? 'selected' : '';
                          echo "
                            <option value='" . $i . "' " . $selected . ">" . $i . "</option>
                          ";
                        }
                        ?>
                      </select>
                    </div>
                  </form>
                </div>
              </div>
              <div class="box-body">
                <div class="chart-responsive">
                  <canvas id="barChart" height="180"></canvas>
                </div>
              </div>
              <div class="box-footer no-padding">
                <div class="row">
                  <div class="col-md-12 text-center" id="legend"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <?php include 'includes/footer.php'; ?>

  </div>
  <!-- ./wrapper -->

  <!-- Chart Data -->
  <?php
  $and = 'AND YEAR(date) = ' . $year;
  $months = array();
  $ontime = array();
  $late = array();
  for ($m = 1; $m <= 12; $m++) {
    $sql = "SELECT * FROM attendance WHERE MONTH(date) = '$m' AND status = 1 $and";
    $oquery = $conn->query($sql);
    array_push($ontime, $oquery->num_rows);

    $sql = "SELECT * FROM attendance WHERE MONTH(date) = '$m' AND status = 0 $and";
    $lquery = $conn->query($sql);
    array_push($late, $lquery->num_rows);

    $num = str_pad($m, 2, 0, STR_PAD_LEFT);
    $month =  date('M', mktime(0, 0, 0, $m, 1));
    array_push($months, $month);
  }

  $months = json_encode($months);
  $late = json_encode($late);
  $ontime = json_encode($ontime);

  ?>
  <!-- End Chart Data -->
  <?php include 'includes/scripts.php'; ?>
  <script>
    $(function() {
      var barChartCanvas = $('#barChart').get(0).getContext('2d')
      var barChart = new Chart(barChartCanvas)
      var barChartData = {
        labels: <?php echo $months; ?>,
        datasets: [{
            label: 'Tarde',
            fillColor: 'rgba(210, 214, 222, 1)',
            strokeColor: 'rgba(210, 214, 222, 1)',
            pointColor: 'rgba(210, 214, 222, 1)',
            pointStrokeColor: '#c1c7d1',
            pointHighlightFill: '#fff',
            pointHighlightStroke: 'rgba(220,220,220,1)',
            data: <?php echo $late; ?>
          },
          {
            label: 'A tiempo',
            fillColor: 'rgba(60,141,188,0.9)',
            strokeColor: 'rgba(60,141,188,0.8)',
            pointColor: '#3b8bba',
            pointStrokeColor: 'rgba(60,141,188,1)',
            pointHighlightFill: '#fff',
            pointHighlightStroke: 'rgba(60,141,188,1)',
            data: <?php echo $ontime; ?>
          }
        ]
      }

      // Update colors for better visibility
      barChartData.datasets[0].fillColor = '#dd4b39';
      barChartData.datasets[0].strokeColor = '#dd4b39';
      barChartData.datasets[0].pointColor = '#dd4b39';

      barChartData.datasets[1].fillColor = '#00a65a';
      barChartData.datasets[1].strokeColor = '#00a65a';
      barChartData.datasets[1].pointColor = '#00a65a';

      var barChartOptions = {
        scaleBeginAtZero: true,
        scaleShowGridLines: true,
        scaleGridLineColor: 'rgba(0,0,0,.05)',
        scaleGridLineWidth: 1,
        scaleShowHorizontalLines: true,
        scaleShowVerticalLines: true,
        barShowStroke: true,
        barStrokeWidth: 2,
        barValueSpacing: 5,
        barDatasetSpacing: 1,
        legendTemplate: '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
        responsive: true,
        maintainAspectRatio: false,
        tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %> registros",
        multiTooltipTemplate: "<%= value %> registros"
      }

      barChartOptions.datasetFill = false;
      var myChart = barChart.Bar(barChartData, barChartOptions);
      document.getElementById('legend').innerHTML = myChart.generateLegend();
    });
  </script>
  <script>
    $(function() {
      $('#select_year').change(function() {
        window.location.href = 'home.php?year=' + $(this).val();
      });
    });
  </script>
</body>

</html>