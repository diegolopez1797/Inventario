<?php
/**
* Informe Movimientos (Fase 2.1 / Fase 3 seccion 6). Reemplaza, cuando se limpien los informes
* obsoletos, a: Por No. entrada, Por fecha (entrada), Por material/fecha (entrada), Por No.
* salida, Por fecha (salida), Por material/fecha (salida) y Por contratista y fecha. El
* drill-down al documento completo reutiliza las acciones detalle() ya existentes de
* InformeEntradaController/InformeSalidaController (Fase 3 seccion 4: no se duplica esa vista).
*/
if (isset($_SESSION['usuario'])) {

	class MovimientosController
{

	const TAMANO_PAGINA = 25;

	function __construct()
	{

	}

	function show(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este informe.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$filtros = [
			'tipo' => isset($_GET['tipo']) ? $_GET['tipo'] : '',
			'fechaInicial' => isset($_GET['fechaInicial']) ? $_GET['fechaInicial'] : '',
			'fechaFinal' => isset($_GET['fechaFinal']) ? $_GET['fechaFinal'] : '',
			'materialId' => isset($_GET['materialId']) ? $_GET['materialId'] : '',
			'documento' => isset($_GET['documento']) ? $_GET['documento'] : '',
			'contratistaId' => isset($_GET['contratistaId']) ? $_GET['contratistaId'] : '',
			'proveedorId' => isset($_GET['proveedorId']) ? $_GET['proveedorId'] : '',
			'proyectoId' => isset($_GET['proyectoId']) ? $_GET['proyectoId'] : '',
			'usuarioId' => isset($_GET['usuarioId']) ? $_GET['usuarioId'] : '',
		];
		$orden = isset($_GET['orden']) ? $_GET['orden'] : 'fecha';
		$direccion = isset($_GET['direccion']) ? $_GET['direccion'] : 'DESC';

		// Se guarda en sesion (no solo en variable local) porque generarPDF() exporta exactamente
		// este mismo conjunto completo, sin paginar - mismo patron ya usado en los 6 informes de
		// movimiento migrados a array_slice esta sesion.
		$_SESSION['movimientosGeneral'] = Movimientos::buscar($filtros, $orden, $direccion);

		$totalRegistros = count($_SESSION['movimientosGeneral']);
		$totalPaginas = max(1, (int) ceil($totalRegistros / self::TAMANO_PAGINA));
		$pagina = isset($_GET['pagina']) ? max(1, (int) $_GET['pagina']) : 1;
		$pagina = min($pagina, $totalPaginas);

		$listaMaterialCompleta = Material::all();
		$listaContratista = Contratista::all();
		$listaProveedor = Proveedor::all();
		$listaProyecto = Proyecto::all();
		$listaUsuario = Usuario::all();

		require_once('Views/Movimientos/show.php');
	}

	function generarPDF(){
		echo "<script>window.open('Controllers/MovimientosPDF.php', '_blank')</script>";
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
