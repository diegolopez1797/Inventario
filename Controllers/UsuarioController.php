<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

if ($_SESSION['usuario']->getRolId() == 1) {

	class UsuarioController
{
	
	function __construct()
	{
		
	}

	function register(){
		require_once('Views/Usuario/register.php');
	}

	function save(){

		$identificacion = $_POST['identificacion'];
		$nombre = $_POST['nombre'];
		$apellido = $_POST['apellido'];
		$clave = $_POST['clave'];
		$rol = $_POST['rol'];

		$usuario = new Usuario(null, $identificacion, $nombre, $apellido, $clave, $rol);
		$respuesta = Usuario::save($usuario);
		if(isset($respuesta)){
		    echo "<script>alert('¡ Usuario Creado Exitosamente !')</script>";
		}
		else{
		    echo "<script>alert('¡ Ups... No se ha podido guardar el Usuario. Intentalo Nuevamente !')</script>";
		}
		$this->show();
	}

	function show(){

		$listaUsuario = Usuario::all();

		require_once('Views/Usuario/show.php');
	}

	function updateshow(){
		$id = $_GET['id'];
		$usuario = Usuario::searchByCodigoUser($id);
		require_once('Views/Usuario/updateshow.php');
	}

	function update(){
		$id = $_POST['id'];
		$identificacion = $_POST['identificacion'];
		$nombre = $_POST['nombre'];
		$apellido = $_POST['apellido'];
		$clave = $_POST['clave'];
		$rol = $_POST['rol'];

		$usuario = new Usuario($id, $identificacion, $nombre, $apellido, $clave, $rol);
		Usuario::update($usuario);
		$this->show();
	}
	function delete(){
		$id=$_GET['id'];

		try{
			Usuario::delete($id);
		}catch (Exception $e) {
			echo "<script>alert('¡ Ups... No se puede eliminar el usuario !')</script>";
		}
		
		
		$this->show();
	}

	function search(){
		if ((!empty($_POST['identificacion'])) and ($_POST['identificacion']>=1)) {
			$identificacion = $_POST['identificacion'];
			$usuario = Usuario::searchByIdUser($identificacion);
			if ($usuario->getIdentificacion() == $identificacion) {
				$listaUsuario[] = $usuario;
				require_once('Views/Usuario/show.php');
			}else{
				echo "<script>alert('¡ El usuario buscado NO EXISTE !')</script>";
				$this->show();
			}	
		} else {
			echo "<script>alert('¡ No a ingresado una identificacion o el valor ingresado NO ES VALIDO !')</script>";
			$this->show();
		}
	}

	function error(){
		require_once('Views/Material/error.php');
	}

}

}else{

	echo "<script>window.location.href = '?controller=Material&action=index';</script>";

}
}else{
	echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}



?>