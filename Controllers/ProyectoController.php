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
		require_once('Views/Proyecto/register.php');
	}

	function save(){

		$proyecto = new Proyecto(null,$_POST['descripcion']);
		$respuesta = Proyecto::save($proyecto);
		if(isset($respuesta)){
		    echo "<script>alert('¡ Proyecto Creado Exitosamente !')</script>";
		}
		else{
		    echo "<script>alert('¡ Ups... No se ha podido guardar el Proyecto. Intentalo Nuevamente !')</script>";
		}
			$this->show();
	}

	function show(){

		$listaProyecto = Proyecto::all();
		require_once('Views/Proyecto/show.php');
	}

	function updateshow(){
		$id = $_GET['id'];
		$proyecto = Proyecto::searchByIdUpdate($id);
		require_once('Views/Proyecto/updateshow.php');
	}

	function update(){
		$proyecto = new Proyecto($_POST['id'],$_POST['descripcion']);
		Proyecto::update($proyecto);
		$this->show();
	}
	function delete(){
		$id=$_GET['id'];

		try{
			Proyecto::delete($id);
		}catch (Exception $e) {
			echo "<script>alert('¡ Ups... No se puede eliminar el Proyecto !')</script>";
		}
		
		
		$this->show();
	}

	function search(){
		if ((!empty($_POST['id'])) and ($_POST['id']>=1)) {
			$id = $_POST['id'];
			$proyecto = Proyecto::searchById($id);
			if ($proyecto->getId() == $id) {
				$listaProyecto[] = $proyecto;
				require_once('Views/Proyecto/show.php');
			}else{
				echo "<script>alert('¡ El proyecto buscado NO EXISTE !')</script>";
				$this->show();
			}	
		} else {
			echo "<script>alert('¡ No a ingresado un codigo o el valor ingresado NO ES VALIDO !')</script>";
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