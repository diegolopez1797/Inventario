<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class DestinoController
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

		require_once('Views/Destino/register.php');
	}

	function save(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$destino = new Destino(null,$_POST['descripcion']);
		$respuesta = Destino::save($destino);
		if(isset($respuesta)){
		    flash_now('success', 'Destino creado exitosamente.');
		}
		else{
		    flash_now('danger', 'No se ha podido guardar el destino. Inténtelo nuevamente.');
		}
			$this->show();
	}

	function show(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este catálogo.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$listaDestino = Destino::all();

		require_once('Views/Destino/show.php');
	}

	function updateshow(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$destino = Destino::searchByIdUpdate($id);
		require_once('Views/Destino/updateshow.php');
	}

	function update(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$destino = new Destino($_POST['id'],$_POST['descripcion']);
		Destino::update($destino);
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
			Destino::delete($id);
		}catch (Exception $e) {
			flash_now('danger', 'No se puede eliminar el destino.');
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
			$destino = Destino::searchById($id);
			if ($destino->getId() == $id) {
				$listaDestino[] = $destino;
				require_once('Views/Destino/show.php');
			}else{
				flash_now('warning', 'El destino buscado no existe.');
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