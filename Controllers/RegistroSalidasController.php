<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
/**
*  Autor: JUAN DIEGO LOPEZ BARRAGAN
*/
if (isset($_SESSION['usuario'])) {
	
	class RegistroSalidasController{
	
	function __construct()
	{
	
	}

	function save(){

		if (isset($_SESSION['listaMaterialSalida'])) {
			
			$listaMaterial = $_SESSION['listaMaterialSalida'];
			$listaCantidad = $_SESSION['listaCantidadSalida'];
			$seleccionContratista = $_SESSION['seleccionContratista'];
			$seleccionProyecto = $_SESSION['seleccionProyecto'];
			$seleccionCasa = $_SESSION['seleccionCasa'];
			$listaSeleccionCasa = $_SESSION['listaSeleccionCasa'];
			$seleccionManzana = $_SESSION['seleccionManzana'];
			$listaSeleccionManzana = $_SESSION['listaSeleccionManzana'];
			$seleccionArea = $_SESSION['seleccionArea'];
			$listaSeleccionArea = $_SESSION['listaSeleccionArea'];
			$seleccionDestino = $_SESSION['seleccionDestino'];
			$listaSeleccionDestino = $_SESSION['listaSeleccionDestino'];
			$fechaActual = date('Y-m-d');
			$hora = date('H:i:s');
			$usuario = $_SESSION['usuario']->getId();

			$i = 0;
			$listaOk = true;
			foreach ($listaMaterial as $material) {

				if ($listaCantidad[$i] <= 0) {
					$listaOk = false;
				}
				if ($listaSeleccionCasa[$i] == 0) {
					$listaOk = false;
				}
				if ($listaSeleccionManzana[$i] == 0) {
					$listaOk = false;
				}
				if ($listaSeleccionArea[$i] == 0) {
					$listaOk = false;
				}
				if ($listaSeleccionDestino[$i] == 0) {
					$listaOk = false;
				}


				$i = $i + 1;

			}
			if (empty($seleccionContratista)) {
					$listaOk = false;
			}
			if (empty($seleccionProyecto)) {
					$listaOk = false;
			}

				
			
			if ($listaOk == true) {


				// verificar si la cantidad en inventario es suficiente
				$listaCompletaMaterial = Material::all();
				$objetoTotal = [];
				$cantidadTotal = [];

				foreach ($listaCompletaMaterial as $completaMaterial) {

					$cantidadTemp = 0;
					$objetoTemp;
					$i = 0;
					$valor = false;


					foreach ($listaMaterial as $material) {
						if ($completaMaterial->getId() == $material->getId()) {

							$cantidadTemp = $cantidadTemp + $listaCantidad[$i];
							$objetoTemp = $material;
							$valor = true;
						}

						$i = $i + 1;
						
					}

					if ($valor) {
						array_push ( $objetoTotal , $objetoTemp );
						array_push ( $cantidadTotal , $cantidadTemp );
					}

				}


				$i = 0;
				$inventarioOk = true;
				foreach ($objetoTotal as $material) {
					if ($material->getSaldo() < $cantidadTotal[$i]) {
						$descripcion = $material->getDescripcion();
						$cantidadSaliente = $cantidadTotal[$i];
						$cantidadInventario = $material->getSaldo();
						$unidad = $material->getUnidad();
						echo "<script>alert('La cantidad de ${descripcion} que existe en inventario es ${cantidadInventario} ${unidad} y usted desea sacar ${cantidadSaliente} ${unidad}. ¡ Por lo tanto NO se puede continuar con esta operacion !')</script>";
						$inventarioOk = false;
					}

					$i = $i+1;
				}



				if ($inventarioOk == true) {
					$i = 0;

				//ACTUALIZA LA CANTIDAD DE MATERIAL
				foreach ($listaMaterial as $material) {
					
					$id = $material->getId();
					//Se llama al material directamente de la base de datos para actualizar 
					//todas las salidas del mismo material
					$saldoMaterial = Material::searchById($id);
					$saldo = $saldoMaterial->getSaldo();
					$minAlmacen = $saldoMaterial->getMinAlmacen();
					$descripcion = $saldoMaterial->getDescripcion();
					$unidad = $saldoMaterial->getUnidad();
					$cantidad = $listaCantidad[$i];

					$nuevoSaldo = $saldo - $cantidad;
					Material::ingresoMaterial($id, $nuevoSaldo);

					if ($nuevoSaldo <= $minAlmacen) {
						# code...
						$oMail = new PHPMailer();
						$oMail->isSMTP();
						$oMail->Host = "smtp.gmail.com";
						$oMail->Port = 587;
						$oMail->SMTPSecure = "tls";
						$oMail->SMTPAuth = true;
						$oMail->Username = "diegolopez1797@gmail.com";
						$oMail->Password = "jdlbK_32125631087";
						$oMail->setFrom("diegolopez1797@gmail.com", "Juan Diego");
						$oMail->addAddress("u20161145407@usco.edu.co");
						$oMail->Subject = "ALMACEN BERDEZ INFORMA";
						$oMail->msgHTML("¡¡¡ ALERTA !!! La cantidad de ".$descripcion." es de ".$nuevoSaldo." ".$unidad.". Por debajo o igual a ".$minAlmacen.", que es la cantidad minima que deberia existir en el almacen.");

						if (!$oMail->send()) {
							echo $oMail->ErrorInfo;
						}
					}

					$i = $i + 1;
					
				}

				$contratista = $seleccionContratista->getId();
				$proyecto = $seleccionProyecto->getId(); 

				$registroSalidas1 = new RegistroSalidas(null,$fechaActual,$hora,$usuario,$contratista,$proyecto);
				RegistroSalidas::save($registroSalidas1);

				$registroSalidas = RegistroSalidas::searchUltimoId();
				$registroSalidasFin = end($registroSalidas);

				$idUltimaSalida = $registroSalidasFin->getId();


				$i = 0;
				foreach ($listaMaterial as $material) {

					$idMaterial = $material->getId();
					$cantidad = $listaCantidad[$i];
					$casa = $seleccionCasa[$i]->getId();
					$manzana = $seleccionManzana[$i]->getId();
					$area = $seleccionArea[$i]->getId();
					$destino = $seleccionDestino[$i]->getId();
					
					$materialRegistroSalidas = new MaterialRegistroSalidas(null,$idMaterial,$idUltimaSalida,$cantidad,$casa,$manzana,$destino,$area);
					MaterialRegistroSalidas::save($materialRegistroSalidas);

					$i = $i + 1;
					
				}

				$_SESSION['idSalida'] = $idUltimaSalida;

				
				echo "<script>alert('¡ Material Sacado EXITOSAMENTE !')</script>";

				

				echo "<script>window.open('Controllers/SalidaMaterialPDF.php', '_blank')</script>";
			
				//Se borrar variables de sesion
				unset($_SESSION['listaMaterialSalida']);
				unset($_SESSION['listaCantidadSalida']);
				unset($_SESSION['seleccionContratista']);
				unset($_SESSION['seleccionProyecto']);
				unset($_SESSION['listaSeleccionCasa']);
				unset($_SESSION['seleccionCasa']);
				unset($_SESSION['listaSeleccionManzana']);
				unset($_SESSION['seleccionManzana']);
				unset($_SESSION['listaSeleccionArea']);
				unset($_SESSION['seleccionArea']);
				unset($_SESSION['listaSeleccionDestino']);
				unset($_SESSION['seleccionDestino']);


				
				$this->show();
				}else{
					$this->show();
				}


			}else{

				echo "<script>alert('¡ No se pudo sacar el Material... Porfavor verifique todas las entradas !')</script>";
				$this->show();

			}

			
		}else{

			echo "<script>alert('¡ Seleccione primero el material a Sacar !')</script>";
			$this->show();

		}
	
	}

	function notificacion($descripcion, $saldo){
		//Enviar correo 

		$oMail = new PHPMailer();
		$oMail->isSMTP();
		$oMail->Host = "smtp.gmail.com";
		$oMail->Port = 587;
		$oMail->SMTPSecure = "tls";
		$oMail->SMTPAuth = true;
		$oMail->Username = "diegolopez1797@gmail.com";
		$oMail->Password = "jdlbK_32125631087";
		$oMail->setFrom("diegolopez1797@gmail.com", "Juan Diego");
		$oMail->addAddress("u20161145407@usco.edu.co");
		$oMail->Subject = "¡¡¡ Almacen Berdez INFORMA !!!";
		$oMail->msgHTML("Material por debajo del minimo");

		if (!$oMail->send()) {
			echo $oMail->ErrorInfo;
		}
	}

	function show(){

		//Busqueda y carga de los contratista
		$listaContratista = Contratista::all();
		$_SESSION['listaContratista'] = $listaContratista;

		//Busqueda y carga de los casa
		$listaCasa = Casa::all();
		$_SESSION['listaCasa'] = $listaCasa;

		//Busqueda y carga de los manzana
		$listaManzana = Manzana::all();
		$_SESSION['listaManzana'] = $listaManzana;

		//Busqueda y carga de los area
		$listaArea = Area::all();
		$_SESSION['listaArea'] = $listaArea;

		//Busqueda y carga de los destino
		$listaDestino = Destino::all();
		$_SESSION['listaDestino'] = $listaDestino;

		$listaProyecto = Proyecto::all();
		$_SESSION['listaProyecto'] = $listaProyecto;

		//$listaMaterialCompleta = Material::all();

		require_once('Views/Salidas/salidas.php');

	}


	function searchMaterial(){


		if (isset(($_REQUEST['btnIngresar']))) {

			$_SESSION['listaCantidadSalida'] = $_REQUEST['listaCantidadSalida'];


					//CONTRATISTA
					$idContratista = $_REQUEST['idContratista'];
					if ($idContratista != 0) {
						$_SESSION['seleccionContratista'] = Contratista::searchById($idContratista);

					}

					//PROYECTO
					$idProyecto = $_REQUEST['idProyecto'];
					if ($idProyecto != 0) {
						$_SESSION['seleccionProyecto'] = Proyecto::searchById($idProyecto);

					}
					
					//CASA --------------------------------------------------------

					$_SESSION['listaSeleccionCasa'] = $_REQUEST['listaSeleccionCasa'];

					$seleccionCasa = [];

					foreach ($_SESSION['listaSeleccionCasa'] as $lista) {
						if ($lista != 0) {
							$casa = Casa::searchById($lista);
							array_push ( $seleccionCasa , $casa );
						}else{
							$vacio = null;
							array_push ( $seleccionCasa , $vacio );
						}	
					}

					$_SESSION['seleccionCasa'] = $seleccionCasa;



					//MANZANA------------------------------------------------------

					$_SESSION['listaSeleccionManzana'] = $_REQUEST['listaSeleccionManzana'];

					$seleccionManzana = [];

					foreach ($_SESSION['listaSeleccionManzana'] as $lista) {
						if ($lista != 0) {
							$manzana = Manzana::searchById($lista);
							array_push ( $seleccionManzana , $manzana );
						}else{
							$vacio = null;
							array_push ( $seleccionManzana , $vacio );
						}	
					}

					$_SESSION['seleccionManzana'] = $seleccionManzana;

					//AREA------------------------------------------------------

					$_SESSION['listaSeleccionArea'] = $_REQUEST['listaSeleccionArea'];

					$seleccionArea = [];

					foreach ($_SESSION['listaSeleccionArea'] as $lista) {
						if ($lista != 0) {
							$area = Area::searchById($lista);
							array_push ( $seleccionArea , $area );
						}else{
							$vacio = null;
							array_push ( $seleccionArea , $vacio );
						}	
					}

					$_SESSION['seleccionArea'] = $seleccionArea;


					//DESTINO------------------------------------------------------

					$_SESSION['listaSeleccionDestino'] = $_REQUEST['listaSeleccionDestino'];

					$seleccionDestino = [];

					foreach ($_SESSION['listaSeleccionDestino'] as $lista) {
						if ($lista != 0) {
							$destino = Destino::searchById($lista);
							array_push ( $seleccionDestino , $destino );
						}else{
							$vacio = null;
							array_push ( $seleccionDestino , $vacio );
						}	
					}

					$_SESSION['seleccionDestino'] = $seleccionDestino;

			
			$this->save();

			
		}else{

			if (isset($_SESSION['listaMaterialSalida'])) {
			$listaMaterialSalida = [];
			$listaMaterialSalida = $_SESSION['listaMaterialSalida'];
			}else{
				$listaMaterialSalida = [];
			}

			$codigo = $_POST['codigo'];

			$MaterialR = false;
			foreach ($listaMaterialSalida as $material) {
					if ($material->getCodigo() == $codigo) {
						$MaterialR = true;
					}
				}
			

			if (empty($codigo) or $codigo < 0) {

				$_SESSION['listaCantidadSalida'] = $_REQUEST['listaCantidadSalida'];
				echo "<script>alert('¡ No a ingresado un codigo o el valor ingresado NO ES VALIDO !')</script>";

			}else{

				$material = Material::searchByCodigo($codigo);

				if ($material->getCodigo() == $codigo) {
					array_push ( $listaMaterialSalida , $material );
					$_SESSION['listaMaterialSalida'] = $listaMaterialSalida;
					$_SESSION['listaCantidadSalida'] = $_REQUEST['listaCantidadSalida'];	


					//CONTRATISTA
					$idContratista = $_REQUEST['idContratista'];
					if ($idContratista != 0) {
						$_SESSION['seleccionContratista'] = Contratista::searchById($idContratista);

					}

					//PROYECTO
					$idProyecto = $_REQUEST['idProyecto'];
					//echo $idProyecto;
					if ($idProyecto != 0) {
						$_SESSION['seleccionProyecto'] = Proyecto::searchById($idProyecto);

					}

					//CASA --------------------------------------------------------

					$_SESSION['listaSeleccionCasa'] = $_REQUEST['listaSeleccionCasa'];

					$seleccionCasa = [];

					foreach ($_SESSION['listaSeleccionCasa'] as $lista) {
						if ($lista != 0) {
							$casa = Casa::searchById($lista);
							array_push ( $seleccionCasa , $casa );
						}else{
							$vacio = null;
							array_push ( $seleccionCasa , $vacio );
						}	
					}

					$_SESSION['seleccionCasa'] = $seleccionCasa;



					//MANZANA------------------------------------------------------

					$_SESSION['listaSeleccionManzana'] = $_REQUEST['listaSeleccionManzana'];

					$seleccionManzana = [];

					foreach ($_SESSION['listaSeleccionManzana'] as $lista) {
						if ($lista != 0) {
							$manzana = Manzana::searchById($lista);
							array_push ( $seleccionManzana , $manzana );
						}else{
							$vacio = null;
							array_push ( $seleccionManzana , $vacio );
						}	
					}

					$_SESSION['seleccionManzana'] = $seleccionManzana;

					//AREA------------------------------------------------------

					$_SESSION['listaSeleccionArea'] = $_REQUEST['listaSeleccionArea'];

					$seleccionArea = [];

					foreach ($_SESSION['listaSeleccionArea'] as $lista) {
						if ($lista != 0) {
							$area = Area::searchById($lista);
							array_push ( $seleccionArea , $area );
						}else{
							$vacio = null;
							array_push ( $seleccionArea , $vacio );
						}	
					}

					$_SESSION['seleccionArea'] = $seleccionArea;


					//DESTINO------------------------------------------------------

					$_SESSION['listaSeleccionDestino'] = $_REQUEST['listaSeleccionDestino'];

					$seleccionDestino = [];

					foreach ($_SESSION['listaSeleccionDestino'] as $lista) {
						if ($lista != 0) {
							$destino = Destino::searchById($lista);
							array_push ( $seleccionDestino , $destino );
						}else{
							$vacio = null;
							array_push ( $seleccionDestino , $vacio );
						}	
					}

					$_SESSION['seleccionDestino'] = $seleccionDestino;

	

				}else{
					echo "<script>alert('¡ El matarial buscado NO EXISTE !')</script>";
				}	
			}		
			
			$this->show();

		}

	}

	function quitarMaterial(){

		$i = $_GET['id'];

		$listaMaterialSalida = $_SESSION['listaMaterialSalida'];
		$listaCantidadSalida = $_SESSION['listaCantidadSalida'];
		$seleccionCasa = $_SESSION['seleccionCasa'];
		$seleccionManzana = $_SESSION['seleccionManzana'];
		$seleccionArea = $_SESSION['seleccionArea'];
		$seleccionDestino = $_SESSION['seleccionDestino'];

		unset($listaMaterialSalida[$i]);
		unset($listaCantidadSalida[$i]);
		unset($seleccionCasa[$i]);
		unset($seleccionManzana[$i]);
		unset($seleccionArea[$i]);
		unset($seleccionDestino[$i]);

		try {
			$_SESSION['listaMaterialSalida'] = array_values($listaMaterialSalida);
			$_SESSION['listaCantidadSalida'] = array_values($listaCantidadSalida);
			$_SESSION['seleccionCasa'] = array_values($seleccionCasa);
			$_SESSION['seleccionManzana'] = array_values($seleccionManzana);
			$_SESSION['seleccionArea'] = array_values($seleccionArea);
			$_SESSION['seleccionDestino'] = array_values($seleccionDestino);
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