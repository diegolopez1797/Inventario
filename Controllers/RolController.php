<?php
/**
*
*/

if (isset($_SESSION['usuario'])) {


	class RolController
{

	function __construct()
	{

	}

	function show(){
		if (!Permiso::usuarioPuede('rol.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar roles.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$listaRol = Rol::all();

		require_once('Views/Rol/show.php');
	}

	function register(){
		if (!Permiso::usuarioPuede('rol.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar roles.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		require_once('Views/Rol/register.php');
	}

	function save(){
		if (!Permiso::usuarioPuede('rol.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar roles.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$rol = new Rol(null, $_POST['descripcion']);
		$respuesta = Rol::save($rol);
		if (isset($respuesta)) {
			$nuevoId = Db::getConnect()->lastInsertId();
			Auditoria::registrar('Rol', $nuevoId, 'CREAR', $_SESSION['usuario']->getId(), null, ['Descripcion' => $rol->getDescripcion()]);
			flash_now('success', 'Rol creado exitosamente.');
		} else {
			flash_now('danger', 'No se ha podido guardar el rol. Inténtelo nuevamente.');
		}
		$this->show();
	}

	function updateshow(){
		if (!Permiso::usuarioPuede('rol.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar roles.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$rol = Rol::searchById($id);
		require_once('Views/Rol/updateshow.php');
	}

	function update(){
		if (!Permiso::usuarioPuede('rol.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar roles.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$antes = Rol::searchById($_POST['id']);
		$rol = new Rol($_POST['id'], $_POST['descripcion']);
		Rol::update($rol);
		Auditoria::registrar('Rol', $rol->getId(), 'EDITAR', $_SESSION['usuario']->getId(), ['Descripcion' => $antes->getDescripcion()], ['Descripcion' => $rol->getDescripcion()]);
		$this->show();
	}

	function delete(){
		if (!Permiso::usuarioPuede('rol.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar roles.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];

		try {
			$antes = Rol::searchById($id);
			Rol::delete($id);
			Auditoria::registrar('Rol', $id, 'ELIMINAR', $_SESSION['usuario']->getId(), ['Descripcion' => $antes->getDescripcion()], null);
		} catch (Exception $e) {
			flash_now('danger', 'No se puede eliminar el rol, es posible que tenga usuarios o permisos asociados.');
		}

		$this->show();
	}

	function permisos(){
		if (!Permiso::usuarioPuede('rol.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar roles.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$rol = Rol::searchById($id);
		$listaPermiso = Permiso::all();
		$codigosAsignados = Permiso::codigosPorRol($id);

		require_once('Views/Rol/permisos.php');
	}

	function guardarPermisos(){
		if (!Permiso::usuarioPuede('rol.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar roles.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$rolId = $_POST['id'];
		$idsPermiso = isset($_POST['permisos']) ? $_POST['permisos'] : [];

		$antes = Permiso::codigosPorRol($rolId);
		Permiso::asignarARol($rolId, $idsPermiso);
		$despues = Permiso::codigosPorRol($rolId);

		Auditoria::registrar('RolPermiso', $rolId, 'EDITAR', $_SESSION['usuario']->getId(), ['Permisos' => $antes], ['Permisos' => $despues]);

		flash('success', 'Permisos actualizados exitosamente.');
		echo "<script>window.location.href = '?controller=Rol&action=show';</script>";
	}

	function error(){
		require_once('Views/Material/error.php');
	}

}

}else{
	echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}

?>
