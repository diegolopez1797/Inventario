<?php 
/**
* 
*/

if (isset($_SESSION['usuario'])) {


	class AreaController
{
	
	function __construct()
	{
		
	}

	function register(){
		require_once('Views/Area/register.php');
	}

	function save(){

		$area = new Area(null,$_POST['descripcion']);
		$respuesta = Area::save($area);
		if(isset($respuesta)){
		    echo "<script>alert('¡ Area Creada Exitosamente !')</script>";
		}
		else{
		    echo "<script>alert('¡ Ups... No se ha podido guardar el area. Intentalo Nuevamente !')</script>";
		}
			$this->show();
	}

	function show(){

		$listaArea = Area::all();

		require_once('Views/Area/show.php');
	}

	function updateshow(){
		$id = $_GET['id'];
		$area = Area::searchByIdUpdate($id);
		require_once('Views/Area/updateshow.php');
	}

	function update(){
		$area = new Area($_POST['id'],$_POST['descripcion']);
		Area::update($area);
		$this->show();
	}
	function delete(){
		$id=$_GET['id'];

		try{
			Area::delete($id);
		}catch (Exception $e) {
			echo "<script>alert('¡ Ups... No se puede eliminar el area !')</script>";
		}
		
		
		$this->show();
	}

	function search(){
		if ((!empty($_POST['id'])) and ($_POST['id']>=1)) {
			$id = $_POST['id'];
			$area = Area::searchById($id);
			if ($area->getId() == $id) {
				$listaArea[] = $area;
				require_once('Views/Area/show.php');
			}else{
				echo "<script>alert('¡ El area buscada NO EXISTE !')</script>";
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