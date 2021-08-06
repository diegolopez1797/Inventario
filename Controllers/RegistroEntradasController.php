<?php 

if (isset($_SESSION['usuario'])) {

class RegistroEntradasController{
	
	function __construct()
	{
		
	}

    
	function save(){

		if (isset($_SESSION['listaMaterial'])) {
			
			$listaMaterial = $_SESSION['listaMaterial'];
			$listaCantidad = $_SESSION['listaCantidad'];
			$fechaActual = date('Y-m-d');
			$hora = date('H:i:s');
			$usuario = $_SESSION['usuario']->getId();

			$i = 0;
			$listaOk = true;
			foreach ($listaMaterial as $material) {

				if ($listaCantidad[$i] <= 0) {
					$listaOk = false;
				}
				$i = $i + 1;

			}

				
			if ($listaOk == true) {

				$i = 0;
				foreach ($listaMaterial as $material) {
					
					
					$id = $material->getId();
					$saldo = $material->getSaldo();
					$cantidad = $listaCantidad[$i];

					$nuevoSaldo = $saldo + $cantidad;
					Material::ingresoMaterial($id, $nuevoSaldo);

					$i = $i + 1;
					
				}
				$registroEntradas1 = new RegistroEntradas(null,$fechaActual,$hora,$usuario);
				RegistroEntradas::save($registroEntradas1);

				$registroEntradas = RegistroEntradas::searchUltimoId();
				$registroEntradasFin = end($registroEntradas);
				$idUltimaEntrada = $registroEntradasFin->getId();


				$i = 0;
				foreach ($listaMaterial as $material) {

					$idMaterial = $material->getId();
					$cantidad = $listaCantidad[$i];
					
					$materialRegistroEntradas = new MaterialRegistroEntradas(null,$idMaterial,$idUltimaEntrada,$cantidad);
					MaterialRegistroEntradas::save($materialRegistroEntradas);

					$i = $i + 1;
					
				}

				$_SESSION['idEntrada'] = $idUltimaEntrada;

				
				echo "<script>alert('¡ Material Ingresado EXITOSAMENTE !')</script>";

				echo "<script>window.open('Controllers/EntradaMaterialPDF.php', '_blank')</script>";
			
				unset($_SESSION['listaMaterial']);
				unset($_SESSION['listaCantidad']);

				
				$this->show();


			}else{

				echo "<script>alert('¡ Por favor llene todos los campos de cantidad y/o verifique que los valores sean mayores a Cero !')</script>";
				$this->show();

			}

			
		}else{

			echo "<script>alert('¡ Seleccione primero el material a ingresar !')</script>";
			$this->show();

		}
	
	}

	function show(){

		// $listaMaterialCompleta = Material::all();
		require_once('Views/Entradas/entradas.php');
	}


	function searchMaterial(){


		if (isset(($_REQUEST['btnIngresar']))) {

			$_SESSION['listaCantidad'] = $_REQUEST['listaCantidad'];
			$this->save();

			
		}else{

			if (isset($_SESSION['listaMaterial'])) {
			$listaMaterial = [];
			$listaMaterial = $_SESSION['listaMaterial'];
			}else{
				$listaMaterial = [];
			}

			$codigo = $_POST['codigo'];

			$MaterialR = false;
			foreach ($listaMaterial as $material) {
					if ($material->getCodigo() == $codigo) {
						$MaterialR = true;
					}
				}
			

			if (empty($codigo) or $codigo < 0) {

				$_SESSION['listaCantidad'] = $_REQUEST['listaCantidad'];
				echo "<script>alert('¡ No a ingresado un codigo o el valor ingresado NO ES VALIDO !')</script>";

			}elseif ($MaterialR == true){

				$_SESSION['listaCantidad'] = $_REQUEST['listaCantidad'];
				echo "<script>alert('¡ El material solicitado ya ha sido listado !')</script>";

			}else{

				$material = Material::searchByCodigo($codigo);

				if ($material->getCodigo() == $codigo) {
					array_push ( $listaMaterial , $material );
					$_SESSION['listaMaterial'] = $listaMaterial;
					$_SESSION['listaCantidad'] = $_REQUEST['listaCantidad'];
					
				}else{
					echo "<script>alert('¡ El matarial buscado NO EXISTE !')</script>";
				}	
			}		
			
			$this->show();

		}

	}

	function quitarMaterial(){

		$i = $_GET['id'];

		$listaMaterial = $_SESSION['listaMaterial'];
		$listaCantidad = $_SESSION['listaCantidad'];

		unset($listaMaterial[$i]);
		unset($listaCantidad[$i]);

		try {
			$_SESSION['listaMaterial'] = array_values($listaMaterial);
			$_SESSION['listaCantidad'] = array_values($listaCantidad);
		}catch (Error $e) {
			
		}
		

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