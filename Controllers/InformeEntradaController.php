<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class InformeEntradaController
{
	
	function __construct()
	{
		
	}

	function show(){


		if (!empty($_SESSION['informeEntradaParcial'])) {
			$_SESSION['informeEntradaGeneral'] = $_SESSION['informeEntradaParcial'];
		}else{
			$_SESSION['informeEntradaTotal'] = InformeEntrada::all();
			$_SESSION['informeEntradaGeneral'] = $_SESSION['informeEntradaTotal'];
		}
		//---------------------------------------------------------------

		require_once('Views/Informes/InformeEntrada.php');
	}

	function detalle(){
		$id = $_GET['id'];
		$_SESSION['idEntrada'] = $id;
		$idUsuario = $_GET['usuario'];
		$usuario = Usuario::searchByCodigoUser($idUsuario);
		$materialRegistroEntradas = InformeMaterialEntrada::searchMaterialRegistroEntradas($id);
		$material = [];
		foreach ($materialRegistroEntradas as $entrada) {
			$material[] = Material::searchById($entrada->getMaterialId());
		}

		require_once('Views/Informes/DetalleEntrada.php');
	}

	function generarPDF(){
		echo "<script>window.open('Controllers/InformeEntradaMaterialPDF.php', '_blank')</script>";
		unset($_SESSION['informeEntradaParcial']);
		$this->show();
	}

	function generarEntradaPDF(){
		echo "<script>window.open('Controllers/InformeEntradaPDF.php', '_blank')</script>";
		//unset($_SESSION['informeEntradaParcial']);
		$this->show();
	}


	function search(){
		if (!empty($_POST['codigo'])) {
			$codigo = $_POST['codigo'];
			$material = InformeEntrada::searchById($codigo);
			if ($codigo =! $material->getId()) {
				echo "<script>alert('¡ La entrada buscado NO EXISTE !')</script>";
			}else{

				if (isset($_SESSION['informeEntradaParcial'])) {
				$listaMaterial = [];
				$listaMaterial = $_SESSION['informeEntradaParcial'];
				}else{
					$listaMaterial = [];
				}
				array_push ( $listaMaterial , $material );
				$_SESSION['informeEntradaParcial'] = $listaMaterial;

			}
			
		}
		$this->show();
	}

	function eliminar(){
		unset($_SESSION['informeEntradaParcial']);
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