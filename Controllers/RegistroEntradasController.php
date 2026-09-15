<?php 

if (isset($_SESSION['usuario'])) {

class RegistroEntradasController{
	
	function __construct()
	{
		
	}

    
	function save(){
		if (!Permiso::usuarioPuede('entrada.registrar')) {
			flash('danger', 'No tiene permiso para registrar entradas.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		if (isset($_SESSION['listaMaterial'])) {
			
			$listaMaterial = $_SESSION['listaMaterial'];
			$listaCantidad = $_SESSION['listaCantidad'];
			$seleccionDestino = $_SESSION['seleccionDestinoEntrada'];
			$listaSeleccionDestino = $_SESSION['listaSeleccionDestinoEntrada'];
			$listaCostoUnitario = isset($_SESSION['listaCostoUnitario']) ? $_SESSION['listaCostoUnitario'] : [];
			$seleccionProveedorEntrada = isset($_SESSION['seleccionProveedorEntrada']) ? $_SESSION['seleccionProveedorEntrada'] : null;
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

				try {

					$idProveedorEntrada = $seleccionProveedorEntrada !== null ? $seleccionProveedorEntrada->getId() : null;
					$idUltimaEntrada = RegistroEntradas::registrar($fechaActual, $hora, $usuario, $listaMaterial, $listaCantidad, $seleccionDestino, $listaCostoUnitario, $idProveedorEntrada);

					$_SESSION['idEntrada'] = $idUltimaEntrada;

					flash_now('success', 'Material ingresado exitosamente.');

					echo "<script>window.open('Controllers/EntradaMaterialPDF.php', '_blank')</script>";

					unset($_SESSION['listaMaterial']);
					unset($_SESSION['listaCantidad']);
					unset($_SESSION['listaDestinoEntrada']);
					unset($_SESSION['listaCostoUnitario']);
					unset($_SESSION['seleccionProveedorEntrada']);

				} catch (PDOException $e) {
					flash_now('danger', 'Ocurrió un error inesperado y la operación fue cancelada. Inténtelo nuevamente.');
				}

				$this->show();


			}else{

				flash_now('warning', 'Por favor llene todos los campos de cantidad y/o verifique que los valores sean mayores a cero.');
				$this->show();

			}

			
		}else{

			flash_now('warning', 'Seleccione primero el material a ingresar.');
			$this->show();

		}
	
	}

	function show(){
		if (!Permiso::usuarioPuede('entrada.registrar')) {
			flash('danger', 'No tiene permiso para registrar entradas.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		// $listaMaterialCompleta = Material::all();
		// Busqueda y carga de los rubros
		$listaDestino = Destino::all();
		$_SESSION['listaDestinoEntrada'] = $listaDestino;

		$listaProveedor = Proveedor::all();
		$_SESSION['listaProveedor'] = $listaProveedor;

		require_once('Views/Entradas/entradas.php');
	}


	function searchMaterial(){
		if (!Permiso::usuarioPuede('entrada.registrar')) {
			flash('danger', 'No tiene permiso para registrar entradas.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

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

			//COSTO (opcional) y PROVEEDOR (a nivel de cabecera, igual que Contratista en Salidas) --

			$_SESSION['listaCostoUnitario'] = isset($_REQUEST['listaCostoUnitario']) ? $_REQUEST['listaCostoUnitario'] : [];

			$idProveedor = isset($_REQUEST['idProveedor']) ? $_REQUEST['idProveedor'] : 0;
			if ($idProveedor != 0) {
				$_SESSION['seleccionProveedorEntrada'] = Proveedor::searchById($idProveedor);
			}

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
				flash_now('warning', 'No ha ingresado un código o el valor ingresado no es válido.');

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

					//COSTO (opcional) y PROVEEDOR (a nivel de cabecera, igual que Contratista en Salidas) --

					$_SESSION['listaCostoUnitario'] = isset($_REQUEST['listaCostoUnitario']) ? $_REQUEST['listaCostoUnitario'] : [];

					$idProveedor = isset($_REQUEST['idProveedor']) ? $_REQUEST['idProveedor'] : 0;
					if ($idProveedor != 0) {
						$_SESSION['seleccionProveedorEntrada'] = Proveedor::searchById($idProveedor);
					}

				}else{
					flash_now('warning', 'El material buscado no existe.');
				}
			}		
			
			$this->show();

		}

	}

	function quitarMaterial(){
		if (!Permiso::usuarioPuede('entrada.registrar')) {
			flash('danger', 'No tiene permiso para registrar entradas.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$i = $_GET['id'];

		$listaMaterial = $_SESSION['listaMaterial'];
		$listaCantidad = $_SESSION['listaCantidad'];
		$seleccionDestinoEntrada = $_SESSION['seleccionDestinoEntrada'];
		$listaCostoUnitario = isset($_SESSION['listaCostoUnitario']) ? $_SESSION['listaCostoUnitario'] : [];

		unset($listaMaterial[$i]);
		unset($listaCantidad[$i]);
		unset($seleccionDestinoEntrada[$i]);
		unset($listaCostoUnitario[$i]);

		try {
			$_SESSION['listaMaterial'] = array_values($listaMaterial);
			$_SESSION['listaCantidad'] = array_values($listaCantidad);
			$_SESSION['seleccionDestinoEntrada'] = array_values($seleccionDestinoEntrada);
			$_SESSION['listaCostoUnitario'] = array_values($listaCostoUnitario);
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