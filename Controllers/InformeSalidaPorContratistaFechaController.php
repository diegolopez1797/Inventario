<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class InformeSalidaPorContratistaFechaController
{
	
	function __construct()
	{
		
	}

	function show(){

		if (!empty($_SESSION['informeParcialSalidaPorContratistaFecha'])) {
			$_SESSION['informeGeneralSalidaPorContratistaFecha'] = $_SESSION['informeParcialSalidaPorContratistaFecha'];
		}else{
			$_SESSION['informeTotalSalidaPorContratistaFecha'] = InformeSalidaPorFecha::all();
			$_SESSION['informeGeneralSalidaPorContratistaFecha'] = $_SESSION['informeTotalSalidaPorContratistaFecha'];
		}

		$listaContratistaCompleta = Contratista::all();
		require_once('Views/Informes/InformeSalidaPorContratistaFecha.php');
	}


	function search(){

		$idContratista = $_POST['idContratista'];
		$fechaInicial = $_POST['fechaInicial'];
		$fechaFinal = $_POST['fechaFinal'];

		if (!empty($fechaInicial) and !empty($fechaFinal)) {

			$informeSalidaPorContratistaFecha = InformeSalidaPorFecha::searchByContratistaFecha($fechaInicial, $fechaFinal, $idContratista);

			$_SESSION['informeParcialSalidaPorContratistaFecha'] = $informeSalidaPorContratistaFecha;
			
		}else{

			$informeSalidaPorContratista = InformeSalidaPorFecha::searchByContratista($idContratista);
			$_SESSION['informeParcialSalidaPorContratistaFecha'] = $informeSalidaPorContratista;

		}
		
		$this->show();
	}


	function generarPDF(){
		echo "<script>window.open('Controllers/InformeSalidaPorContratistaFechaPDF.php', '_blank')</script>";
		//unset($_SESSION['informeGeneral']);
		$this->show();
	}

	function eliminar(){
		unset($_SESSION['informeParcialSalidaPorContratistaFecha']);
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