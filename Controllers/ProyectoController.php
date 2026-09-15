<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class ProyectoController
{
	
	function __construct()
	{
		
	}

	function register(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$listaUsuario = Usuario::all();
		require_once('Views/Proyecto/register.php');
	}

	function save(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$responsableId = !empty($_POST['responsable']) ? $_POST['responsable'] : null;
		$proyecto = new Proyecto(null,$_POST['descripcion'], $responsableId);
		$respuesta = Proyecto::save($proyecto);
		if(isset($respuesta)){
		    flash_now('success', 'Proyecto creado exitosamente.');
		}
		else{
		    flash_now('danger', 'No se ha podido guardar el proyecto. Inténtelo nuevamente.');
		}
			$this->show();
	}

	function show(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este catálogo.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$listaProyecto = Proyecto::all();
		require_once('Views/Proyecto/show.php');
	}

	function updateshow(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$proyecto = Proyecto::searchByIdUpdate($id);
		$listaUsuario = Usuario::all();
		require_once('Views/Proyecto/updateshow.php');
	}

	function update(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$responsableId = !empty($_POST['responsable']) ? $_POST['responsable'] : null;
		$proyecto = new Proyecto($_POST['id'],$_POST['descripcion'], $responsableId);
		Proyecto::update($proyecto);
		$this->show();
	}
	function delete(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id=$_GET['id'];

		try{
			Proyecto::delete($id);
		}catch (Exception $e) {
			flash_now('danger', 'No se puede eliminar el proyecto.');
		}


		$this->show();
	}

	function search(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este catálogo.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		if ((!empty($_POST['id'])) and ($_POST['id']>=1)) {
			$id = $_POST['id'];
			$proyecto = Proyecto::searchById($id);
			if ($proyecto->getId() == $id) {
				$listaProyecto[] = $proyecto;
				require_once('Views/Proyecto/show.php');
			}else{
				flash_now('warning', 'El proyecto buscado no existe.');
				$this->show();
			}
		} else {
			flash_now('warning', 'No ha ingresado un código o el valor ingresado no es válido.');
			$this->show();
		}
	}

	function error(){
		require_once('Views/Material/error.php');
	}

}

	# code...
}else{
	echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}


?>