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
        Gestión de Empleados
        <small>Listado completo del personal</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Inicio</a></li>
        <li class="active">Empleados</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Notificaciones -->
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-ban'></i> ¡Atención!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check-circle'></i> ¡Éxito!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>
      
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Todos los Empleados</h3>
              <div class="box-tools pull-right">
                <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat">
                  <i class="fa fa-plus"></i> Nuevo Empleado
                </a>
              </div>
            </div>
            
            <div class="box-body">
              <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped table-hover">
                  <thead class="bg-primary">
                    <tr>
                      <th>ID</th>
                      <th>Foto</th>
                      <th>Nombre Completo</th>
                      <th>Posición</th>
                      <th>Horario</th>
                      <th>Fecha Ingreso</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $sql = "SELECT *, employees.id AS empid FROM employees LEFT JOIN position ON position.id=employees.position_id LEFT JOIN schedules ON schedules.id=employees.schedule_id";
                      $query = $conn->query($sql);
                      while($row = $query->fetch_assoc()){
                        ?>
                          <tr>
                            <td class="text-center"><?php echo $row['employee_id']; ?></td>
                            <td class="text-center">
                              <img src="<?php echo (!empty($row['photo']))? '../images/'.$row['photo']:'../images/profile.jpg'; ?>" 
                                   width="40px" height="40px" class="img-circle" 
                                   style="border: 2px solid #ddd; object-fit: cover;">
                              <a href="#edit_photo" data-toggle="modal" class="photo" data-id="<?php echo $row['empid']; ?>">
                                <small class="text-muted"><i class="fa fa-pencil"></i> editar</small>
                              </a>
                            </td>
                            <td><?php echo $row['firstname'].' '.$row['lastname']; ?></td>
                            <td><?php echo $row['description']; ?></td>
                            <td class="text-center">
                              <?php echo date('h:i A', strtotime($row['time_in'])).' - '.date('h:i A', strtotime($row['time_out'])); ?>
                            </td>
                            <td class="text-center"><?php echo date('d/m/Y', strtotime($row['created_on'])) ?></td>
                            <td class="text-center">
                              <div class="btn-group">
                                <button class="btn btn-sm btn-warning edit btn-flat" data-id="<?php echo $row['empid']; ?>">
                                  <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete btn-flat" data-id="<?php echo $row['empid']; ?>">
                                  <i class="fa fa-trash"></i>
                                </button>
                              </div>
                            </td>
                          </tr>
                        <?php
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
  <?php include 'includes/employee_modal.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<script>
$(function(){
  // Evitar doble inicialización de DataTable
  if ( $.fn.DataTable.isDataTable('#example1') ) {
    $('#example1').DataTable().destroy();
  }
  $('#example1').DataTable({
    "language": {
      "lengthMenu": "Mostrar _MENU_ registros por página",
      "zeroRecords": "No se encontraron resultados",
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
    "order": [[2, "asc"]]
  });

  // Funciones originales mantenidas
  $('.edit').click(function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $('.delete').click(function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $('.photo').click(function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });

});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'employee_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.empid').val(response.empid);
      $('.employee_id').html(response.employee_id);
      $('.del_employee_name').html(response.firstname+' '+response.lastname);
      $('#employee_name').html(response.firstname+' '+response.lastname);
      $('#edit_firstname').val(response.firstname);
      $('#edit_lastname').val(response.lastname);
      $('#edit_address').val(response.address);
      $('#datepicker_edit').val(response.birthdate);
      $('#edit_contact').val(response.contact_info);
      $('#gender_val').val(response.gender).html(response.gender);
      $('#position_val').val(response.position_id).html(response.description);
      $('#schedule_val').val(response.schedule_id).html(response.time_in+' - '+response.time_out);
    }
  });
}
</script>

<!-- Estilos adicionales -->
<style>
  .box-primary {
    border-top-color: #3c8dbc;
    box-shadow: 0 1px 10px rgba(0,0,0,0.1);
  }
  
  .table th {
    background-color: #3c8dbc !important;
    color: white;
  }
  
  .img-circle {
    transition: transform 0.3s ease;
  }
  
  .img-circle:hover {
    transform: scale(1.1);
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
</style>
</body>
</html>