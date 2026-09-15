<?php 
/**
* 
*/


class RegistroSalidas
{
	private $Id;
	private $Fecha;
	private $Hora;
	private $Usuario;
	private $contratistaId;
	private $proyectoId;

	
	function __construct($Id, $Fecha, $Hora, $Usuario, $contratistaId, $proyectoId)
	{
		$this->setId($Id);
		$this->setFecha($Fecha);
		$this->setHora($Hora);
		$this->setUsuario($Usuario);
		$this->setContratista($contratistaId);
		$this->setProyecto($proyectoId);	
	}

	public function getId(){
		return $this->Id;
	}

	public function setId($Id){
		$this->Id = $Id;
	}

	public function getFecha(){
		return $this->Fecha;
	}

	public function setFecha($Fecha){
		$this->Fecha = $Fecha;
	}

	public function getHora(){
		return $this->Hora;
	}

	public function setHora($Hora){
		$this->Hora = $Hora;
	}

	public function getUsuario(){
		return $this->Usuario;
	}

	public function setUsuario($Usuario){
		$this->Usuario = $Usuario;
	}

	public function getContratista(){
		return $this->contratistaId;
	}

	public function setContratista($contratistaId){
		$this->contratistaId = $contratistaId;
	}

	public function getProyecto(){
		return $this->proyectoId;
	}

	public function setProyecto($proyectoId){
		$this->proyectoId = $proyectoId;
	}



	public static function save($registroSalidas1){
		$db = Db::getConnect();
		
		$insert=$db->prepare('INSERT INTO registro_salidas VALUES (null,:fechaActual,:Hora,:UsuarioID,:ContratistaID,:ProyectoID)');
		$insert->bindValue('fechaActual',$registroSalidas1->getFecha());
		$insert->bindValue('Hora',$registroSalidas1->getHora());
		$insert->bindValue('UsuarioID',$registroSalidas1->getUsuario());
		$insert->bindValue('ContratistaID',$registroSalidas1->getContratista());
		$insert->bindValue('ProyectoID',$registroSalidas1->getProyecto());
		$insert->execute();

		return $insert;
	}

	public static function searchSalida($Id){
		$db = Db::getConnect();

		$select = $db->prepare('SELECT * FROM registro_salidas WHERE ID=:ID');
		$select->bindValue('ID',$Id);
		$select->execute();

		$salida = $select->fetch();

		$registroSalidas = new RegistroSalidas($salida['ID'],$salida['Fecha'],$salida['Hora'],$salida['UsuarioID'],$salida['ContratistaID'],$salida['ProyectoID']);

		return $registroSalidas;

	}

	//--------------------------------------------------------------------------------------------------------
	// Agrupa la cantidad solicitada por material (puede repetirse en varias lineas) y la compara
	// contra el saldo actual. Devuelve un arreglo con los materiales sin existencia suficiente
	// (vacio si hay stock para todos).
	public static function validarExistencia($listaMaterial, $listaCantidad){
		$listaCompletaMaterial = Material::all();
		$objetoTotal = [];
		$cantidadTotal = [];

		foreach ($listaCompletaMaterial as $completaMaterial) {

			$cantidadTemp = 0;
			$objetoTemp = null;
			$valor = false;

			foreach ($listaMaterial as $i => $material) {
				if ($completaMaterial->getId() == $material->getId()) {
					$cantidadTemp = $cantidadTemp + $listaCantidad[$i];
					$objetoTemp = $material;
					$valor = true;
				}
			}

			if ($valor) {
				array_push($objetoTotal, $objetoTemp);
				array_push($cantidadTotal, $cantidadTemp);
			}
		}

		$insuficientes = [];
		foreach ($objetoTotal as $i => $material) {
			if ($material->getSaldo() < $cantidadTotal[$i]) {
				$insuficientes[] = [
					'descripcion' => $material->getDescripcion(),
					'unidad' => $material->getUnidad(),
					'saldoInventario' => $material->getSaldo(),
					'cantidadSolicitada' => $cantidadTotal[$i],
				];
			}
		}

		return $insuficientes;
	}

	// Registra una salida completa (cabecera + detalle de materiales) en una unica transaccion.
	// Antes de escribir nada valida existencia; si no hay stock, no abre transaccion y devuelve
	// el detalle de los materiales insuficientes. Si algo falla durante la escritura, revierte todo.
	public static function registrar($fecha, $hora, $usuarioId, $contratistaId, $proyectoId, $listaMaterial, $listaCantidad, $listaCasa, $listaManzana, $listaArea, $listaDestino, $listaRubro, $listaUbicacion = []){

		// Unica fuente de verdad de la regla de Ubicacion para Salidas (Model, no confia en
		// lo que ya haya filtrado el formulario/sesion - resiste manipulacion directa de
		// parametros HTTP). Ubicacion::hojasConRuta() ya filtra por proyecto, Activo=1 y "hoja"
		// (sin hijos activos), asi que pertenencia a esa lista implica las 3 reglas a la vez.
		$hojasValidas = array_column(Ubicacion::hojasConRuta($proyectoId), 'id');
		$proyectoTieneEstructura = !empty($hojasValidas);

		foreach ($listaUbicacion as $ubicacion) {
			if ($ubicacion === null) {
				if ($proyectoTieneEstructura) {
					throw new Exception('Este proyecto tiene ubicaciones activas: debe seleccionar una para cada material.');
				}
				continue;
			}
			if (!in_array((int) $ubicacion->getId(), $hojasValidas, true)) {
				throw new Exception('La ubicación seleccionada no es una hoja activa de este proyecto.');
			}
		}

		$insuficientes = self::validarExistencia($listaMaterial, $listaCantidad);
		if (!empty($insuficientes)) {
			return ['ok' => false, 'insuficientes' => $insuficientes];
		}

		$db = Db::getConnect();

		try {
			$db->beginTransaction();

			$alertasStockMinimo = [];
			$costoPorLinea = [];
			foreach ($listaMaterial as $i => $material) {
				$id = $material->getId();
				$saldoMaterial = Material::searchById($id);
				$saldo = $saldoMaterial->getSaldo();
				$minAlmacen = $saldoMaterial->getMinAlmacen();
				$cantidad = $listaCantidad[$i];

				// Snapshot del costo promedio vigente al momento de la salida (inmutable de aqui
				// en adelante); la salida nunca modifica el CostoPromedio del material, solo lo registra.
				$costoPorLinea[$i] = $saldoMaterial->getCostoPromedio();

				$nuevoSaldo = $saldo - $cantidad;
				Material::ingresoMaterial($id, $nuevoSaldo);

				if ($nuevoSaldo <= $minAlmacen) {
					$alertasStockMinimo[] = [
						'descripcion' => $saldoMaterial->getDescripcion(),
						'nuevoSaldo' => $nuevoSaldo,
						'unidad' => $saldoMaterial->getUnidad(),
						'minAlmacen' => $minAlmacen,
					];
				}
			}

			$registroSalidas = new RegistroSalidas(null, $fecha, $hora, $usuarioId, $contratistaId, $proyectoId);
			self::save($registroSalidas);
			$idRegistroSalidas = $db->lastInsertId();

			$lineasParaAuditoria = [];
			foreach ($listaMaterial as $i => $material) {
				$ubicacionId = (isset($listaUbicacion[$i]) && $listaUbicacion[$i] !== null) ? $listaUbicacion[$i]->getId() : null;

				$detalle = new MaterialRegistroSalidas(
					null,
					$material->getId(),
					$idRegistroSalidas,
					$listaCantidad[$i],
					(isset($listaCasa[$i]) && $listaCasa[$i] !== null) ? $listaCasa[$i]->getId() : null,
					(isset($listaManzana[$i]) && $listaManzana[$i] !== null) ? $listaManzana[$i]->getId() : null,
					(isset($listaDestino[$i]) && $listaDestino[$i] !== null) ? $listaDestino[$i]->getId() : null,
					(isset($listaArea[$i]) && $listaArea[$i] !== null) ? $listaArea[$i]->getId() : null,
					(isset($listaRubro[$i]) && $listaRubro[$i] !== null) ? $listaRubro[$i]->getId() : null,
					$ubicacionId,
					$costoPorLinea[$i]
				);
				MaterialRegistroSalidas::save($detalle);

				// UbicacionID solo se registra cuando existe - nunca se inventa un valor.
				$lineaAuditoria = [
					'MaterialID' => $material->getId(),
					'Cantidad' => $listaCantidad[$i],
				];
				if ($ubicacionId !== null) {
					$lineaAuditoria['UbicacionID'] = $ubicacionId;
				}
				$lineasParaAuditoria[] = $lineaAuditoria;
			}

			Auditoria::registrar('RegistroSalidas', $idRegistroSalidas, 'CREAR', $usuarioId, null, [
				'Fecha' => $fecha,
				'Hora' => $hora,
				'ContratistaID' => $contratistaId,
				'ProyectoID' => $proyectoId,
				'Materiales' => count($listaMaterial),
				'Lineas' => $lineasParaAuditoria,
			]);

			$db->commit();

			return ['ok' => true, 'id' => $idRegistroSalidas, 'alertasStockMinimo' => $alertasStockMinimo];

		} catch (PDOException $e) {
			$db->rollBack();
			throw $e;
		}
	}

}

?>