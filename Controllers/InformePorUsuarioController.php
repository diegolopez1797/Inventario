<?php
/**
*
*/
if (isset($_SESSION['usuario'])) {

	class InformePorUsuarioController
{

	function __construct()
	{

	}

	function show(){
		if (!Permiso::usuarioPuede('dashboard.ver')) {
			flash('danger', 'No tiene permiso para ver este informe.');
			echo "<script>window.location.href = '?controller=Material&action=index';</script>";
			return;
		}

		$fechaInicial = isset($_GET['fechaInicial']) ? $_GET['fechaInicial'] : '';
		$fechaFinal = isset($_GET['fechaFinal']) ? $_GET['fechaFinal'] : '';

		$resumen = InformePorUsuario::resumen($fechaInicial ?: null, $fechaFinal ?: null);

		require_once('Views/Informes/InformePorUsuario.php');
	}

	function detalle(){
		if (!Permiso::usuarioPuede('dashboard.ver')) {
			flash('danger', 'No tiene permiso para ver este informe.');
			echo "<script>window.location.href = '?controller=Material&action=index';</script>";
			return;
		}

		$usuarioId = $_GET['usuario'];
		$fechaInicial = isset($_GET['fechaInicial']) ? $_GET['fechaInicial'] : '';
		$fechaFinal = isset($_GET['fechaFinal']) ? $_GET['fechaFinal'] : '';

		$movimientos = InformePorUsuario::detalleUsuario($usuarioId, $fechaInicial ?: null, $fechaFinal ?: null);
		$usuario = Usuario::searchByCodigoUser($usuarioId);

		require_once('Views/Informes/InformePorUsuarioDetalle.php');
	}

	function error(){
		require_once('Views/Material/error.php');
	}

}

}else{
	echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}

?>
