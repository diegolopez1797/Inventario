<?php
/**
*
*/
if (isset($_SESSION['usuario'])) {

	class UsuarioController
{

	function __construct()
	{

	}

	function register(){
		if (!Permiso::usuarioPuede('usuario.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar usuarios.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$listaRol = Rol::all();
		require_once('Views/Usuario/register.php');
	}

	function save(){
		if (!Permiso::usuarioPuede('usuario.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar usuarios.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$identificacion = $_POST['identificacion'];
		$nombre = $_POST['nombre'];
		$apellido = $_POST['apellido'];
		$clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
		$rol = $_POST['rol'];

		$usuario = new Usuario(null, $identificacion, $nombre, $apellido, $clave, $rol);
		$respuesta = Usuario::save($usuario);
		if(isset($respuesta)){
		    $nuevoId = Db::getConnect()->lastInsertId();
		    Auditoria::registrar('Usuario', $nuevoId, 'CREAR', $_SESSION['usuario']->getId(), null, [
		        'Identificacion' => $usuario->getIdentificacion(),
		        'Nombre' => $usuario->getNombre(),
		        'Apellido' => $usuario->getApellido(),
		        'RolID' => $usuario->getRolId(),
		    ]);
		    flash_now('success', 'Usuario creado exitosamente.');
		}
		else{
		    flash_now('danger', 'No se ha podido guardar el usuario. Inténtelo nuevamente.');
		}
		$this->show();
	}

	function show(){
		if (!Permiso::usuarioPuede('usuario.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar usuarios.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$listaUsuario = Usuario::all();

		require_once('Views/Usuario/show.php');
	}

	function updateshow(){
		if (!Permiso::usuarioPuede('usuario.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar usuarios.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$usuario = Usuario::searchByCodigoUser($id);
		$listaRol = Rol::all();
		require_once('Views/Usuario/updateshow.php');
	}

	function update(){
		if (!Permiso::usuarioPuede('usuario.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar usuarios.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_POST['id'];
		$identificacion = $_POST['identificacion'];
		$nombre = $_POST['nombre'];
		$apellido = $_POST['apellido'];
		$rol = $_POST['rol'];

		$antes = Usuario::searchByCodigoUser($id);

		// Campo de contraseña vacio = conservar la contraseña actual sin modificarla.
		// Solo se genera un hash nuevo si el administrador escribio una contraseña nueva.
		$clave = !empty($_POST['clave']) ? password_hash($_POST['clave'], PASSWORD_DEFAULT) : $antes->getClave();

		$usuario = new Usuario($id, $identificacion, $nombre, $apellido, $clave, $rol);
		Usuario::update($usuario);

		Auditoria::registrar('Usuario', $usuario->getId(), 'EDITAR', $_SESSION['usuario']->getId(), [
			'Identificacion' => $antes->getIdentificacion(),
			'Nombre' => $antes->getNombre(),
			'Apellido' => $antes->getApellido(),
			'RolID' => $antes->getRolId(),
		], [
			'Identificacion' => $usuario->getIdentificacion(),
			'Nombre' => $usuario->getNombre(),
			'Apellido' => $usuario->getApellido(),
			'RolID' => $usuario->getRolId(),
		]);

		$this->show();
	}
	function delete(){
		if (!Permiso::usuarioPuede('usuario.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar usuarios.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id=$_GET['id'];

		try{
			$antes = Usuario::searchByCodigoUser($id);
			Usuario::delete($id);
			Auditoria::registrar('Usuario', $id, 'ELIMINAR', $_SESSION['usuario']->getId(), [
				'Identificacion' => $antes->getIdentificacion(),
				'Nombre' => $antes->getNombre(),
				'Apellido' => $antes->getApellido(),
				'RolID' => $antes->getRolId(),
			], null);
		}catch (Exception $e) {
			flash_now('danger', 'No se puede eliminar el usuario.');
		}


		$this->show();
	}

	function search(){
		if (!Permiso::usuarioPuede('usuario.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar usuarios.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		if ((!empty($_POST['identificacion'])) and ($_POST['identificacion']>=1)) {
			$identificacion = $_POST['identificacion'];
			$usuario = Usuario::searchByIdUser($identificacion);
			if ($usuario->getIdentificacion() == $identificacion) {
				$listaUsuario[] = $usuario;
				require_once('Views/Usuario/show.php');
			}else{
				flash_now('warning', 'El usuario buscado no existe.');
				$this->show();
			}
		} else {
			flash_now('warning', 'No ha ingresado una identificación o el valor ingresado no es válido.');
			$this->show();
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
