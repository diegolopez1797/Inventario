<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class InformeGeneralController
{
	
	function __construct()
	{
		
	}

	function show(){

		if (!empty($_SESSION['informeParcial'])) {
			$_SESSION['informeGeneral'] = $_SESSION['informeParcial'];
		}else{
			$_SESSION['informeTotal'] = InformeGeneral::all();
			$_SESSION['informeGeneral'] = $_SESSION['informeTotal'];
		}
		

		$listaMaterialCompleta = Material::all();
		require_once('Views/Informes/InformeGeneral.php');
	}


	function search(){
		if (!empty($_POST['codigo'])) {
			$codigo = $_POST['codigo'];
			$material = InformeGeneral::searchById($codigo);
			if ($codigo =! $material->getCodigo()) {
				echo "<script>alert('¡ El material buscado NO EXISTE !')</script>";
			}else{

				if (isset($_SESSION['informeParcial'])) {
				$listaMaterial = [];
				$listaMaterial = $_SESSION['informeParcial'];
				}else{
					$listaMaterial = [];
				}
				array_push ( $listaMaterial , $material );
				$_SESSION['informeParcial'] = $listaMaterial;

			}
			
		}
		$this->show();
	}


	function generarPDF(){
		echo "<script>window.open('Controllers/InformeGeneralPDF.php', '_blank')</script>";
		unset($_SESSION['informeGeneral']);
		$this->show();
	}

	function eliminar(){
		unset($_SESSION['informeParcial']);
		$this->show();
	}

	function error(){
		require_once('Views/Material/error.php');
	}

}
	
}else{
	echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}



?>