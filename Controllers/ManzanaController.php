<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {
	
	class ManzanaController
{
	
	function __construct()
	{
		
	}

	function register(){
		require_once('Views/Manzana/register.php');
	}

	function save(){

		$manzana = new Manzana(null,$_POST['descripcion']);
		$respuesta = Manzana::save($manzana);
		if(isset($respuesta)){
		    echo "<script>alert('¡ Manzana Creada Exitosamente !')</script>";
		}
		else{
		    echo "<script>alert('¡ Ups... No se ha podido guardar la Manzana. Intentalo Nuevamente !')</script>";
		}
			$this->show();
	}

	function show(){

		$listaManzana = Manzana::all();

		require_once('Views/Manzana/show.php');
	}

	function updateshow(){
		$id = $_GET['id'];
		$manzana = Manzana::searchByIdUpdate($id);
		require_once('Views/Manzana/updateshow.php');
	}

	function update(){
		$manzana = new Manzana($_POST['id'],$_POST['descripcion']);
		Manzana::update($manzana);
		$this->show();
	}
	function delete(){
		$id=$_GET['id'];

		try{
			Manzana::delete($id);
		}catch (Exception $e) {
			echo "<script>alert('¡ Ups... No se puede eliminar la manzana !')</script>";
		}
		
		
		$this->show();
	}

	function search(){
		if ((!empty($_POST['id'])) and ($_POST['id']>=1)) {
			$id = $_POST['id'];
			$manzana = Manzana::searchById($id);
			if ($manzana->getId() == $id) {
				$listaManzana[] = $manzana;
				require_once('Views/Manzana/show.php');
			}else{
				echo "<script>alert('¡ La manzana buscada NO EXISTE !')</script>";
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

}else{
	echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}



?>