<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class InformeEntradaPorFechaController
{
	
	function __construct()
	{
		
	}

	function show(){


		if (!empty($_SESSION['informeEntradaPorFechaParcial'])) {
			$_SESSION['informeEntradaPorFechaGeneral'] = $_SESSION['informeEntradaPorFechaParcial'];
		}else{
			$_SESSION['informeEntradaPorFechaTotal'] = InformeEntradaPorFecha::all();
			$_SESSION['informeEntradaPorFechaGeneral'] = $_SESSION['informeEntradaPorFechaTotal'];
		}

		//---------------------------------------------------------------

		require_once('Views/Informes/InformeEntradaPorFecha.php');
	}

	function generarPDF(){
		echo "<script>window.open('Controllers/InformeEntradaPorFechaPDF.php', '_blank')</script>";
		//unset($_SESSION['informeEntradaPorFechaParcial']);
		$this->show();
	}

	function search(){

		$fechaInicial = $_POST['fechaInicial'];
		$fechaFinal = $_POST['fechaFinal'];

		$_SESSION['informeEntradaPorFechaParcial'] = InformeEntradaPorFecha::searchByFecha($fechaInicial, $fechaFinal);
		
		$this->show();
	}

	function eliminar(){
		unset($_SESSION['informeEntradaPorFechaParcial']);
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