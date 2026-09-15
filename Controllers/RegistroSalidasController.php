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
		if (!Permiso::usuarioPuede('salida.registrar')) {
			flash('danger', 'No tiene permiso para registrar salidas.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		if (isset($_SESSION['listaMaterialSalida'])) {
			
			$listaMaterial = $_SESSION['listaMaterialSalida'];
			$listaCantidad = $_SESSION['listaCantidadSalida'];
			$seleccionContratista = isset($_SESSION['seleccionContratista']) ? $_SESSION['seleccionContratista'] : null;
			// Lectura null-safe: si por algun motivo se llega aqui sin Proyecto (no deberia
			// pasar, ya que agregar el primer material ya lo exige), evita un Warning de
			// indice indefinido - el chequeo empty($seleccionProyecto) de abajo ya lo cubre.
			$seleccionProyecto = isset($_SESSION['seleccionProyecto']) ? $_SESSION['seleccionProyecto'] : null;
			$seleccionCasa = $_SESSION['seleccionCasa'];
			$listaSeleccionCasa = $_SESSION['listaSeleccionCasa'];
			$seleccionManzana = $_SESSION['seleccionManzana'];
			$listaSeleccionManzana = $_SESSION['listaSeleccionManzana'];
			$seleccionArea = $_SESSION['seleccionArea'];
			$listaSeleccionArea = $_SESSION['listaSeleccionArea'];
			$seleccionDestino = $_SESSION['seleccionDestino'];
			$listaSeleccionDestino = $_SESSION['listaSeleccionDestino'];
			$seleccionRubro = $_SESSION['seleccionRubro'];
			$listaSeleccionRubro = $_SESSION['listaSeleccionRubro'];
			$seleccionUbicacion = isset($_SESSION['seleccionUbicacion']) ? $_SESSION['seleccionUbicacion'] : [];
			$fechaActual = date('Y-m-d');
			$hora = date('H:i:s');
			$usuario = $_SESSION['usuario']->getId();

			// Destino y Rubro/Actividad son obligatorios para cada linea (a diferencia de
			// Casa/Manzana/Area, que siguen sin usarse). Se valida aqui, en el Controller de
			// Salida Material especificamente, y no en RegistroSalidas::registrar() (compartido
			// con la entrega de Solicitudes, donde estos dos campos siguen siendo opcionales por
			// decision de negocio ya tomada) - asi cada flujo mantiene su propia regla.
			foreach ($listaMaterial as $i => $material) {
				if (empty($seleccionDestino[$i]) || empty($seleccionRubro[$i])) {
					flash_now('warning', 'Debe seleccionar un destino y un rubro/actividad para cada material.');
					$this->show();
					return;
				}
			}

			$i = 0;
			$listaOk = true;
			foreach ($listaMaterial as $material) {

				if ($listaCantidad[$i] <= 0) {
					$listaOk = false;
				}
				// Casa/Manzana/Area dejaron de ser obligatorios: Ubicacion es ahora el destino
				// fisico principal. Su obligatoriedad condicional (solo si el proyecto ya tiene
				// hojas activas) se valida en RegistroSalidas::registrar(), unica fuente de verdad.

				$i = $i + 1;

			}
			if (empty($seleccionContratista)) {
					$listaOk = false;
			}
			if (empty($seleccionProyecto)) {
					$listaOk = false;
			}

				
			
			if ($listaOk == true) {

				try {

					$resultado = RegistroSalidas::registrar(
						$fechaActual,
						$hora,
						$usuario,
						$seleccionContratista->getId(),
						$seleccionProyecto->getId(),
						$listaMaterial,
						$listaCantidad,
						$seleccionCasa,
						$seleccionManzana,
						$seleccionArea,
						$seleccionDestino,
						$seleccionRubro,
						$seleccionUbicacion
					);

					if ($resultado['ok']) {

						if (!empty($resultado['alertasStockMinimo'])) {
							$this->notificarAlertasStockMinimo($resultado['alertasStockMinimo'], $resultado['id']);
						}

						$_SESSION['idSalida'] = $resultado['id'];

						flash_now('success', 'Material sacado exitosamente.');

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
						unset($_SESSION['listaSeleccionRubro']);
						unset($_SESSION['seleccionRubro']);
						unset($_SESSION['listaSeleccionUbicacion']);
						unset($_SESSION['seleccionUbicacion']);

					}else{

						foreach ($resultado['insuficientes'] as $insuficiente) {
							$descripcion = $insuficiente['descripcion'];
							$cantidadSaliente = $insuficiente['cantidadSolicitada'];
							$cantidadInventario = $insuficiente['saldoInventario'];
							$unidad = $insuficiente['unidad'];
							flash_now('warning', "La cantidad de {$descripcion} que existe en inventario es {$cantidadInventario} {$unidad} y usted desea sacar {$cantidadSaliente} {$unidad}. Por lo tanto no se puede continuar con esta operación.");
						}
					}

				} catch (PDOException $e) {
					flash_now('danger', 'Ocurrió un error inesperado y la operación fue cancelada. Inténtelo nuevamente.');
				} catch (Exception $e) {
					flash_now('danger', $e->getMessage());
				}

				$this->show();


			}else{

				flash_now('danger', 'No se pudo sacar el material... Por favor verifique todas las entradas.');
				$this->show();

			}

			
		}else{

			flash_now('warning', 'Seleccione primero el material a sacar.');
			$this->show();

		}
	
	}

	// Reemplaza a notificacion(): en vez de un correo por linea de material, agrupa TODAS las
	// alertas de una misma Salida en un solo mensaje, y lee los destinatarios activos desde
	// NotificacionDestinatario en vez de un unico correo fijo en mail.config.php (Fase de
	// implementacion aprobada: Gestion de Destinatarios de Notificaciones por Correo).
	function notificarAlertasStockMinimo($alertas, $idSalida){
		$destinatarios = NotificacionDestinatario::activos();
		if (empty($destinatarios)) {
			return; // Sin destinatarios activos: no hay a quien enviar, no es un error.
		}

		if (!file_exists('mail.config.php')) {
			return;
		}
		$mailConfig = require('mail.config.php');

		$oMail = new PHPMailer();
		$oMail->isSMTP();
		$oMail->Host = $mailConfig['host'];
		$oMail->Port = $mailConfig['port'];
		$oMail->SMTPSecure = $mailConfig['smtp_secure'];
		$oMail->SMTPAuth = true;
		$oMail->Username = $mailConfig['username'];
		$oMail->Password = $mailConfig['password'];
		$oMail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
		$oMail->addAddress($mailConfig['from_email'], $mailConfig['from_name']); // "To": el propio remitente
		foreach ($destinatarios as $destinatario) {
			$oMail->addBCC($destinatario->getCorreo()); // BCC: los destinatarios reales nunca se ven entre si
		}

		$oMail->Subject = "ALMACEN INFORMA";
		$oMail->msgHTML($this->construirCuerpoAlertasStockMinimo($alertas, $idSalida));

		if (!$oMail->send()) {
			error_log('Fallo el envio de alertas de stock minimo (Salida #'.$idSalida.'): ' . $oMail->ErrorInfo);
			flash_now('warning', 'La salida se registró correctamente, pero no se pudo enviar la notificación por correo.');
		}
	}

	// Cuerpo agrupado: todos los materiales que cruzaron el minimo en UNA sola Salida, en una
	// sola tabla. Fecha/hora se toman al momento de construir el correo (el envio es sincronico
	// e inmediato, coincide con el momento real de la Salida); el numero de Salida ya esta
	// disponible en $resultado['id'] al momento de llamar a este metodo, asi que se incluye
	// para trazabilidad en vez de omitirlo.
	function construirCuerpoAlertasStockMinimo($alertas, $idSalida){
		$filas = '';
		foreach ($alertas as $alerta) {
			$filas .= '<tr>'
				. '<td>' . h($alerta['descripcion']) . '</td>'
				. '<td>' . (int) $alerta['nuevoSaldo'] . ' ' . h($alerta['unidad']) . '</td>'
				. '<td>' . (int) $alerta['minAlmacen'] . ' ' . h($alerta['unidad']) . '</td>'
				. '</tr>';
		}

		return '<h3>¡¡¡ ALERTA DE INVENTARIO !!!</h3>'
			. '<p>Salida No: ' . (int) $idSalida . ' / Fecha: ' . date('Y-m-d H:i:s') . '</p>'
			. '<p>Los siguientes materiales requieren reposición — su saldo llegó al mínimo o quedó por debajo de él:</p>'
			. '<table border="1" cellpadding="4"><tr><th>Material</th><th>Saldo actual</th><th>Mínimo</th></tr>' . $filas . '</table>';
	}

	function show(){
		if (!Permiso::usuarioPuede('salida.registrar')) {
			flash('danger', 'No tiene permiso para registrar salidas.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

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
				
		//Busqueda y carga de los destino
		$listaRubro = Rubro::all();
		$_SESSION['listaRubro'] = $listaRubro;

		$listaProyecto = Proyecto::all();
		$_SESSION['listaProyecto'] = $listaProyecto;

		// El proyecto queda fijo (no editable) en cuanto la salida en curso ya tiene al menos
		// un material - una salida nunca puede mezclar proyectos entre sus lineas.
		$proyectoFijo = isset($_SESSION['listaMaterialSalida']) && count($_SESSION['listaMaterialSalida']) > 0;

		//Busqueda y carga de ubicaciones (nivel generico: etapa/manzana/casa/torre/piso...),
		//filtradas por el proyecto ya elegido en esta sesion (si aun no se elige ninguno, no
		//se ofrece ubicacion todavia para no mezclar ubicaciones de proyectos distintos).
		$listaUbicacion = isset($_SESSION['seleccionProyecto']) ? Ubicacion::hojasConRuta($_SESSION['seleccionProyecto']->getId()) : [];
		$_SESSION['listaUbicacion'] = $listaUbicacion;

		// Si el proyecto elegido ya tiene al menos una ubicacion activa (hoja), seleccionarla
		// pasa a ser obligatorio (RegistroSalidas::registrar() ya lo exige); si no tiene
		// ninguna, la vista debe permitir continuar sin bloquear al usuario.
		$proyectoTieneEstructura = isset($_SESSION['seleccionProyecto']) && !empty($listaUbicacion);

		// Arbol completo del proyecto elegido (para el selector jerarquico por niveles de la
		// vista) - se consulta UNA sola vez por proyecto (cacheado en sesion) y nunca incluye
		// ubicaciones de otros proyectos. Solo id/padreId/nombre/tipo/activo: lo minimo que el
		// JS necesita para navegar por PadreID, nunca por Tipo.
		if (isset($_SESSION['seleccionProyecto'])) {
			$idProyectoActual = $_SESSION['seleccionProyecto']->getId();
			if (!isset($_SESSION['arbolUbicacionProyectoId']) || $_SESSION['arbolUbicacionProyectoId'] != $idProyectoActual) {
				$_SESSION['arbolUbicacionProyectoId'] = $idProyectoActual;
				$_SESSION['arbolUbicacionJson'] = json_encode(array_map(function($u){
					return [
						'id' => (int) $u->getId(),
						'padreId' => $u->getPadreId() !== null ? (int) $u->getPadreId() : null,
						'nombre' => $u->getNombre(),
						'tipo' => $u->getTipo(),
						'activo' => (int) $u->getActivo(),
					];
				}, Ubicacion::todosPorProyecto($idProyectoActual)), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
			}
		} else {
			unset($_SESSION['arbolUbicacionProyectoId']);
			$_SESSION['arbolUbicacionJson'] = '[]';
		}
		$arbolUbicacionJson = $_SESSION['arbolUbicacionJson'];

		//$listaMaterialCompleta = Material::all();

		require_once('Views/Salidas/salidas.php');

	}


	function searchMaterial(){
		if (!Permiso::usuarioPuede('salida.registrar')) {
			flash('danger', 'No tiene permiso para registrar salidas.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		// "Reiniciar salida": unico punto de salida cuando el usuario quiere empezar de cero
		// con otro proyecto teniendo ya materiales en curso. Reutiliza esta misma accion (ya
		// protegida por CSRF y ya autorizada en routing.php) en vez de crear una accion nueva.
		if (isset($_REQUEST['btnReiniciar'])) {
			unset(
				$_SESSION['listaMaterialSalida'], $_SESSION['listaCantidadSalida'],
				$_SESSION['seleccionContratista'], $_SESSION['seleccionProyecto'],
				$_SESSION['listaSeleccionCasa'], $_SESSION['seleccionCasa'],
				$_SESSION['listaSeleccionManzana'], $_SESSION['seleccionManzana'],
				$_SESSION['listaSeleccionArea'], $_SESSION['seleccionArea'],
				$_SESSION['listaSeleccionDestino'], $_SESSION['seleccionDestino'],
				$_SESSION['listaSeleccionRubro'], $_SESSION['seleccionRubro'],
				$_SESSION['listaSeleccionUbicacion'], $_SESSION['seleccionUbicacion'],
				$_SESSION['arbolUbicacionProyectoId'], $_SESSION['arbolUbicacionJson'],
				$_SESSION['listaUbicacion']
			);
			$this->show();
			return;
		}

		// "Continuar" del Estado 1 (sin proyecto) al Estado 2 (proyecto elegido, sin
		// materiales todavia) - fija el proyecto de la salida sin exigir todavia un codigo de
		// material (la unica forma de fijar Proyecto antes, agregar-material, requeria codigo).
		if (isset($_REQUEST['btnSeleccionarProyecto'])) {
			$idProyecto = isset($_REQUEST['idProyecto']) ? $_REQUEST['idProyecto'] : 0;
			if ($idProyecto != 0) {
				$_SESSION['seleccionProyecto'] = Proyecto::searchById($idProyecto);
			}
			$this->show();
			return;
		}

		// El proyecto queda fijo (no se puede cambiar) en cuanto la salida en curso ya tiene
		// al menos un material - se calcula ANTES de cualquier mutacion de este request, para
		// que un material que se este agregando en este mismo envio no cuente todavia.
		$habiaMaterialesAntes = isset($_SESSION['listaMaterialSalida']) && count($_SESSION['listaMaterialSalida']) > 0;

		// Una salida siempre pertenece a un Proyecto: si todavia no hay uno en sesion ni viene
		// uno en este mismo envio, no se agrega ningun material (evita ademas cualquier acceso
		// a un Proyecto inexistente mas adelante, sin Warning ni Fatal Error).
		if (!isset($_REQUEST['btnIngresar'])) {
			$idProyectoEntrante = isset($_REQUEST['idProyecto']) ? $_REQUEST['idProyecto'] : 0;
			$hayProyecto = isset($_SESSION['seleccionProyecto']) || $idProyectoEntrante != 0;
			if (!$hayProyecto) {
				flash_now('warning', 'Debe seleccionar un proyecto antes de agregar materiales.');
				$this->show();
				return;
			}
		}

		if (isset(($_REQUEST['btnIngresar']))) {

			$_SESSION['listaCantidadSalida'] = isset($_REQUEST['listaCantidadSalida']) ? $_REQUEST['listaCantidadSalida'] : [];


					//CONTRATISTA
					$idContratista = $_REQUEST['idContratista'];
					if ($idContratista != 0) {
						$_SESSION['seleccionContratista'] = Contratista::searchById($idContratista);

					}

					//PROYECTO
					$idProyectoSolicitado = $_REQUEST['idProyecto'];
					$idProyectoActual = isset($_SESSION['seleccionProyecto']) ? $_SESSION['seleccionProyecto']->getId() : null;
					$intentaCambiarProyecto = $idProyectoSolicitado != 0 && $idProyectoActual !== null && (int) $idProyectoActual !== (int) $idProyectoSolicitado;

					if ($intentaCambiarProyecto && $habiaMaterialesAntes) {
						// El proyecto ya quedo fijo (la salida en curso ya tiene materiales) -
						// se ignora el intento de cambio, incluso si llega manipulado por HTTP
						// directo, y el proyecto de sesion no se toca.
						flash_now('warning', 'Esta salida ya tiene materiales seleccionados. El proyecto no se puede cambiar, use "Reiniciar salida" para empezar de nuevo con otro proyecto.');
						$idProyecto = $idProyectoActual;
						$proyectoCambio = false;
					} else {
						$idProyecto = $idProyectoSolicitado;
						// Si el proyecto cambia (solo posible cuando aun no hay materiales),
						// cualquier Ubicacion ya elegida en este mismo envio (del arbol viejo
						// aun no refrescado en el cliente) debe descartarse.
						$proyectoCambio = $intentaCambiarProyecto;
						if ($idProyecto != 0) {
							$_SESSION['seleccionProyecto'] = Proyecto::searchById($idProyecto);
						}
					}

					//CASA --------------------------------------------------------

					$_SESSION['listaSeleccionCasa'] = isset($_REQUEST['listaSeleccionCasa']) ? $_REQUEST['listaSeleccionCasa'] : [];

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

					$_SESSION['listaSeleccionManzana'] = isset($_REQUEST['listaSeleccionManzana']) ? $_REQUEST['listaSeleccionManzana'] : [];

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

					$_SESSION['listaSeleccionArea'] = isset($_REQUEST['listaSeleccionArea']) ? $_REQUEST['listaSeleccionArea'] : [];

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

					$_SESSION['listaSeleccionDestino'] = isset($_REQUEST['listaSeleccionDestino']) ? $_REQUEST['listaSeleccionDestino'] : [];

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

					//RUBRO------------------------------------------------------

					$_SESSION['listaSeleccionRubro'] = isset($_REQUEST['listaSeleccionRubro']) ? $_REQUEST['listaSeleccionRubro'] : [];

					$seleccionRubro = [];

					foreach ($_SESSION['listaSeleccionRubro'] as $lista) {
						if ($lista != 0) {
							$rubro = Rubro::searchById($lista);
							array_push ( $seleccionRubro , $rubro );
						}else{
							$vacio = null;
							array_push ( $seleccionRubro , $vacio );
						}	
					}

					$_SESSION['seleccionRubro'] = $seleccionRubro;

					//UBICACION (generica: etapa/manzana/casa/torre/piso...) ------------------

					$_SESSION['listaSeleccionUbicacion'] = isset($_REQUEST['listaSeleccionUbicacion']) ? $_REQUEST['listaSeleccionUbicacion'] : [];

					$seleccionUbicacion = [];

					foreach ($_SESSION['listaSeleccionUbicacion'] as $lista) {
						if ($lista != 0 && !$proyectoCambio) {
							$ubicacion = Ubicacion::searchById($lista);
							array_push ( $seleccionUbicacion , $ubicacion );
						}else{
							$vacio = null;
							array_push ( $seleccionUbicacion , $vacio );
						}
					}

					$_SESSION['seleccionUbicacion'] = $seleccionUbicacion;


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

				$_SESSION['listaCantidadSalida'] = isset($_REQUEST['listaCantidadSalida']) ? $_REQUEST['listaCantidadSalida'] : [];
				flash_now('warning', 'No ha ingresado un código o el valor ingresado no es válido.');

			}else{

				$material = Material::searchByCodigo($codigo);

				if ($material->getCodigo() == $codigo) {
					array_push ( $listaMaterialSalida , $material );
					$_SESSION['listaMaterialSalida'] = $listaMaterialSalida;
					$_SESSION['listaCantidadSalida'] = isset($_REQUEST['listaCantidadSalida']) ? $_REQUEST['listaCantidadSalida'] : [];	


					//CONTRATISTA
					$idContratista = $_REQUEST['idContratista'];
					if ($idContratista != 0) {
						$_SESSION['seleccionContratista'] = Contratista::searchById($idContratista);

					}

					//PROYECTO
					//echo $idProyecto;
					$idProyectoSolicitado = $_REQUEST['idProyecto'];
					$idProyectoActual = isset($_SESSION['seleccionProyecto']) ? $_SESSION['seleccionProyecto']->getId() : null;
					$intentaCambiarProyecto = $idProyectoSolicitado != 0 && $idProyectoActual !== null && (int) $idProyectoActual !== (int) $idProyectoSolicitado;

					if ($intentaCambiarProyecto && $habiaMaterialesAntes) {
						// El proyecto ya quedo fijo (la salida en curso ya tiene materiales) -
						// se ignora el intento de cambio, incluso si llega manipulado por HTTP
						// directo, y el proyecto de sesion no se toca.
						flash_now('warning', 'Esta salida ya tiene materiales seleccionados. El proyecto no se puede cambiar, use "Reiniciar salida" para empezar de nuevo con otro proyecto.');
						$idProyecto = $idProyectoActual;
						$proyectoCambio = false;
					} else {
						$idProyecto = $idProyectoSolicitado;
						// Si el proyecto cambia (solo posible cuando aun no hay materiales),
						// cualquier Ubicacion ya elegida en este mismo envio (del arbol viejo
						// aun no refrescado en el cliente) debe descartarse.
						$proyectoCambio = $intentaCambiarProyecto;
						if ($idProyecto != 0) {
							$_SESSION['seleccionProyecto'] = Proyecto::searchById($idProyecto);
						}
					}

					//CASA --------------------------------------------------------

					$_SESSION['listaSeleccionCasa'] = isset($_REQUEST['listaSeleccionCasa']) ? $_REQUEST['listaSeleccionCasa'] : [];

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

					$_SESSION['listaSeleccionManzana'] = isset($_REQUEST['listaSeleccionManzana']) ? $_REQUEST['listaSeleccionManzana'] : [];

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

					$_SESSION['listaSeleccionArea'] = isset($_REQUEST['listaSeleccionArea']) ? $_REQUEST['listaSeleccionArea'] : [];

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

					$_SESSION['listaSeleccionDestino'] = isset($_REQUEST['listaSeleccionDestino']) ? $_REQUEST['listaSeleccionDestino'] : [];

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

					//RUBRO------------------------------------------------------

					$_SESSION['listaSeleccionRubro'] = isset($_REQUEST['listaSeleccionRubro']) ? $_REQUEST['listaSeleccionRubro'] : [];

					$seleccionRubro = [];

					foreach ($_SESSION['listaSeleccionRubro'] as $lista) {
						if ($lista != 0) {
							$rubro = Rubro::searchById($lista);
							array_push ( $seleccionRubro , $rubro );
						}else{
							$vacio = null;
							array_push ( $seleccionRubro , $vacio );
						}	
					}

					$_SESSION['seleccionRubro'] = $seleccionRubro;

					//UBICACION (generica: etapa/manzana/casa/torre/piso...) ------------------

					$_SESSION['listaSeleccionUbicacion'] = isset($_REQUEST['listaSeleccionUbicacion']) ? $_REQUEST['listaSeleccionUbicacion'] : [];

					$seleccionUbicacion = [];

					foreach ($_SESSION['listaSeleccionUbicacion'] as $lista) {
						if ($lista != 0 && !$proyectoCambio) {
							$ubicacion = Ubicacion::searchById($lista);
							array_push ( $seleccionUbicacion , $ubicacion );
						}else{
							$vacio = null;
							array_push ( $seleccionUbicacion , $vacio );
						}
					}

					$_SESSION['seleccionUbicacion'] = $seleccionUbicacion;

				}else{
					flash_now('warning', 'El material buscado no existe.');
				}
			}
			
			$this->show();

		}

	}

	function quitarMaterial(){
		if (!Permiso::usuarioPuede('salida.registrar')) {
			flash('danger', 'No tiene permiso para registrar salidas.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$i = $_GET['id'];

		$listaMaterialSalida = $_SESSION['listaMaterialSalida'];
		$listaCantidadSalida = $_SESSION['listaCantidadSalida'];
		$seleccionCasa = $_SESSION['seleccionCasa'];
		$seleccionManzana = $_SESSION['seleccionManzana'];
		$seleccionArea = $_SESSION['seleccionArea'];
		$seleccionDestino = $_SESSION['seleccionDestino'];
		$seleccionRubro = $_SESSION['seleccionRubro'];
		$seleccionUbicacion = isset($_SESSION['seleccionUbicacion']) ? $_SESSION['seleccionUbicacion'] : [];

		unset($listaMaterialSalida[$i]);
		unset($listaCantidadSalida[$i]);
		unset($seleccionCasa[$i]);
		unset($seleccionManzana[$i]);
		unset($seleccionArea[$i]);
		unset($seleccionDestino[$i]);
		unset($seleccionRubro[$i]);
		unset($seleccionUbicacion[$i]);

		try {
			$_SESSION['listaMaterialSalida'] = array_values($listaMaterialSalida);
			$_SESSION['listaCantidadSalida'] = array_values($listaCantidadSalida);
			$_SESSION['seleccionCasa'] = array_values($seleccionCasa);
			$_SESSION['seleccionManzana'] = array_values($seleccionManzana);
			$_SESSION['seleccionArea'] = array_values($seleccionArea);
			$_SESSION['seleccionDestino'] = array_values($seleccionDestino);
			$_SESSION['seleccionRubro'] = array_values($seleccionRubro);
			$_SESSION['seleccionUbicacion'] = array_values($seleccionUbicacion);
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
