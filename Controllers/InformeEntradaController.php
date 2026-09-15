<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class InformeEntradaController
{

	function __construct()
	{

	}

	// El listado propio de este informe (show/search/eliminar/generarEntradaPDF) se elimino -
	// Movimientos lo reemplaza. detalle() se conserva porque Movimientos, Kardex, Por Usuario,
	// Salidas por Ubicacion y el Informe de Solicitudes reutilizan este drill-down para mostrar
	// el documento completo de una entrada (Fase 3 seccion 4: "reutilizar sin modificar").
	function detalle(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este documento.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$_SESSION['idEntrada'] = $id;
		$idUsuario = $_GET['usuario'];
		$usuario = Usuario::searchByCodigoUser($idUsuario);
		$materialRegistroEntradas = InformeMaterialEntrada::searchMaterialRegistroEntradas($id);
		$material = [];
		foreach ($materialRegistroEntradas as $entrada) {
			$material[] = Material::searchById($entrada->getMaterialId());
		}

		require_once('Views/Informes/DetalleEntrada.php');
	}

	// Reabre el mismo documento (usa el id guardado por detalle()) despues de generar el PDF -
	// ya no existe un show() al cual volver, porque el listado propio de este informe se elimino.
	function generarPDF(){
		echo "<script>window.open('Controllers/InformeEntradaMaterialPDF.php', '_blank')</script>";
		if (isset($_SESSION['idEntrada'])) {
			$registroEntradas = RegistroEntradas::searchEntrada($_SESSION['idEntrada']);
			echo "<script>window.location.href = '?controller=InformeEntrada&action=detalle&id=" . $_SESSION['idEntrada'] . "&usuario=" . $registroEntradas->getUsuario() . "';</script>";
		}
	}

	function error(){
		require_once('Views/Material/error.php');
	}

}
	
}else{
	echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}



?>