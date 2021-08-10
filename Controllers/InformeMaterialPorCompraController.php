<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class InformeMaterialPorCompraController
{
	
	function __construct()
	{
		
	}

	function show(){

		if (!empty($_SESSION['informeParcialPorCompra'])) {
			$_SESSION['informeGeneralPorCompra'] = $_SESSION['informeParcialPorCompra'];
		}else{
			$_SESSION['informeTotalPorCompra'] = InformeMaterialPorCompra::all();
			$_SESSION['informeGeneralPorCompra'] = $_SESSION['informeTotalPorCompra'];
		}
		

		$listaMaterialCompletaPorCompra = InformeMaterialPorCompra::all();
		require_once('Views/Informes/InformeMaterialPorCompra.php');
	}


	function search(){
		if (!empty($_POST['codigo'])) {
			$codigo = $_POST['codigo'];
			$material = InformeMaterialPorCompra::searchByCodigo($codigo);
			if ($codigo =! $material->getCodigo()) {
				echo "<script>alert('¡ El material buscado NO EXISTE !')</script>";
			}else{

				if (isset($_SESSION['informeParcialPorCompra'])) {
				$listaMaterial = [];
				$listaMaterial = $_SESSION['informeParcialPorCompra'];
				}else{
					$listaMaterial = [];
				}
				array_push ( $listaMaterial , $material );
				$_SESSION['informeParcialPorCompra'] = $listaMaterial;

			}
			
		}
		$this->show();
	}


	function generarPDF(){
		echo "<script>window.open('Controllers/InformeMaterialPorCompraPDF.php', '_blank')</script>";
		unset($_SESSION['informeGeneralPorCompra']);
		$this->show();
	}

	function eliminar(){
		unset($_SESSION['informeParcialPorCompra']);
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