<?php 
/**
* 
*/

if (isset($_SESSION['usuario'])) {

	class CasaController
{
	
	function __construct()
	{
		
	}

	function register(){
		require_once('Views/Casa/register.php');
	}

	function save(){

		$casa = new Casa(null,$_POST['descripcion']);
		$respuesta = Casa::save($casa);
		if(isset($respuesta)){
		    echo "<script>alert('¡ Casa Creada Exitosamente !')</script>";
		}
		else{
		    echo "<script>alert('¡ Ups... No se ha podido guardar la Casa. Intentalo Nuevamente !')</script>";
		}
			$this->show();
	}

	function show(){

		$listaCasa = Casa::all();

		require_once('Views/Casa/show.php');
	}

	function updateshow(){
		$id = $_GET['id'];
		$casa = Casa::searchByIdUpdate($id);
		require_once('Views/Casa/updateshow.php');
	}

	function update(){
		$casa = new Casa($_POST['id'],$_POST['descripcion']);
		Casa::update($casa);
		$this->show();
	}
	function delete(){
		$id=$_GET['id'];

		try{
			Casa::delete($id);
		}catch (Exception $e) {
			echo "<script>alert('¡ Ups... No se puede eliminar la casa !')</script>";
		}
		
		
		$this->show();
	}

	function search(){
		if ((!empty($_POST['id'])) and ($_POST['id']>=1)) {
			$id = $_POST['id'];
			$casa = Casa::searchById($id);
			if ($casa->getId() == $id) {
				$listaCasa[] = $casa;
				require_once('Views/Casa/show.php');
			}else{
				echo "<script>alert('¡ La casa buscada NO EXISTE !')</script>";
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