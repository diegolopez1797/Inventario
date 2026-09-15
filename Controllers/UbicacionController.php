<?php
/**
*
*/
if (isset($_SESSION['usuario'])) {

	class UbicacionController
{

	function __construct()
	{

	}

	function show(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este catálogo.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$listaProyecto = Proyecto::all();
		require_once('Views/Ubicacion/show.php');
	}

	function register(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$listaTipoUbicacion = TipoUbicacion::all();
		require_once('Views/Ubicacion/register.php');
	}

	function save(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$padreId = (!empty($_POST['padre_id'])) ? $_POST['padre_id'] : null;
		$tipoIngresado = ($_POST['tipo'] === '__otro__') ? trim($_POST['tipo_otro']) : $_POST['tipo'];
		$tipo = TipoUbicacion::normalizar($tipoIngresado);

		try {
			$ubicacion = new Ubicacion(null, $_POST['proyecto_id'], $padreId, trim($_POST['nombre']), $tipo, 1);
			Ubicacion::save($ubicacion);

			$nuevoId = Db::getConnect()->lastInsertId();
			Auditoria::registrar('Ubicacion', $nuevoId, 'CREAR', $_SESSION['usuario']->getId(), null, [
				'ProyectoID' => $ubicacion->getProyectoId(),
				'PadreID' => $ubicacion->getPadreId(),
				'Nombre' => $ubicacion->getNombre(),
				'Tipo' => $ubicacion->getTipo(),
			]);
			flash_now('success', 'Ubicación creada exitosamente.');
		} catch (PDOException $e) {
			flash_now('danger', 'Ya existe una ubicación con ese nombre en este mismo nivel.');
		} catch (Exception $e) {
			flash_now('danger', 'No se ha podido guardar la ubicación: ' . $e->getMessage());
		}

		$this->show();
	}

	function updateshow(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$ubicacion = Ubicacion::searchById($id);
		$listaTipoUbicacion = TipoUbicacion::all();
		$tieneMovimientos = Ubicacion::tieneMovimientos($id);

		$listaPadresElegibles = [];
		if (!$tieneMovimientos) {
			foreach (Ubicacion::todosPorProyecto($ubicacion->getProyectoId()) as $candidato) {
				if ((int) $candidato->getId() === (int) $id) continue;
				if (Ubicacion::generariaCiclo($id, $candidato->getId())) continue;
				$listaPadresElegibles[] = $candidato;
			}
		}

		require_once('Views/Ubicacion/updateshow.php');
	}

	function update(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$antes = Ubicacion::searchById($_POST['id']);
		$tipoIngresado = ($_POST['tipo'] === '__otro__') ? trim($_POST['tipo_otro']) : $_POST['tipo'];
		$tipo = TipoUbicacion::normalizar($tipoIngresado);

		$nuevoPadreId = $antes->getPadreId();
		if (isset($_POST['padre_id'])) {
			$solicitado = ($_POST['padre_id'] === '') ? null : $_POST['padre_id'];
			if ((string) $solicitado !== (string) $antes->getPadreId()) {
				if (Ubicacion::tieneMovimientos($antes->getId())) {
					flash_now('danger', 'Esta ubicación tiene movimientos históricos y no puede cambiar de nivel superior.');
					$this->show();
					return;
				}
				if (Ubicacion::generariaCiclo($antes->getId(), $solicitado)) {
					flash_now('danger', 'Ese cambio crearía un ciclo en el árbol de ubicaciones.');
					$this->show();
					return;
				}
				if ($solicitado !== null) {
					$nuevoPadre = Ubicacion::searchById($solicitado);
					if ((int) $nuevoPadre->getProyectoId() !== (int) $antes->getProyectoId()) {
						flash_now('danger', 'Una ubicación no puede pasar a otro proyecto.');
						$this->show();
						return;
					}
				}
				$nuevoPadreId = $solicitado;
			}
		}

		try {
			$ubicacion = new Ubicacion($_POST['id'], $antes->getProyectoId(), $nuevoPadreId, trim($_POST['nombre']), $tipo, 1);
			Ubicacion::update($ubicacion);

			Auditoria::registrar('Ubicacion', $ubicacion->getId(), 'EDITAR', $_SESSION['usuario']->getId(),
				['Nombre' => $antes->getNombre(), 'Tipo' => $antes->getTipo(), 'PadreID' => $antes->getPadreId()],
				['Nombre' => $ubicacion->getNombre(), 'Tipo' => $ubicacion->getTipo(), 'PadreID' => $ubicacion->getPadreId()]
			);
		} catch (PDOException $e) {
			flash_now('danger', 'Ya existe una ubicación con ese nombre en este mismo nivel.');
		} catch (Exception $e) {
			flash_now('danger', 'No se ha podido actualizar la ubicación: ' . $e->getMessage());
		}

		$this->show();
	}

	function delete(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];

		try {
			$antes = Ubicacion::searchById($id);
			Ubicacion::delete($id);
			Auditoria::registrar('Ubicacion', $id, 'ELIMINAR', $_SESSION['usuario']->getId(), [
				'ProyectoID' => $antes->getProyectoId(),
				'PadreID' => $antes->getPadreId(),
				'Nombre' => $antes->getNombre(),
				'Tipo' => $antes->getTipo(),
			], null);
		} catch (Exception $e) {
			flash_now('danger', 'No se puede eliminar esta ubicación (puede tener niveles hijos o movimientos asociados). Puede desactivarla en su lugar.');
		}

		$this->show();
	}

	function desactivar(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$antes = Ubicacion::searchById($id);
		Ubicacion::desactivar($id);

		Auditoria::registrar('Ubicacion', $id, 'DESACTIVAR', $_SESSION['usuario']->getId(),
			['Nombre' => $antes->getNombre(), 'Activo' => $antes->getActivo()],
			['Activo' => 0]
		);

		flash_now('success', 'Ubicación desactivada. Deja de aparecer en selectores nuevos pero conserva su historial.');
		$this->show();
	}

	// --- Generacion masiva: config -> vista previa obligatoria -> confirmar (una sola operacion) ---

	private function leerNivelesDesdePost(){
		$niveles = [];
		for ($i = 1; $i <= 3; $i++) {
			$cantidad = isset($_POST["nivel{$i}_cantidad"]) ? trim($_POST["nivel{$i}_cantidad"]) : '';
			if ($cantidad === '' || (int) $cantidad <= 0) {
				continue;
			}
			$tipoIngresado = ($_POST["nivel{$i}_tipo"] === '__otro__') ? trim($_POST["nivel{$i}_tipo_otro"]) : $_POST["nivel{$i}_tipo"];
			$niveles[] = [
				'tipo' => $tipoIngresado,
				'cantidad' => (int) $cantidad,
				'patron' => trim($_POST["nivel{$i}_patron"]),
			];
		}
		return $niveles;
	}

	function generarMasivoShow(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$proyectoId = (int) $_GET['proyecto'];
		$padreId = isset($_GET['padre']) && $_GET['padre'] !== '' ? (int) $_GET['padre'] : null;

		$proyecto = Proyecto::searchById($proyectoId);
		$padre = $padreId !== null ? Ubicacion::searchById($padreId) : null;
		$listaTipoUbicacion = TipoUbicacion::all();

		require_once('Views/Ubicacion/generarMasivoShow.php');
	}

	function generarMasivoPreview(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$proyectoId = (int) $_POST['proyecto_id'];
		$padreId = ($_POST['padre_id'] !== '') ? (int) $_POST['padre_id'] : null;
		$nombreContenedorNuevo = trim($_POST['contenedor_nombre']);
		$tipoContenedorIngresado = ($_POST['contenedor_tipo'] === '__otro__') ? trim($_POST['contenedor_tipo_otro']) : $_POST['contenedor_tipo'];

		$proyecto = Proyecto::searchById($proyectoId);

		if ($padreId !== null) {
			$padre = Ubicacion::searchById($padreId);
			if ((int) $padre->getProyectoId() !== $proyectoId) {
				flash_now('danger', 'El nivel superior indicado no pertenece al proyecto seleccionado.');
				$this->show();
				return;
			}
		} else {
			$padre = null;
		}

		$niveles = $this->leerNivelesDesdePost();
		if (empty($niveles)) {
			flash_now('warning', 'Debe indicar al menos un nivel a generar, con una cantidad mayor a cero.');
			$this->generarMasivoShowConDatos($proyectoId, $padreId);
			return;
		}

		try {
			$resumen = Ubicacion::previsualizarEstructura($proyectoId, $padreId, $nombreContenedorNuevo, $niveles);
		} catch (Exception $e) {
			flash_now('danger', $e->getMessage());
			$this->generarMasivoShowConDatos($proyectoId, $padreId);
			return;
		}

		require_once('Views/Ubicacion/generarMasivoPreview.php');
	}

	private function generarMasivoShowConDatos($proyectoId, $padreId){
		$proyecto = Proyecto::searchById($proyectoId);
		$padre = $padreId !== null ? Ubicacion::searchById($padreId) : null;
		$listaTipoUbicacion = TipoUbicacion::all();
		require_once('Views/Ubicacion/generarMasivoShow.php');
	}

	function generarMasivoConfirmar(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$proyectoId = (int) $_POST['proyecto_id'];
		$padreId = ($_POST['padre_id'] !== '') ? (int) $_POST['padre_id'] : null;
		$nombreContenedorNuevo = trim($_POST['contenedor_nombre']);
		$tipoContenedorIngresado = ($_POST['contenedor_tipo'] === '__otro__') ? trim($_POST['contenedor_tipo_otro']) : $_POST['contenedor_tipo'];

		if ($padreId !== null) {
			$padre = Ubicacion::searchById($padreId);
			if ((int) $padre->getProyectoId() !== $proyectoId) {
				flash_now('danger', 'El nivel superior indicado no pertenece al proyecto seleccionado.');
				$this->show();
				return;
			}
		}

		$niveles = $this->leerNivelesDesdePost();

		try {
			$resultado = Ubicacion::generarEstructura($proyectoId, $padreId, $nombreContenedorNuevo, $tipoContenedorIngresado, $niveles, $_SESSION['usuario']->getId());
			flash_now('success', 'Estructura generada exitosamente. Total de ubicaciones creadas: ' . (int) $resultado['totalGenerado'] . '.');
		} catch (Exception $e) {
			flash_now('danger', 'No se pudo generar la estructura: ' . $e->getMessage());
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
