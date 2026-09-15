<?php
/**
*
*/
if (isset($_SESSION['usuario'])) {

	class AuditoriaController
{

	const TAMANO_PAGINA = 25;

	function __construct()
	{

	}

	function show(){
		if (!Permiso::usuarioPuede('auditoria.ver')) {
			flash('danger', 'No tiene permiso para ver la auditoría.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$usuarioId = isset($_GET['usuarioId']) ? $_GET['usuarioId'] : '';
		$entidad = isset($_GET['entidad']) ? $_GET['entidad'] : '';
		$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
		$fechaInicial = isset($_GET['fechaInicial']) ? $_GET['fechaInicial'] : '';
		$fechaFinal = isset($_GET['fechaFinal']) ? $_GET['fechaFinal'] : '';

		$pagina = isset($_GET['pagina']) ? max(1, (int) $_GET['pagina']) : 1;
		$totalEventos = Auditoria::contarTotal($usuarioId, $entidad, $accion, $fechaInicial, $fechaFinal);
		$totalPaginas = max(1, (int) ceil($totalEventos / self::TAMANO_PAGINA));
		$pagina = min($pagina, $totalPaginas);

		$listaAuditoria = Auditoria::paginado($pagina, self::TAMANO_PAGINA, $usuarioId, $entidad, $accion, $fechaInicial, $fechaFinal);
		$listaUsuario = Usuario::all();
		$listaEntidad = Auditoria::entidadesRegistradas();
		$listaAccion = Auditoria::accionesRegistradas();

		require_once('Views/Auditoria/show.php');
	}

	function error(){
		require_once('Views/Material/error.php');
	}

}

}else{
	echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}

?>
