<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class InformeSalidaController
{
	
	function __construct()
	{
		
	}

	function show(){


		if (!empty($_SESSION['informeSalidaParcial'])) {
			$_SESSION['informeSalidaGeneral'] = $_SESSION['informeSalidaParcial'];
		}else{
			$_SESSION['informeSalidaTotal'] = InformeSalida::all();
			$_SESSION['informeSalidaGeneral'] = $_SESSION['informeSalidaTotal'];
		}
		//---------------------------------------------------------------

		require_once('Views/Informes/InformeSalida.php');
	}

	function detalle(){
		$id = $_GET['id'];
		$_SESSION['idSalida'] = $id;
		$idUsuario = $_GET['usuario'];
		$usuario = Usuario::searchByCodigoUser($idUsuario);
		//print_r($usuario);
		$materialRegistroSalidas = InformeMaterialSalida::searchMaterialRegistroSalidas($id);
		$material = [];
		foreach ($materialRegistroSalidas as $salida) {
			$material[] = Material::searchById($salida->getMaterialId());
		}

		require_once('Views/Informes/DetalleSalida.php');
	}

	function generarPDF(){
		echo "<script>window.open('Controllers/InformeSalidaMaterialPDF.php', '_blank')</script>";
		unset($_SESSION['informeSalidaParcial']);
		$this->show();
	}

	function generarSalidaPDF(){
		echo "<script>window.open('Controllers/InformeSalidaPDF.php', '_blank')</script>";
		//unset($_SESSION['informeEntradaParcial']);
		$this->show();
	}


	function search(){
		if (!empty($_POST['codigo'])) {
			$codigo = $_POST['codigo'];
			$material = InformeSalida::searchById($codigo);
			if ($codigo =! $material->getId()) {
				echo "<script>alert('¡ La salida buscado NO EXISTE !')</script>";
			}else{

				if (isset($_SESSION['informeSalidaParcial'])) {
				$listaMaterial = [];
				$listaMaterial = $_SESSION['informeSalidaParcial'];
				}else{
					$listaMaterial = [];
				}
				array_push ( $listaMaterial , $material );
				$_SESSION['informeSalidaParcial'] = $listaMaterial;

			}
			
		}
		$this->show();
	}

	function eliminar(){
		unset($_SESSION['informeSalidaParcial']);
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