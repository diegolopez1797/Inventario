<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class InformeDetalladoController
{
	
	function __construct()
	{
		
	}

	function show(){

		if (!empty($_SESSION['materialParcial'])) {
			$_SESSION['materialGeneral'] = $_SESSION['materialParcial'];
		}else{
			$_SESSION['materialTotal'] = Material::all();
			$_SESSION['materialGeneral'] = $_SESSION['materialTotal'];
		}


		$listaMaterialCompleta = Material::all();
		$listaProyectoCompleta = Proyecto::all();
		

		require_once('Views/Informes/InformeDetallado.php');
	}


	function searchDescripcion(){

		if (isset(($_REQUEST['btnConsultar']))) {

			$idMaterial = $_POST['idMaterial'];
			$idProyecto = $_POST['idProyecto'];

			$cantidadR = $_POST['cantidad'];
			$manzana = Manzana::all();
			$casa = Casa::all();

			$informeDetallado = InformeDetallado::searchByMaterial($idMaterial, $idProyecto);

			$cantidadTotal = [];
			$objetoTotal = [];
			foreach ($manzana as $man) {
				foreach ($casa as $cas) {
					$cantidad = 0;
					$objeto;
					$despacho = false;
					foreach ($informeDetallado as $detallado) {
						if (($man->getId() == $detallado->getManzanaId()) and ($cas->getId() == $detallado->getCasaId())) {
							# code...
							$cantidad = $cantidad + $detallado->getCantidad();
							$objeto = $detallado;
							$despacho = true;

						}
					}

					if ($despacho and $cantidad > $cantidadR) {
						$objeto->setCantidad($cantidad);
						array_push ( $objetoTotal , $objeto );
						

					}
				}
			}

			$_SESSION['informeDetallado'] = $objetoTotal;
			$this->show();

		}

	}

	function detalladoMaterial(){
		$idProyecto = $_GET['proyecto'];
		$idManzana = $_GET['manzana'];
		$idCasa = $_GET['casa'];
		$idMaterial = $_GET['material'];

		$informeDetalladoMaterial = InformeDetallado::searchByMaterialDetalle($idProyecto, $idManzana, $idCasa, $idMaterial);

		require_once('Views/Informes/InformeDetalladoMaterial.php');


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



?>