<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class InformeSalidaPorCasaController
{
	
	function __construct()
	{
		
	}

	function show(){

		$listaProyectoCompleta = Proyecto::all();
		$listaManzanaCompleta = Manzana::all();
		$listaCasaCompleta = Casa::all();
		

		require_once('Views/Informes/InformeSalidaPorCasa.php');
	}


	function search(){

		$idProyecto = $_POST['idProyecto'];
		$idManzana = $_POST['idManzana'];
		$idCasa = $_POST['idCasa'];

		echo $idProyecto;
		echo $idManzana;
		echo $idCasa;

		$material = Material::all();

		$informeDetallado = InformeDetallado::searchByCasa($idProyecto, $idManzana, $idCasa);

			

		$_SESSION['informeGeneralSalidaPorCasa'] = $informeDetallado;// $objetoTotal;
			
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
