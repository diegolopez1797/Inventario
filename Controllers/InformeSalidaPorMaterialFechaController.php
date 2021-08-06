<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class InformeSalidaPorMaterialFechaController
{
	
	function __construct()
	{
		
	}

	function show(){

		if (!empty($_SESSION['informeParcialSalidaPorMaterialFecha'])) {
			$_SESSION['informeGeneralSalidaPorMaterialFecha'] = $_SESSION['informeParcialSalidaPorMaterialFecha'];
		}else{
			$_SESSION['informeTotalSalidaPorMaterialFecha'] = InformeSalidaPorMaterialFecha::all();
			$_SESSION['informeGeneralSalidaPorMaterialFecha'] = $_SESSION['informeTotalSalidaPorMaterialFecha'];
		}
		

		$listaMaterialCompleta = Material::all();
		require_once('Views/Informes/InformeSalidaPorMaterialFecha.php');
	}


	function search(){

		$id = $_POST['id'];
		$fechaInicial = $_POST['fechaInicial'];
		$fechaFinal = $_POST['fechaFinal'];

		if (!empty($fechaInicial) and !empty($fechaFinal)) {

			$informeSalidaPorFecha = InformeSalidaPorFecha::searchByFecha($fechaInicial, $fechaFinal);
			$informeSalidaPorMaterialFecha = InformeSalidaPorMaterialFecha::searchMaterialRegistroSalidas($id);

			$objetoTotal = [];
			foreach ($informeSalidaPorMaterialFecha as $salidaMaterial) {
				foreach ($informeSalidaPorFecha as $salidaFecha) {
					if (($salidaMaterial->getRegistroSalidasId()) == ($salidaFecha->getId())) {
						array_push ( $objetoTotal , $salidaMaterial );
					}
				}
			}

			$_SESSION['informeParcialSalidaPorMaterialFecha'] = $objetoTotal;
			
		}else{

			$informeSalidaPorMaterialFecha = InformeSalidaPorMaterialFecha::searchMaterialRegistroSalidas($id);
			$_SESSION['informeParcialSalidaPorMaterialFecha'] = $informeSalidaPorMaterialFecha;

		}
		
		$this->show();
	}


	function generarPDF(){
		echo "<script>window.open('Controllers/InformeSalidaPorMaterialFechaPDF.php', '_blank')</script>";
		//unset($_SESSION['informeGeneral']);
		$this->show();
	}

	function eliminar(){
		unset($_SESSION['informeParcialSalidaPorMaterialFecha']);
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