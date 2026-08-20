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
		require_once('Views/Rubro/register.php');
	}

	function save(){

		$rubro = new Rubro(null,$_POST['descripcion']);
		$respuesta = Rubro::save($rubro);
		if(isset($respuesta)){
		    echo "<script>alert('¡ Actividad Creada Exitosamente !')</script>";
		}
		else{
		    echo "<script>alert('¡ Ups... No se ha podido guardar la actividad. Intentalo Nuevamente !')</script>";
		}
			$this->show();
	}

	function show(){

		$listaRubro = Rubro::all();

		require_once('Views/Rubro/show.php');
	}

	function updateshow(){
		$id = $_GET['id'];
		$rubro = Rubro::searchByIdUpdate($id);
		require_once('Views/Rubro/updateshow.php');
	}

	function update(){
		$rubro = new Rubro($_POST['id'],$_POST['descripcion']);
		Rubro::update($rubro);
		$this->show();
	}
	function delete(){
		$id=$_GET['id'];

		try{
			Rubro::delete($id);
		}catch (Exception $e) {
			echo "<script>alert('¡ Ups... No se puede eliminar la actividad !')</script>";
		}
		
		
		$this->show();
	}

	function search(){
		if ((!empty($_POST['id'])) and ($_POST['id']>=1)) {
			$id = $_POST['id'];
			$rubro = Rubro::searchById($id);
			if ($rubro->getId() == $id) {
				$listaRubro[] = $rubro;
				require_once('Views/Rubro/show.php');
			}else{
				echo "<script>alert('¡ La actividad buscada NO EXISTE !')</script>";
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