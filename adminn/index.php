<?php
session_start();
if (isset($_SESSION['admin'])) {
	header('location:home.php');
}
?>
<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html>

<head>
	<title>Admin Login</title>
	<link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<style>
		*,
		*:before,
		*:after {
			box-sizing: border-box
		}

		body {
			min-height: 100vh;
			font-family: 'Raleway', sans-serif;
			margin: 0;
			padding: 0;
		}

		.container {
			position: absolute;
			width: 100%;
			height: 100%;
			overflow: hidden;
		}

		.container:hover .top:before,
		.container:hover .top:after,
		.container:hover .bottom:before,
		.container:hover .bottom:after {
			margin-left: 200px;
			transform-origin: -200px 50%;
			transition-delay: 0s;
		}

		.container:hover .center {
			opacity: 1;
			transition-delay: 0.2s;
		}

		.top:before,
		.top:after,
		.bottom:before,
		.bottom:after {
			content: '';
			display: block;
			position: absolute;
			width: 200vmax;
			height: 200vmax;
			top: 50%;
			left: 50%;
			margin-top: -100vmax;
			transform-origin: 0 50%;
			transition: all 0.5s cubic-bezier(0.445, 0.05, 0, 1), 
			background 0.3s ease;
			z-index: 10;
			opacity: 0.65;
			transition-delay: 0.2s;
		}

		.top:before {
			transform: rotate(45deg);
			background: #78909c;
		}

		.top:after {
			transform: rotate(135deg);
			background: #b0bec5;
		}

		.bottom:before {
			transform: rotate(-45deg);
			background: #80deea;
		}

		.bottom:after {
			transform: rotate(-135deg);
			background: #4dd0e1;
		}

		.center {
			position: absolute;
			width: 400px;
			height: 400px;
			top: 50%;
			left: 50%;
			margin-left: -200px;
			margin-top: -200px;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			padding: 30px;
			opacity: 0;
			transition: all 0.5s cubic-bezier(0.445, 0.05, 0, 1);
			transition-delay: 0s;
			color: #333;
			background: rgba(255, 255, 255, 0.9);
			border-radius: 8px;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
			z-index: 20;
		}

		.center input {
			width: 100%;
			padding: 15px;
			margin: 5px;
			border-radius: 1px;
			border: 1px solid #ccc;
			font-family: inherit;
		}

		.center h2 {
			margin-bottom: 30px;
			color: #3745b5;
		}

		.btn-login {
			width: 100%;
			padding: 15px;
			margin: 5px;
			background: #3745b5;
			color: white;
			border: none;
			cursor: pointer;
			font-family: 'Raleway', sans-serif;
			font-weight: 700;
			transition: background 0.3s;
		}

		.btn-login:hover {
			background: #2934a1;
		}

		.input-icon {
			position: relative;
			width: 100%;
		}

		.input-icon i {
			position: absolute;
			left: 15px;
			top: 15px;
			color: #999;
		}

		.input-icon input {
			padding-left: 40px !important;
		}

		.error-message {
			color: #e46569;
			margin-top: 10px;
			text-align: center;
			width: 100%;
		}
	</style>
</head>

<body>

	<div class="container">
		<div class="top"></div>
		<div class="bottom"></div>
		<div class="center">
			<h2>Ingreso Administrador</h2>

			<form action="login.php" method="POST">
				<div class="input-icon">
					<i class="fa fa-user"></i>
					<input type="text" name="username" placeholder="Usuario" required autofocus>
				</div>

				<div class="input-icon">
					<i class="fa fa-lock"></i>
					<input type="password" name="password" placeholder="Contraseña" required>
				</div>

				<button type="submit" class="btn-login" name="login">INGRESAR</button>
			</form>

			<?php
			if (isset($_SESSION['error'])) {
				echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
				unset($_SESSION['error']);
			}
			?>
		</div>
	</div>

	<?php include 'includes/scripts.php' ?>
</body>

</html>