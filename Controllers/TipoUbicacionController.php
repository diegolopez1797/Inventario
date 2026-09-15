<?php
/**
*
*/
if (isset($_SESSION['usuario'])) {

	class TipoUbicacionController
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

		$listaTipoUbicacion = TipoUbicacion::all();
		require_once('Views/TipoUbicacion/show.php');
	}

	function register(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		require_once('Views/TipoUbicacion/register.php');
	}

	function save(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$tipo = new TipoUbicacion(null, null, trim($_POST['descripcion']), 1);
		$respuesta = TipoUbicacion::save($tipo);

		if (isset($respuesta)) {
			$nuevoId = Db::getConnect()->lastInsertId();
			Auditoria::registrar('TipoUbicacion', $nuevoId, 'CREAR', $_SESSION['usuario']->getId(), null, [
				'Descripcion' => $tipo->getDescripcion(),
			]);
			flash_now('success', 'Tipo de ubicación creado exitosamente.');
		} else {
			flash_now('danger', 'No se ha podido guardar el tipo de ubicación. Inténtelo nuevamente.');
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
		$tipo = TipoUbicacion::searchById($id);
		require_once('Views/TipoUbicacion/updateshow.php');
	}

	function update(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$antes = TipoUbicacion::searchById($_POST['id']);
		$tipo = new TipoUbicacion($_POST['id'], $antes->getCodigo(), trim($_POST['descripcion']), 1);
		TipoUbicacion::update($tipo);

		Auditoria::registrar('TipoUbicacion', $tipo->getId(), 'EDITAR', $_SESSION['usuario']->getId(),
			['Descripcion' => $antes->getDescripcion()],
			['Descripcion' => $tipo->getDescripcion()]
		);

		$this->show();
	}

	function desactivar(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$antes = TipoUbicacion::searchById($id);
		TipoUbicacion::desactivar($id);

		Auditoria::registrar('TipoUbicacion', $id, 'DESACTIVAR', $_SESSION['usuario']->getId(),
			['Descripcion' => $antes->getDescripcion(), 'Activo' => $antes->getActivo()],
			['Activo' => 0]
		);

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
