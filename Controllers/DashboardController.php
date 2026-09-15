<?php
/**
*
*/
if (isset($_SESSION['usuario'])) {

	class DashboardController
{

	function __construct()
	{

	}

	function show(){
		if (!Permiso::usuarioPuede('dashboard.ver')) {
			flash('danger', 'No tiene permiso para ver el dashboard.');
			echo "<script>window.location.href = '?controller=Material&action=index';</script>";
			return;
		}

		$materialesCriticos = Dashboard::materialesCriticos();
		$movimientosRecientes = Dashboard::movimientosRecientes();
		$proyectosActivos = Dashboard::proyectosActivos();

		require_once('Views/Dashboard/show.php');
	}

	function error(){
		require_once('Views/Material/error.php');
	}

}

}else{
	echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}

?>
