<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class InformeSalidaPorFechaController
{
	
	function __construct()
	{
		
	}

	function show(){


		if (!empty($_SESSION['informeSalidaPorFechaParcial'])) {
			$_SESSION['informeSalidaPorFechaGeneral'] = $_SESSION['informeSalidaPorFechaParcial'];
		}else{
			$_SESSION['informeSalidaPorFechaTotal'] = InformeSalidaPorFecha::all();
			$_SESSION['informeSalidaPorFechaGeneral'] = $_SESSION['informeSalidaPorFechaTotal'];
		}

		//---------------------------------------------------------------

		require_once('Views/Informes/InformeSalidaPorFecha.php');
	}

	function generarPDF(){
		echo "<script>window.open('Controllers/InformeSalidaPorFechaPDF.php', '_blank')</script>";
		//unset($_SESSION['informeEntradaParcial']);
		$this->show();
	}

	function search(){

		$fechaInicial = $_POST['fechaInicial'];
		$fechaFinal = $_POST['fechaFinal'];

		$_SESSION['informeSalidaPorFechaParcial'] = InformeSalidaPorFecha::searchByFecha($fechaInicial, $fechaFinal);
		
		$this->show();
	}

	function eliminar(){
		unset($_SESSION['informeSalidaPorFechaParcial']);
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