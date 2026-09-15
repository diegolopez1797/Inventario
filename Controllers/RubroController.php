<?php 
/**
* 
*/

if (isset($_SESSION['usuario'])) {

	class RubroController
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

		require_once('Views/Rubro/register.php');
	}

	function save(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$rubro = new Rubro(null,$_POST['descripcion']);
		$respuesta = Rubro::save($rubro);
		if(isset($respuesta)){
		    flash_now('success', 'Actividad creada exitosamente.');
		}
		else{
		    flash_now('danger', 'No se ha podido guardar la actividad. Inténtelo nuevamente.');
		}
			$this->show();
	}

	function show(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este catálogo.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$listaRubro = Rubro::all();

		require_once('Views/Rubro/show.php');
	}

	function updateshow(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$rubro = Rubro::searchByIdUpdate($id);
		require_once('Views/Rubro/updateshow.php');
	}

	function update(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$rubro = new Rubro($_POST['id'],$_POST['descripcion']);
		Rubro::update($rubro);
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
			Rubro::delete($id);
		}catch (Exception $e) {
			flash_now('danger', 'No se puede eliminar la actividad.');
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
			$rubro = Rubro::searchById($id);
			if ($rubro->getId() == $id) {
				$listaRubro[] = $rubro;
				require_once('Views/Rubro/show.php');
			}else{
				flash_now('warning', 'La actividad buscada no existe.');
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