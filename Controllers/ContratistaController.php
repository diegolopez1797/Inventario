<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class ContratistaController
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

		require_once('Views/Contratista/register.php');
	}

	function save(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$contratista = new Contratista(null,$_POST['descripcion']);
		$respuesta = Contratista::save($contratista);
		if(isset($respuesta)){
		    flash_now('success', 'Contratista creado exitosamente.');
		}
		else{
		    flash_now('danger', 'No se ha podido guardar el contratista. Inténtelo nuevamente.');
		}
			$this->show();
	}

	function show(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este catálogo.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$listaContratista = Contratista::all();
		require_once('Views/Contratista/show.php');
	}

	function updateshow(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$contratista = Contratista::searchByIdUpdate($id);
		require_once('Views/Contratista/updateshow.php');
	}

	function update(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$contratista = new Contratista($_POST['id'],$_POST['descripcion']);
		Contratista::update($contratista);
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
			Contratista::delete($id);
		}catch (Exception $e) {
			flash_now('danger', 'No se puede eliminar el contratista.');
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
			$contratista = Contratista::searchById($id);
			if ($contratista->getId() == $id) {
				$listaContratista[] = $contratista;
				require_once('Views/Contratista/show.php');
			}else{
				flash_now('warning', 'El contratista buscado no existe.');
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