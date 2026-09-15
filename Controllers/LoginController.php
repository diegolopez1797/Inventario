<?php 
/**
* 
*/
class LoginController{
	
	function __construct()
	{
		
	}

	function show(){

		require_once('Views/Login/Login.php');
	}


	function verificar(){

		$identificacion = $_POST['identificacion'];
		$clave = $_POST['clave'];

		try {
			$usuarioLogeado = Usuario::verificarUsuario($identificacion, $clave);
		} catch (Throwable $e) {
			// Cualquier error durante la validacion de la contraseña se trata como
			// credenciales invalidas: nunca debe propagarse un error que muestre la
			// contraseña ingresada (argumento de la llamada) en un stack trace.
			$usuarioLogeado = false;
		}

		if ($usuarioLogeado == false) {
			flash_now('danger', 'Identificación o contraseña incorrecta.');
			$this->show();
		} else {
			$usuario = Usuario::searchByIdUser($identificacion);
			$this->entrar($usuario);

		}

	}

	function entrar($usuario){
		session_regenerate_id(true);
		$_SESSION['usuario'] = $usuario;
		flash('success', 'Bienvenido: ' . $usuario->getNombre() . ' ' . $usuario->getApellido());
		echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
	}

	function salir(){
		unset($_SESSION['usuario']);
		session_destroy();
		echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}	
}
?>