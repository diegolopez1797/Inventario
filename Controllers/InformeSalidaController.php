<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class InformeSalidaController
{

	function __construct()
	{

	}

	// El listado propio de este informe (show/search/eliminar/generarSalidaPDF) se elimino -
	// Movimientos lo reemplaza. detalle() se conserva porque Movimientos, Kardex, Por Usuario,
	// Salidas por Ubicacion y el Informe de Solicitudes reutilizan este drill-down para mostrar
	// el documento completo de una salida (Fase 3 seccion 4: "reutilizar sin modificar").
	function detalle(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este documento.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$_SESSION['idSalida'] = $id;
		$idUsuario = $_GET['usuario'];
		$usuario = Usuario::searchByCodigoUser($idUsuario);
		//print_r($usuario);
		$materialRegistroSalidas = InformeMaterialSalida::searchMaterialRegistroSalidas($id);
		$material = [];
		foreach ($materialRegistroSalidas as $salida) {
			$material[] = Material::searchById($salida->getMaterialId());
		}

		require_once('Views/Informes/DetalleSalida.php');
	}

	// Reabre el mismo documento (usa el id guardado por detalle()) despues de generar el PDF -
	// ya no existe un show() al cual volver, porque el listado propio de este informe se elimino.
	function generarPDF(){
		echo "<script>window.open('Controllers/InformeSalidaMaterialPDF.php', '_blank')</script>";
		if (isset($_SESSION['idSalida'])) {
			$registroSalidas = RegistroSalidas::searchSalida($_SESSION['idSalida']);
			echo "<script>window.location.href = '?controller=InformeSalida&action=detalle&id=" . $_SESSION['idSalida'] . "&usuario=" . $registroSalidas->getUsuario() . "';</script>";
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