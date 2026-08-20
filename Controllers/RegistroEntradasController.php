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
			$seleccionDestino = $_SESSION['seleccionDestinoEntrada'];
			$listaSeleccionDestino = $_SESSION['listaSeleccionDestinoEntrada'];
			$fechaActual = date('Y-m-d');
			$hora = date('H:i:s');
			$usuario = $_SESSION['usuario']->getId();

			$i = 0;
			$listaOk = true;
			foreach ($listaMaterial as $material) {

				if ($listaCantidad[$i] <= 0) {
					$listaOk = false;
				}

				if ($listaSeleccionDestino[$i] == 0) {
					$listaOk = false;
				}

				$i = $i + 1;

			}

				
			if ($listaOk == true) {

				$i = 0;
				foreach ($listaMaterial as $material) {

					$id = $material->getId();
					//Se llama al material directamente de la base de datos para actualizar 
					//todas las salidas del mismo material
					$saldoMaterial = Material::searchById($id);
					$saldo = $saldoMaterial->getSaldo();
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
					$destino = $seleccionDestino[$i]->getId();
					
					$materialRegistroEntradas = new MaterialRegistroEntradas(null,$idMaterial,$idUltimaEntrada,$cantidad,$destino);
					MaterialRegistroEntradas::save($materialRegistroEntradas);

					$i = $i + 1;
					
				}

				$_SESSION['idEntrada'] = $idUltimaEntrada;

				
				echo "<script>alert('¡ Material Ingresado EXITOSAMENTE !')</script>";

				echo "<script>window.open('Controllers/EntradaMaterialPDF.php', '_blank')</script>";
			
				unset($_SESSION['listaMaterial']);
				unset($_SESSION['listaCantidad']);
				unset($_SESSION['listaDestinoEntrada']);

				
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
		// Busqueda y carga de los rubros
		$listaDestino = Destino::all();
		$_SESSION['listaDestinoEntrada'] = $listaDestino;

		require_once('Views/Entradas/entradas.php');
	}


	function searchMaterial(){


		if (isset(($_REQUEST['btnIngresar']))) {

			$_SESSION['listaCantidad'] = $_REQUEST['listaCantidad'];

			$_SESSION['listaSeleccionDestinoEntrada'] = $_REQUEST['listaSeleccionDestinoEntrada'];

			$seleccionDestino = [];

			foreach ($_SESSION['listaSeleccionDestinoEntrada'] as $lista) {
				if ($lista != 0) {
					$destino = Destino::searchById($lista);
					array_push ( $seleccionDestino , $destino );
				}else{
					$vacio = null;
					array_push ( $seleccionDestino , $vacio );
				}	
			}

			$_SESSION['seleccionDestinoEntrada'] = $seleccionDestino;

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

			//}elseif ($MaterialR == true){

			//	$_SESSION['listaCantidad'] = $_REQUEST['listaCantidad'];
			//	echo "<script>alert('¡ El material solicitado ya ha sido listado !')</script>";

			}else{

				$material = Material::searchByCodigo($codigo);

				if ($material->getCodigo() == $codigo) {
					array_push ( $listaMaterial , $material );
					$_SESSION['listaMaterial'] = $listaMaterial;
					$_SESSION['listaCantidad'] = $_REQUEST['listaCantidad'];

					//RUBRO --------------------------------------------------------

					$_SESSION['listaSeleccionDestinoEntrada'] = $_REQUEST['listaSeleccionDestinoEntrada'];

					$seleccionDestino = [];

					foreach ($_SESSION['listaSeleccionDestinoEntrada'] as $lista) {
						if ($lista != 0) {
							$destino = Destino::searchById($lista);
							array_push ( $seleccionDestino , $destino );
						}else{
							$vacio = null;
							array_push ( $seleccionDestino , $vacio );
						}	
					}

					$_SESSION['seleccionDestinoEntrada'] = $seleccionDestino;
					
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
		$seleccionDestinoEntrada = $_SESSION['seleccionDestinoEntrada'];

		unset($listaMaterial[$i]);
		unset($listaCantidad[$i]);
		unset($seleccionDestinoEntrada[$i]);

		try {
			$_SESSION['listaMaterial'] = array_values($listaMaterial);
			$_SESSION['listaCantidad'] = array_values($listaCantidad);
			$_SESSION['seleccionDestinoEntrada'] = array_values($seleccionDestinoEntrada);
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