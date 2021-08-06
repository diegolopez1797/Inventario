<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class MaterialController
{
	
	function __construct()
	{
		
	}

	function index(){
		require_once('Views/Material/bienvenido.php');
	}

	function register(){
		require_once('Views/Material/register.php');
		
	}

	function save(){
		$material = new Material(null,$_POST['codigo'],$_POST['descripcion'],$_POST['unidad'],null);
		$respuesta = Material::save($material);

		if(isset($respuesta)){
		    echo "<script>alert('¡ Material Creado EXITOSAMENTE !')</script>";
		}
		else{
		    echo "<script>alert('¡ Ups... No se ha podido guardar el material. Intentalo Nuevamente !')</script>";
		}
		
		$this->show();
		
	}

	function show(){

		$listaMaterial = Material::all();
		$listaMaterialCompleta = $listaMaterial;
		require_once('Views/Material/show.php');
	}

	function updateshow(){
		$id = $_GET['id'];
		$material = Material::searchById($id);
		require_once('Views/Material/updateshow.php');
	}

	function update(){
		$material = new Material($_POST['id'],$_POST['codigo'],$_POST['descripcion'],$_POST['unidad'],$_POST['saldo']);
		Material::update($material);
		$this->show();
	}
	function delete(){
		$id=$_GET['id'];

		try{
			Material::delete($id);
		}catch (Exception $e) {
			echo "<script>alert('¡ Ups... No se puede eliminar el material !')</script>";
		}
		
		
		$this->show();

	}

	function search(){
		if (!empty($_POST['codigo'])) {
			$codigo = $_POST['codigo'];
			$material = Material::searchByCodigo($codigo);
			if ($codigo =! $material->getCodigo()) {
				echo "<script>alert('¡ El material buscado NO EXISTE !')</script>";
				$this->show();
			}else{
				$listaMaterial[] = $material;
				require_once('Views/Material/show.php');
			}	
		} else {
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