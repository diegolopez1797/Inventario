<?php
/**
* Administracion de destinatarios de la alerta de stock minimo. Sin DELETE (baja logica
* unicamente, ver Auditoria y Propuesta - Gestion de Destinatarios de Notificaciones por Correo).
*/
if (isset($_SESSION['usuario'])) {

	class NotificacionDestinatarioController
{

	function __construct(){}

	function show(){
		if (!Permiso::usuarioPuede('notificacion.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$listaDestinatario = NotificacionDestinatario::all();
		require_once('Views/NotificacionDestinatario/show.php');
	}

	function register(){
		if (!Permiso::usuarioPuede('notificacion.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		require_once('Views/NotificacionDestinatario/register.php');
	}

	function save(){
		if (!Permiso::usuarioPuede('notificacion.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$nombre = trim($_POST['nombre']);
		$correo = trim($_POST['correo']);

		if ($nombre === '' || $correo === '') {
			flash_now('warning', 'Nombre y correo son obligatorios.');
			$this->show();
			return;
		}
		if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
			flash_now('warning', 'El correo ingresado no es válido.');
			$this->show();
			return;
		}
		if (NotificacionDestinatario::correoExiste($correo)) {
			flash_now('warning', 'Este correo ya está registrado.');
			$this->show();
			return;
		}

		try {
			$destinatario = new NotificacionDestinatario(null, $nombre, $correo, 1);
			NotificacionDestinatario::save($destinatario);
			$nuevoId = Db::getConnect()->lastInsertId();

			Auditoria::registrar('NotificacionDestinatario', $nuevoId, 'CREAR', $_SESSION['usuario']->getId(), null, [
				'Nombre' => $nombre,
				'Correo' => $correo,
			]);
			flash_now('success', 'Destinatario creado exitosamente.');
		} catch (PDOException $e) {
			flash_now('danger', 'No se ha podido guardar el destinatario. Verifique que el correo no esté repetido.');
		}

		$this->show();
	}

	function updateshow(){
		if (!Permiso::usuarioPuede('notificacion.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$destinatario = NotificacionDestinatario::searchById($id);
		require_once('Views/NotificacionDestinatario/updateshow.php');
	}

	function update(){
		if (!Permiso::usuarioPuede('notificacion.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_POST['id'];
		$nombre = trim($_POST['nombre']);
		$correo = trim($_POST['correo']);

		if ($nombre === '' || $correo === '') {
			flash_now('warning', 'Nombre y correo son obligatorios.');
			$this->show();
			return;
		}
		if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
			flash_now('warning', 'El correo ingresado no es válido.');
			$this->show();
			return;
		}
		if (NotificacionDestinatario::correoExiste($correo, $id)) {
			flash_now('warning', 'Este correo ya está registrado en otro destinatario.');
			$this->show();
			return;
		}

		$antes = NotificacionDestinatario::searchById($id);
		try {
			$destinatario = new NotificacionDestinatario($id, $nombre, $correo, $antes->getActivo());
			NotificacionDestinatario::update($destinatario);

			Auditoria::registrar('NotificacionDestinatario', $id, 'EDITAR', $_SESSION['usuario']->getId(),
				['Nombre' => $antes->getNombre(), 'Correo' => $antes->getCorreo()],
				['Nombre' => $nombre, 'Correo' => $correo]
			);
			flash_now('success', 'Destinatario actualizado exitosamente.');
		} catch (PDOException $e) {
			flash_now('danger', 'No se ha podido actualizar el destinatario. Verifique que el correo no esté repetido.');
		}

		$this->show();
	}

	function activar(){
		if (!Permiso::usuarioPuede('notificacion.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		NotificacionDestinatario::activar($id);

		Auditoria::registrar('NotificacionDestinatario', $id, 'ACTIVAR', $_SESSION['usuario']->getId(),
			['Activo' => 0], ['Activo' => 1]
		);
		flash_now('success', 'Destinatario activado.');
		$this->show();
	}

	function desactivar(){
		if (!Permiso::usuarioPuede('notificacion.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		NotificacionDestinatario::desactivar($id);

		Auditoria::registrar('NotificacionDestinatario', $id, 'DESACTIVAR', $_SESSION['usuario']->getId(),
			['Activo' => 1], ['Activo' => 0]
		);
		flash_now('success', 'Destinatario desactivado.');
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
