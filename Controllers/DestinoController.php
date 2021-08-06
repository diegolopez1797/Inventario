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
		require_once('Views/Destino/register.php');
	}

	function save(){

		$destino = new Destino(null,$_POST['descripcion']);
		$respuesta = Destino::save($destino);
		if(isset($respuesta)){
		    echo "<script>alert('¡ Destino Creado Exitosamente !')</script>";
		}
		else{
		    echo "<script>alert('¡ Ups... No se ha podido guardar el Destino. Intentalo Nuevamente !')</script>";
		}
			$this->show();
	}

	function show(){

		$listaDestino = Destino::all();

		require_once('Views/Destino/show.php');
	}

	function updateshow(){
		$id = $_GET['id'];
		$destino = Destino::searchByIdUpdate($id);
		require_once('Views/Destino/updateshow.php');
	}

	function update(){
		$destino = new Destino($_POST['id'],$_POST['descripcion']);
		Destino::update($destino);
		$this->show();
	}
	function delete(){

		$id=$_GET['id'];

		try{
			Destino::delete($id);
		}catch (Exception $e) {
			echo "<script>alert('¡ Ups... No se puede eliminar el destino !')</script>";
		}
		
		$this->show();
	}

	function search(){
		if ((!empty($_POST['id'])) and ($_POST['id']>=1)) {
			$id = $_POST['id'];
			$destino = Destino::searchById($id);
			if ($destino->getId() == $id) {
				$listaDestino[] = $destino;
				require_once('Views/Destino/show.php');
			}else{
				echo "<script>alert('¡ El destino buscado NO EXISTE !')</script>";
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