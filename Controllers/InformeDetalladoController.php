<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class InformeDetalladoController
{

	function __construct()
	{

	}

	// Salidas por Ubicación (Fase 2.1 seccion 6-7 #3 / Fase 3 seccion 8): agrupa por Ubicacion
	// y Material dentro de un Proyecto (obligatorio). Ubicacion es el unico concepto de lugar
	// usado aqui - Casa/Manzana/Area no aparecen en ningun filtro ni columna nueva.
	function show(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este informe.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$listaProyectoCompleta = Proyecto::all();
		$listaMaterialCompleta = Material::all();
		$listaRubro = Rubro::all();

		$proyectoId = isset($_GET['proyectoId']) ? $_GET['proyectoId'] : '';
		$ubicacionId = isset($_GET['ubicacionId']) ? $_GET['ubicacionId'] : '';
		$materialId = isset($_GET['materialId']) ? $_GET['materialId'] : '';
		$rubroId = isset($_GET['rubroId']) ? $_GET['rubroId'] : '';
		$fechaInicial = isset($_GET['fechaInicial']) ? $_GET['fechaInicial'] : '';
		$fechaFinal = isset($_GET['fechaFinal']) ? $_GET['fechaFinal'] : '';

		$listaUbicacionProyecto = !empty($proyectoId) ? Ubicacion::hojasConRuta($proyectoId) : [];
		$rutaPorUbicacionId = array_column($listaUbicacionProyecto, 'ruta', 'id');

		$grupos = [];
		if (!empty($proyectoId)) {
			$filtros = [
				'ubicacionId' => $ubicacionId,
				'materialId' => $materialId,
				'rubroId' => $rubroId,
				'fechaInicial' => $fechaInicial,
				'fechaFinal' => $fechaFinal,
			];
			$grupos = InformeDetallado::porUbicacion($proyectoId, $filtros);

			foreach ($grupos as &$grupo) {
				$grupo['UbicacionRuta'] = !empty($grupo['UbicacionID']) && isset($rutaPorUbicacionId[$grupo['UbicacionID']])
					? $rutaPorUbicacionId[$grupo['UbicacionID']]
					: 'Sin ubicación registrada';
			}
			unset($grupo);

			usort($grupos, function($a, $b){
				$cmp = strcmp($a['UbicacionRuta'], $b['UbicacionRuta']);
				return $cmp !== 0 ? $cmp : ($b['CantidadTotal'] <=> $a['CantidadTotal']);
			});

			$_SESSION['informeSalidaPorUbicacionGeneral'] = $grupos;
			$_SESSION['informeSalidaPorUbicacionProyecto'] = Proyecto::searchById($proyectoId);
		}

		require_once('Views/Informes/InformeDetallado.php');
	}

	// Drill-down: lineas individuales detras de un grupo (Proyecto+Ubicacion+Material).
	function detalle(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este informe.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$proyectoId = $_GET['proyecto'];
		$ubicacionId = isset($_GET['ubicacion']) && $_GET['ubicacion'] !== '' ? $_GET['ubicacion'] : null;
		$materialId = $_GET['material'];

		$material = Material::searchById($materialId);
		$proyecto = Proyecto::searchById($proyectoId);
		$ubicacionRuta = $ubicacionId !== null ? Ubicacion::ruta($ubicacionId) : 'Sin ubicación registrada';

		$lineas = InformeDetallado::lineasPorUbicacionMaterial($proyectoId, $ubicacionId, $materialId);

		require_once('Views/Informes/InformeDetalladoMaterial.php');
	}

	function generarPDF(){
		echo "<script>window.open('Controllers/InformeSalidaPorUbicacionPDF.php', '_blank')</script>";
		$this->show();
	}

	function error(){
		require_once('Views/Material/error.php');
	}

}
	
}else{
	echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}



?>