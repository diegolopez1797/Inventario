<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class InformeEntradaPorMaterialFechaController
{
	
	function __construct()
	{
		
	}

	function show(){

		if (!empty($_SESSION['informeParcialPorMaterialFecha'])) {
			$_SESSION['informeGeneralPorMaterialFecha'] = $_SESSION['informeParcialPorMaterialFecha'];
		}else{
			$_SESSION['informeTotalPorMaterialFecha'] = InformeEntradaPorMaterialFecha::all();
			$_SESSION['informeGeneralPorMaterialFecha'] = $_SESSION['informeTotalPorMaterialFecha'];
		}
		

		$listaMaterialCompleta = Material::all();
		require_once('Views/Informes/InformeEntradaPorMaterialFecha.php');
	}


	function search(){

		$id = $_POST['id'];
		$fechaInicial = $_POST['fechaInicial'];
		$fechaFinal = $_POST['fechaFinal'];


		if (!empty($fechaInicial) and !empty($fechaFinal)) {

			$informeEntradaPorFecha = InformeEntradaPorFecha::searchByFecha($fechaInicial, $fechaFinal);
			$informeEntradaPorMaterialFecha = InformeEntradaPorMaterialFecha::searchMaterialRegistroEntradas($id);

			$objetoTotal = [];
			foreach ($informeEntradaPorMaterialFecha as $entradaMaterial) {
				foreach ($informeEntradaPorFecha as $entradaFecha) {
					if (($entradaMaterial->getRegistroEntradasId()) == ($entradaFecha->getId())) {
						array_push ( $objetoTotal , $entradaMaterial );
					}
				}
			}

			$_SESSION['informeParcialPorMaterialFecha'] = $objetoTotal;
			
		}else{

			$informeEntradaPorMaterialFecha = InformeEntradaPorMaterialFecha::searchMaterialRegistroEntradas($id);
			$_SESSION['informeParcialPorMaterialFecha'] = $informeEntradaPorMaterialFecha;

		}


		

		$this->show();
	}


	function generarPDF(){
		echo "<script>window.open('Controllers/InformeEntradaPorMaterialFechaPDF.php', '_blank')</script>";
		//unset($_SESSION['informeGeneral']);
		$this->show();
	}

	function eliminar(){
		unset($_SESSION['informeParcialPorMaterialFecha']);
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