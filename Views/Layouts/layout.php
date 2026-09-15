<?php

require_once('connection.php');
require_once('Csrf.php');
require_once('Html.php');
require_once('Model/Usuario.php');
require_once('Model/MaterialRegistroEntradas.php');
require_once('Model/Material.php');
require_once('Model/Contratista.php');
require_once('Model/Casa.php');
require_once('Model/Rubro.php');
require_once('Model/Manzana.php');
require_once('Model/Area.php');
require_once('Model/Destino.php');
require_once('Model/Proyecto.php');
require_once('Model/Rol.php');
require_once('Model/MaterialRegistroSalidas.php');
require_once('Model/InformeDetallado.php');
require_once('Model/InformeMaterialEntrada.php');
require_once('Model/InformeMaterialSalida.php');
require_once('Model/RegistroEntradas.php');
require_once('Model/RegistroSalidas.php');
require_once('Model/Auditoria.php');
require_once('Model/Ubicacion.php');
require_once('Model/TipoUbicacion.php');
require_once('Model/Proveedor.php');
require_once('Model/Almacen.php');
require_once('Model/Dashboard.php');
require_once('Model/Kardex.php');
require_once('Model/InformePorUsuario.php');
require_once('Model/Permiso.php');
require_once('Model/Solicitud.php');
require_once('Model/AjusteInventario.php');
require_once('Model/Movimientos.php');
require_once('Model/NotificacionDestinatario.php');
require('PHPMailer/Exception.php');
require('PHPMailer/PHPMailer.php');
require('PHPMailer/SMTP.php');



//error_reporting(0);
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Berdez s.a.s</title>
	<meta http-equiv=”Content-Type” content=”text/html; charset=UTF-8″ />

	<?php //<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css"> ?>
	<script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
	<script type="text/javascript" src="assets/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>


	<link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
	<link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css'>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>

	
	<?php

	if (!isset($_SESSION['usuario'])) {
		echo '<link rel="stylesheet" href="css/styleLogin.css" type="text/css">';
	}

	?>
  	<link rel="stylesheet" href="css/cabecera.css" type="text/css">

</head>
<body>

	<header>
		<?php
			if (isset($_SESSION['usuario'])) {
		 		require_once('cabeceraNueva.php');
		 	}
		?>

	</header>
	<?php flash_render(); ?>
	<section>
		<?php require_once('routing.php'); ?>
	</section>

</body>
</html>