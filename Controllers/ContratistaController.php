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
		require_once('Views/Contratista/register.php');
	}

	function save(){

		$contratista = new Contratista(null,$_POST['descripcion']);
		$respuesta = Contratista::save($contratista);
		if(isset($respuesta)){
		    echo "<script>alert('¡ Contratista Creado Exitosamente !')</script>";
		}
		else{
		    echo "<script>alert('¡ Ups... No se ha podido guardar el Contratista. Intentalo Nuevamente !')</script>";
		}
			$this->show();
	}

	function show(){

		$listaContratista = Contratista::all();
		require_once('Views/Contratista/show.php');
	}

	function updateshow(){
		$id = $_GET['id'];
		$contratista = Contratista::searchByIdUpdate($id);
		require_once('Views/Contratista/updateshow.php');
	}

	function update(){
		$contratista = new Contratista($_POST['id'],$_POST['descripcion']);
		Contratista::update($contratista);
		$this->show();
	}
	function delete(){
		$id=$_GET['id'];

		try{
			Contratista::delete($id);
		}catch (Exception $e) {
			echo "<script>alert('¡ Ups... No se puede eliminar el contratista !')</script>";
		}
		
		
		$this->show();
	}

	function search(){
		if ((!empty($_POST['id'])) and ($_POST['id']>=1)) {
			$id = $_POST['id'];
			$contratista = Contratista::searchById($id);
			if ($contratista->getId() == $id) {
				$listaContratista[] = $contratista;
				require_once('Views/Contratista/show.php');
			}else{
				echo "<script>alert('¡ El contratista buscado NO EXISTE !')</script>";
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