<?php
/**
* Flujo de Solicitudes: crear (solicitud.crear), aprobar/rechazar (segun
* Solicitud::usuarioPuedeAprobar, no un permiso plano) y entregar (solicitud.entregar,
* reutilizando RegistroSalidas::registrar()).
*/
if (isset($_SESSION['usuario'])) {

	class SolicitudController
{

	const TAMANO_PAGINA = 25;

	function __construct()
	{

	}

	// Informe de Solicitudes (Fase 2.1 seccion 6-7 #8 / Fase 3 seccion 13): NO reemplaza ni
	// modifica show() (la bandeja operativa "mis solicitudes / pendientes de aprobar / aprobadas
	// para entregar" sigue exactamente igual) - es una accion nueva y separada, de solo lectura,
	// con filtros combinables. No se restringe por usuario segun rol: cualquiera con
	// solicitud.ver ve todas las solicitudes que cumplan el filtro (ver nota en el reporte final
	// sobre el alcance "solo mis solicitudes" para Residente, que Fase 2.1 sugiere pero no define
	// como implementarlo sin una regla de negocio explicita).
	function reporte(){
		if (!Permiso::usuarioPuede('solicitud.ver')) {
			flash('danger', 'No tiene permiso para ver solicitudes.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
		$proyectoId = isset($_GET['proyectoId']) ? $_GET['proyectoId'] : '';
		$usuarioSolicitaId = isset($_GET['usuarioSolicitaId']) ? $_GET['usuarioSolicitaId'] : '';
		$fechaInicial = isset($_GET['fechaInicial']) ? $_GET['fechaInicial'] : '';
		$fechaFinal = isset($_GET['fechaFinal']) ? $_GET['fechaFinal'] : '';

		$pagina = isset($_GET['pagina']) ? max(1, (int) $_GET['pagina']) : 1;
		$totalSolicitudes = Solicitud::contarTotal($estado, $proyectoId, $usuarioSolicitaId, $fechaInicial, $fechaFinal);
		$totalPaginas = max(1, (int) ceil($totalSolicitudes / self::TAMANO_PAGINA));
		$pagina = min($pagina, $totalPaginas);

		$listaSolicitud = Solicitud::paginado($pagina, self::TAMANO_PAGINA, $estado, $proyectoId, $usuarioSolicitaId, $fechaInicial, $fechaFinal);
		$conteoPorEstado = Solicitud::contarPorEstado($proyectoId, $usuarioSolicitaId, $fechaInicial, $fechaFinal);

		$listaProyecto = Proyecto::all();
		$listaUsuario = Usuario::all();

		require_once('Views/Solicitud/reporte.php');
	}

	function register(){
		if (!Permiso::usuarioPuede('solicitud.crear')) {
			flash('danger', 'No tiene permiso para crear solicitudes.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$listaProyecto = Proyecto::all();
		// Filtradas por el proyecto ya elegido en esta sesion (si aun no se elige ninguno, no
		// se ofrece ubicacion todavia para no mezclar ubicaciones de proyectos distintos).
		$listaUbicacion = isset($_SESSION['solicitudProyectoId']) && $_SESSION['solicitudProyectoId'] ? Ubicacion::hojasConRuta($_SESSION['solicitudProyectoId']) : [];
		require_once('Views/Solicitud/register.php');
	}

	function searchMaterial(){
		if (!Permiso::usuarioPuede('solicitud.crear')) {
			flash('danger', 'No tiene permiso para crear solicitudes.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$codigo = $_POST['codigo'];
		$cantidad = $_POST['cantidad'];

		if (empty($codigo) or $cantidad <= 0) {
			flash_now('warning', 'Ingrese un código válido y una cantidad mayor a cero.');
		} else {
			$material = Material::searchByCodigo($codigo);

			if ($material->getCodigo() == $codigo) {
				$listaMaterial = isset($_SESSION['solicitudMaterial']) ? $_SESSION['solicitudMaterial'] : [];
				$listaCantidad = isset($_SESSION['solicitudCantidad']) ? $_SESSION['solicitudCantidad'] : [];

				array_push($listaMaterial, $material);
				array_push($listaCantidad, $cantidad);

				$_SESSION['solicitudMaterial'] = $listaMaterial;
				$_SESSION['solicitudCantidad'] = $listaCantidad;

				$_SESSION['solicitudProyectoId'] = isset($_POST['proyecto']) ? $_POST['proyecto'] : (isset($_SESSION['solicitudProyectoId']) ? $_SESSION['solicitudProyectoId'] : null);
				$_SESSION['solicitudUbicacionId'] = isset($_POST['ubicacion']) && $_POST['ubicacion'] !== '' ? $_POST['ubicacion'] : (isset($_SESSION['solicitudUbicacionId']) ? $_SESSION['solicitudUbicacionId'] : null);
			} else {
				flash_now('warning', 'El material buscado no existe.');
			}
		}

		$this->register();
	}

	function quitarMaterial(){
		if (!Permiso::usuarioPuede('solicitud.crear')) {
			flash('danger', 'No tiene permiso para crear solicitudes.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$i = $_GET['id'];

		$listaMaterial = isset($_SESSION['solicitudMaterial']) ? $_SESSION['solicitudMaterial'] : [];
		$listaCantidad = isset($_SESSION['solicitudCantidad']) ? $_SESSION['solicitudCantidad'] : [];

		unset($listaMaterial[$i]);
		unset($listaCantidad[$i]);

		$_SESSION['solicitudMaterial'] = array_values($listaMaterial);
		$_SESSION['solicitudCantidad'] = array_values($listaCantidad);

		$this->register();
	}

	function save(){
		if (!Permiso::usuarioPuede('solicitud.crear')) {
			flash('danger', 'No tiene permiso para crear solicitudes.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$proyectoId = isset($_POST['proyecto']) ? $_POST['proyecto'] : $_SESSION['solicitudProyectoId'];
		$ubicacionId = !empty($_POST['ubicacion']) ? $_POST['ubicacion'] : (isset($_SESSION['solicitudUbicacionId']) ? $_SESSION['solicitudUbicacionId'] : null);

		if (empty($proyectoId) || empty($_SESSION['solicitudMaterial'])) {
			flash_now('warning', 'Seleccione un proyecto y agregue al menos un material antes de guardar.');
			$this->register();
			return;
		}

		$listaMaterialId = array_map(function($m){ return $m->getId(); }, $_SESSION['solicitudMaterial']);
		$listaCantidad = $_SESSION['solicitudCantidad'];

		try {
			Solicitud::crear($_SESSION['usuario']->getId(), $proyectoId, $ubicacionId, $listaMaterialId, $listaCantidad);

			unset($_SESSION['solicitudMaterial']);
			unset($_SESSION['solicitudCantidad']);
			unset($_SESSION['solicitudProyectoId']);
			unset($_SESSION['solicitudUbicacionId']);

			flash_now('success', 'Solicitud creada exitosamente.');
		} catch (Exception $e) {
			flash_now('danger', $e->getMessage());
		}

		$this->show();
	}

	function show(){
		if (!Permiso::usuarioPuede('solicitud.ver')) {
			flash('danger', 'No tiene permiso para ver solicitudes.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$usuarioActual = $_SESSION['usuario'];

		$misSolicitudes = Solicitud::porUsuarioSolicita($usuarioActual->getId());

		$pendientesParaAprobar = [];
		foreach (Solicitud::pendientes() as $solicitud) {
			if (Solicitud::usuarioPuedeAprobar($solicitud, $usuarioActual)) {
				$pendientesParaAprobar[] = $solicitud;
			}
		}

		$aprobadasParaEntregar = Permiso::usuarioPuede('solicitud.entregar') ? Solicitud::aprobadas() : [];

		require_once('Views/Solicitud/show.php');
	}

	function aprobar(){
		$id = $_GET['id'];
		$solicitud = Solicitud::searchById($id);

		if (!Solicitud::usuarioPuedeAprobar($solicitud, $_SESSION['usuario'])) {
			flash('danger', 'No tiene permiso para aprobar esta solicitud.');
			echo "<script>window.location.href = '?controller=Solicitud&action=show';</script>";
			return;
		}

		Solicitud::aprobar($id, $_SESSION['usuario']->getId());
		flash_now('success', 'Solicitud aprobada.');
		$this->show();
	}

	function rechazar(){
		$id = $_GET['id'];
		$solicitud = Solicitud::searchById($id);

		if (!Solicitud::usuarioPuedeAprobar($solicitud, $_SESSION['usuario'])) {
			flash('danger', 'No tiene permiso para rechazar esta solicitud.');
			echo "<script>window.location.href = '?controller=Solicitud&action=show';</script>";
			return;
		}

		Solicitud::rechazar($id, $_SESSION['usuario']->getId());
		flash_now('success', 'Solicitud rechazada.');
		$this->show();
	}

	function mostrarEntrega(){
		if (!Permiso::usuarioPuede('solicitud.entregar')) {
			flash('danger', 'No tiene permiso para entregar solicitudes.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$solicitud = Solicitud::searchById($id);

		if ($solicitud->getEstado() != 'APROBADA') {
			flash('danger', 'Esta solicitud no está en estado APROBADA.');
			echo "<script>window.location.href = '?controller=Solicitud&action=show';</script>";
			return;
		}

		$proyecto = Proyecto::searchById($solicitud->getProyectoId());
		$detalle = Solicitud::detalle($id);
		$listaContratista = Contratista::all();
		$listaCasa = Casa::all();
		$listaManzana = Manzana::all();
		$listaArea = Area::all();
		$listaDestino = Destino::all();
		$listaRubro = Rubro::all();

		require_once('Views/Solicitud/entrega.php');
	}

	function procesarEntrega(){
		if (!Permiso::usuarioPuede('solicitud.entregar')) {
			flash('danger', 'No tiene permiso para entregar solicitudes.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$solicitudId = $_POST['id'];
		$solicitud = Solicitud::searchById($solicitudId);

		if ($solicitud->getEstado() != 'APROBADA') {
			flash_now('danger', 'Esta solicitud no está en estado APROBADA.');
			$this->show();
			return;
		}

		if (empty($_POST['contratista']) || $_POST['contratista'] == 0) {
			flash_now('warning', 'Seleccione un contratista.');
			$this->show();
			return;
		}

		$detalle = Solicitud::detalle($solicitudId);

		// Casa/Manzana/Area/Destino/Rubro dejaron de ser obligatorios (Ubicacion es el destino
		// fisico principal); ya no se bloquea la entrega por dejarlos sin elegir.

		$listaMaterial = array_map(function($d){ return $d['material']; }, $detalle);
		$listaCantidad = array_map(function($d){ return $d['cantidad']; }, $detalle);

		$listaCasa = [];
		$listaManzana = [];
		$listaArea = [];
		$listaDestino = [];
		$listaRubro = [];
		$listaUbicacion = [];

		// La Ubicacion de la Solicitud (si tiene) se reutiliza para toda la entrega - nunca
		// se le vuelve a preguntar al Almacenero, ya quedo validada al crear la Solicitud.
		$ubicacionSolicitud = $solicitud->getUbicacionId() !== null ? Ubicacion::searchById($solicitud->getUbicacionId()) : null;

		foreach ($detalle as $i => $linea) {
			$listaCasa[] = (!empty($_POST['casa'][$i])) ? Casa::searchById($_POST['casa'][$i]) : null;
			$listaManzana[] = (!empty($_POST['manzana'][$i])) ? Manzana::searchById($_POST['manzana'][$i]) : null;
			$listaArea[] = (!empty($_POST['area'][$i])) ? Area::searchById($_POST['area'][$i]) : null;
			$listaDestino[] = (!empty($_POST['destino'][$i])) ? Destino::searchById($_POST['destino'][$i]) : null;
			$listaRubro[] = (!empty($_POST['rubro'][$i])) ? Rubro::searchById($_POST['rubro'][$i]) : null;
			$listaUbicacion[] = $ubicacionSolicitud;
		}

		try {
			$resultado = RegistroSalidas::registrar(
				date('Y-m-d'), date('H:i:s'), $_SESSION['usuario']->getId(),
				$_POST['contratista'], $solicitud->getProyectoId(),
				$listaMaterial, $listaCantidad,
				$listaCasa, $listaManzana, $listaArea, $listaDestino, $listaRubro,
				$listaUbicacion
			);

			if ($resultado['ok']) {
				Solicitud::marcarEntregada($solicitudId, $resultado['id'], $_SESSION['usuario']->getId());
				flash_now('success', 'Solicitud entregada exitosamente.');
			} else {
				foreach ($resultado['insuficientes'] as $insuficiente) {
					$descripcion = $insuficiente['descripcion'];
					$cantidadSaliente = $insuficiente['cantidadSolicitada'];
					$cantidadInventario = $insuficiente['saldoInventario'];
					$unidad = $insuficiente['unidad'];
					flash_now('warning', "La cantidad de {$descripcion} que existe en inventario es {$cantidadInventario} {$unidad} y usted desea sacar {$cantidadSaliente} {$unidad}. Por lo tanto no se puede entregar esta solicitud.");
				}
			}
		} catch (PDOException $e) {
			flash_now('danger', 'Ocurrió un error inesperado y la operación fue cancelada. Inténtelo nuevamente.');
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
