<?php session_start(); ?>
<?php include 'header.php'; ?>

<body class="hold-transition login-page" style="background: #f5f5f5;">
  <div class="login-box" style="width: 400px; margin-top: 50px;">
    <!-- Encabezado con logo y reloj -->
    <div class="login-logo">
      <div style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <img src="images/company.png" alt="Logo Empresa" style="height: 200px; margin-bottom: 100x;">
        <div style="border-top: 1px solid #eee; padding-top: 15px;">
          <p id="date" style="margin: 0; color: #555; font-size: 16px;"></p>
          <p id="time" class="bold" style="margin: 0; color: #333; font-size: 28px; font-weight: 700;"></p>
        </div>
      </div>
    </div>

    <!-- Formulario de asistencia -->
    <div class="login-box-body" style="border-radius: 10px; box-shadow: 0 3px 20px rgba(0,0,0,0.08); background: #fff; padding: 25px;">
      <h4 class="login-box-msg" style="color: #333; font-weight: 600; margin-bottom: 25px;">Registro de Asistencia</h4>

      <form id="attendance">
        <div class="form-group">
          <select class="form-control" name="status" style="padding: 12px; border-radius: 6px; border: 1px solid #ddd; height: auto;">
            <option value="in">🟢 Entrada</option>
            <option value="out">🔴 Salida</option>
          </select>
        </div>

        <div class="form-group has-feedback" style="margin-bottom: 25px;">
          <input type="text" class="form-control input-lg" id="employee" name="employee" required
            style="padding: 15px; border-radius: 6px; border: 1px solid #ddd;"
            placeholder="Ingrese su ID de empleado">
          <span class="glyphicon glyphicon-user form-control-feedback" style="line-height: 50px; color: #777;"></span>
        </div>

        <div class="row">
          <div class="col-xs-12">
            <button type="submit" class="btn btn-primary btn-block btn-flat" name="signin"
              style="padding: 12px; font-size: 16px; font-weight: 600; border-radius: 6px; border: none; background: #3498db;">
              <i class="fa fa-sign-in"></i> Registrar
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- Mensajes de alerta (manteniendo clases originales) -->
    <div class="alert alert-success alert-dismissible mt20 text-center" style="display:none; border-radius: 6px; margin-top: 20px;">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <span class="result"><i class="icon fa fa-check"></i> <span class="message"></span></span>
    </div>

    <div class="alert alert-danger alert-dismissible mt20 text-center" style="display:none; border-radius: 6px; margin-top: 20px;">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <span class="result"><i class="icon fa fa-warning"></i> <span class="message"></span></span>
    </div>

    <!-- Pie de página -->
    <div style="text-align: center; margin-top: 20px; color: #777; font-size: 13px;">
      <?php echo date('Y'); ?> © Sistema de Asistencia
    </div>
  </div>

  <?php include 'scripts.php' ?>
  <script type="text/javascript">
    $(function() {
      // Reloj funcional 
      var interval = setInterval(function() {
        var momentNow = moment();
        $('#date').html(momentNow.format('dddd').substring(0, 3).toUpperCase() + ' - ' + momentNow.format('MMMM DD, YYYY'));
        $('#time').html(momentNow.format('hh:mm:ss A'));
      }, 100);

      // Formulario 
      $('#attendance').submit(function(e) {
        e.preventDefault();
        var attendance = $(this).serialize();
        $.ajax({
          type: 'POST',
          url: 'attendance.php',
          data: attendance,
          dataType: 'json',
          success: function(response) {
            if (response.error) {
              $('.alert').hide();
              $('.alert-danger').show();
              $('.message').html(response.message);
            } else {
              $('.alert').hide();
              $('.alert-success').show();
              $('.message').html(response.message);
              $('#employee').val('');
            }
          }
        });
      });

      // Enfocar automáticamente el campo de ID
      setTimeout(function() {
        $('#employee').focus();
      }, 500);

      // Efecto hover para el botón
      $('[name="signin"]').hover(
        function() {
          $(this).css('background', '#2980b9');
        },
        function() {
          $(this).css('background', '#3498db');
        }
      );
    });
  </script>

  <!-- Estilos adicionales -->
  <style>
    .login-page {
      background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ed 100%);
    }

    .form-control {
      transition: border-color 0.3s ease;
    }

    .form-control:focus {
      border-color: #3498db;
      box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .login-box-body {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .login-box-body:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
    }
  </style>
</body>

</html>