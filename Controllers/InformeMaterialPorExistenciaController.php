<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class InformeMaterialPorExistenciaController
{
	
	function __construct()
	{
		
	}

	function show(){

		if (!empty($_SESSION['informeParcialPorExistencia'])) {
			$_SESSION['informeGeneralPorExistencia'] = $_SESSION['informeParcialPorExistencia'];
		}else{
			$_SESSION['informeTotalPorExistencia'] = InformeMaterialPorExistencia::all();
			$_SESSION['informeGeneralPorExistencia'] = $_SESSION['informeTotalPorExistencia'];
		}
		

		$listaMaterialCompletaPorExistencia = InformeMaterialPorExistencia::all();
		require_once('Views/Informes/InformeMaterialPorExistencia.php');
	}


	function search(){
		if (!empty($_POST['codigo'])) {
			$codigo = $_POST['codigo'];
			$material = InformeMaterialPorExistencia::searchByCodigo($codigo);
			if ($codigo =! $material->getCodigo()) {
				echo "<script>alert('¡ El material buscado NO EXISTE !')</script>";
			}else{

				if (isset($_SESSION['informeParcialPorExistencia'])) {
				$listaMaterial = [];
				$listaMaterial = $_SESSION['informeParcialPorExistencia'];
				}else{
					$listaMaterial = [];
				}
				array_push ( $listaMaterial , $material );
				$_SESSION['informeParcialPorExistencia'] = $listaMaterial;

			}
			
		}
		$this->show();
	}


	function generarPDF(){
		echo "<script>window.open('Controllers/InformeMaterialPorExistenciaPDF.php', '_blank')</script>";
		unset($_SESSION['informeGeneralPorExistencia']);
		$this->show();
	}

	function eliminar(){
		unset($_SESSION['informeParcialPorExistencia']);
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