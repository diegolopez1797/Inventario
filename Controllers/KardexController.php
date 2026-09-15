<?php
/**
*
*/
if (isset($_SESSION['usuario'])) {

	class KardexController
{

	const TAMANO_PAGINA = 25;

	function __construct()
	{

	}

	function show(){
		if (!Permiso::usuarioPuede('dashboard.ver')) {
			flash('danger', 'No tiene permiso para ver el kardex.');
			echo "<script>window.location.href = '?controller=Material&action=index';</script>";
			return;
		}

		$listaMaterialCompleta = Material::all();
		// hojasConRuta() sin proyecto devuelve TODAS las ubicaciones hoja del sistema (con su
		// ruta "Proyecto > ... > Nombre") - un material puede haber salido a cualquier proyecto
		// a lo largo de su historia, no solo al que se este viendo en otra pantalla.
		$listaUbicacion = Ubicacion::hojasConRuta();

		$fechaInicial = isset($_GET['fechaInicial']) ? $_GET['fechaInicial'] : '';
		$fechaFinal = isset($_GET['fechaFinal']) ? $_GET['fechaFinal'] : '';
		$ubicacionId = isset($_GET['ubicacionId']) ? $_GET['ubicacionId'] : '';
		$pagina = isset($_GET['pagina']) ? max(1, (int) $_GET['pagina']) : 1;

		if (isset($_SESSION['kardexMovimientosCompleto'])) {
			// El filtro se aplica SOLO aqui, sobre el resultado ya reconstruido por
			// Kardex::porMaterial() - nunca se vuelve a llamar a porMaterial() con un rango de
			// fechas, precisamente para no repetir el error corregido en la Fase 3.
			$movimientosFiltrados = Kardex::filtrar($_SESSION['kardexMovimientosCompleto'], $fechaInicial, $fechaFinal, $ubicacionId);

			$totalMovimientos = count($movimientosFiltrados);
			$totalPaginas = max(1, (int) ceil($totalMovimientos / self::TAMANO_PAGINA));
			$pagina = min($pagina, $totalPaginas);

			$movimientosPagina = array_slice($movimientosFiltrados, ($pagina - 1) * self::TAMANO_PAGINA, self::TAMANO_PAGINA);
		}

		require_once('Views/Kardex/show.php');
	}

	function buscar(){
		if (!Permiso::usuarioPuede('dashboard.ver')) {
			flash('danger', 'No tiene permiso para ver el kardex.');
			echo "<script>window.location.href = '?controller=Material&action=index';</script>";
			return;
		}

		if (!empty($_POST['idMaterial'])) {
			$idMaterial = $_POST['idMaterial'];
			$_SESSION['kardexMaterial'] = Material::searchById($idMaterial);
			// Conjunto COMPLETO y sin filtrar, tal como lo devuelve porMaterial() - los filtros
			// de fecha/ubicacion se aplican despues, en show(), nunca aqui.
			$_SESSION['kardexMovimientosCompleto'] = Kardex::porMaterial($idMaterial);
		}

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
