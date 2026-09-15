<?php
/**
* Ajustes de inventario: apertura, conteo, perdida, dano. Reutiliza
* AjusteInventario::registrar() (transaccional) - este Controller solo valida
* forma de entrada y permiso, nunca calcula saldo ni toca material directamente.
*/
if (isset($_SESSION['usuario'])) {

	class AjusteInventarioController
{

	const TAMANO_PAGINA = 25;

	function __construct()
	{

	}

	// Permiso de vista: inventario.ajustar, sin cambios (revertido en la auditoria de la Fase 3
	// tras verificar que catalogo.ver esta asignado a los 6 roles del sistema - habria expuesto
	// perdidas/danos a roles que hoy no los ven, sin decision de negocio que lo respalde).
	function show(){
		if (!Permiso::usuarioPuede('inventario.ajustar')) {
			flash('danger', 'No tiene permiso para ajustar inventario.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
		$materialId = isset($_GET['materialId']) ? $_GET['materialId'] : '';
		$usuarioId = isset($_GET['usuarioId']) ? $_GET['usuarioId'] : '';
		$fechaInicial = isset($_GET['fechaInicial']) ? $_GET['fechaInicial'] : '';
		$fechaFinal = isset($_GET['fechaFinal']) ? $_GET['fechaFinal'] : '';

		$pagina = isset($_GET['pagina']) ? max(1, (int) $_GET['pagina']) : 1;
		$totalAjustes = AjusteInventario::contarTotal($tipo, $materialId, $usuarioId, $fechaInicial, $fechaFinal);
		$totalPaginas = max(1, (int) ceil($totalAjustes / self::TAMANO_PAGINA));
		$pagina = min($pagina, $totalPaginas);

		$listaAjuste = AjusteInventario::paginado($pagina, self::TAMANO_PAGINA, $tipo, $materialId, $usuarioId, $fechaInicial, $fechaFinal);
		$listaMaterialCompleta = Material::all();
		$listaUsuario = Usuario::all();

		require_once('Views/AjusteInventario/show.php');
	}

	function register(){
		if (!Permiso::usuarioPuede('inventario.ajustar')) {
			flash('danger', 'No tiene permiso para ajustar inventario.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
		if (!in_array($tipo, AjusteInventario::TIPOS_VALIDOS)) {
			flash('warning', 'Seleccione un tipo de ajuste válido.');
			echo "<script>window.location.href = '?controller=AjusteInventario&action=show';</script>";
			return;
		}

		$listaMaterialCompleta = Material::all();

		// Paso 1: elegir material (sin recargar via JS, solo navegacion GET). Paso 2:
		// una vez elegido, se muestra saldo actual y el campo especifico del tipo.
		$material = null;
		if (!empty($_GET['material'])) {
			$material = Material::searchById($_GET['material']);
			$tieneMovimientosPrevios = AjusteInventario::existeApertura($material->getId());
		}

		require_once('Views/AjusteInventario/register.php');
	}

	function save(){
		if (!Permiso::usuarioPuede('inventario.ajustar')) {
			flash('danger', 'No tiene permiso para ajustar inventario.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$materialId = isset($_POST['material']) ? $_POST['material'] : null;
		$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
		$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
		$valor = isset($_POST['valor']) ? $_POST['valor'] : null;
		$costoUnitario = (isset($_POST['costoUnitario']) && $_POST['costoUnitario'] !== '') ? $_POST['costoUnitario'] : null;

		if (empty($materialId) || !in_array($tipo, AjusteInventario::TIPOS_VALIDOS) || $motivo === '' || $valor === null || $valor === '' || !is_numeric($valor) || $valor < 0) {
			flash('warning', 'Complete todos los campos obligatorios con valores válidos.');
			echo "<script>window.location.href = '?controller=AjusteInventario&action=show';</script>";
			return;
		}

		if ($costoUnitario !== null && (!is_numeric($costoUnitario) || $costoUnitario < 0)) {
			flash_now('warning', 'El costo unitario ingresado no es válido.');
			$this->show();
			return;
		}

		try {
			AjusteInventario::registrar($materialId, $_SESSION['usuario']->getId(), $tipo, $valor, $costoUnitario, $motivo);
			flash_now('success', 'Ajuste registrado exitosamente.');
		} catch (Exception $e) {
			flash_now('danger', $e->getMessage());
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
