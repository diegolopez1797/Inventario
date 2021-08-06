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

		$usuarioLogeado = Usuario::verificarUsuario($identificacion, $clave);

		if ($usuarioLogeado == false) {
			echo "<script>alert('¡ Identificacion o Contraseña INCORRECTA !')</script>";
			$this->show();
		} else {
			$usuario = Usuario::searchByIdUser($identificacion);
			$this->entrar($usuario);
			
		}
		
	}

	function entrar($usuario){
		$_SESSION['usuario'] = $usuario;
		echo "<script>window.location.href = '?controller=Material&action=index';</script>";
	}

	function salir(){
		unset($_SESSION['usuario']);
		session_destroy();
		echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}	
}
?>