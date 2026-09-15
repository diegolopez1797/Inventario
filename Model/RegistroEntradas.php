<?php 
/**
* 
*/


class RegistroEntradas
{
	private $Id;
	private $Fecha;
	private $Hora;
	private $Usuario;
	private $ProveedorId;


	function __construct($Id, $Fecha, $Hora, $Usuario, $ProveedorId = null)
	{
		$this->setId($Id);
		$this->setFecha($Fecha);
		$this->setHora($Hora);
		$this->setUsuario($Usuario);
		$this->setProveedorId($ProveedorId);
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

	public function getProveedorId(){
		return $this->ProveedorId;
	}

	public function setProveedorId($ProveedorId){
		$this->ProveedorId = $ProveedorId;
	}



	public static function save($registroEntradas1){
		$db = Db::getConnect();

		$insert=$db->prepare('INSERT INTO registro_entradas VALUES (null,:fechaActual,:hora,:usuario,:proveedor)');
		$insert->bindValue('fechaActual',$registroEntradas1->getFecha());
		$insert->bindValue('hora',$registroEntradas1->getHora());
		$insert->bindValue('usuario',$registroEntradas1->getUsuario());
		$insert->bindValue('proveedor',$registroEntradas1->getProveedorId(), $registroEntradas1->getProveedorId() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$insert->execute();

		return $insert;
	}

	public static function searchEntrada($Id){
		$db = Db::getConnect();

		$select = $db->prepare('SELECT * FROM registro_entradas WHERE ID=:ID');
		$select->bindValue('ID',$Id);
		$select->execute();

		$entrada = $select->fetch();

		$registroEntradas = new RegistroEntradas($entrada['ID'],$entrada['Fecha'],$entrada['Hora'],$entrada['UsuarioID'],$entrada['ProveedorID']);

		return $registroEntradas;

	}

	//--------------------------------------------------------------------------------------------------------
	// Registra una entrada completa (cabecera + detalle de materiales) en una unica transaccion:
	// si alguna escritura falla, se revierte todo y no queda saldo de material actualizado a medias.
	public static function registrar($fecha, $hora, $usuarioId, $listaMaterial, $listaCantidad, $listaDestino, $listaCosto = [], $proveedorId = null){
		$db = Db::getConnect();

		try {
			$db->beginTransaction();

			foreach ($listaMaterial as $i => $material) {
				$id = $material->getId();
				$saldoMaterial = Material::searchById($id);
				$saldoAnterior = $saldoMaterial->getSaldo();
				$nuevoSaldo = $saldoAnterior + $listaCantidad[$i];
				Material::ingresoMaterial($id, $nuevoSaldo);

				// Costo promedio ponderado (CPP): solo se calcula si esta entrada trae un costo.
				// Si el material nunca tuvo costo conocido, el promedio nuevo es directamente el
				// costo de esta compra (no hay base valida contra la cual ponderar el saldo viejo).
				$costoEntrada = isset($listaCosto[$i]) ? $listaCosto[$i] : null;
				if ($costoEntrada !== null && $costoEntrada !== '') {
					$promedioAnterior = $saldoMaterial->getCostoPromedio();
					if ($promedioAnterior === null) {
						$nuevoPromedio = $costoEntrada;
					} else {
						$nuevoPromedio = (($saldoAnterior * $promedioAnterior) + ($listaCantidad[$i] * $costoEntrada)) / $nuevoSaldo;
					}
					Material::actualizarCosto($id, $nuevoPromedio);
				}
			}

			$registroEntradas = new RegistroEntradas(null, $fecha, $hora, $usuarioId, $proveedorId);
			self::save($registroEntradas);
			$idRegistroEntradas = $db->lastInsertId();

			foreach ($listaMaterial as $i => $material) {
				$destino = $listaDestino[$i]->getId();
				$costoEntrada = isset($listaCosto[$i]) && $listaCosto[$i] !== '' ? $listaCosto[$i] : null;

				$detalle = new MaterialRegistroEntradas(null, $material->getId(), $idRegistroEntradas, $listaCantidad[$i], $destino, $costoEntrada);
				MaterialRegistroEntradas::save($detalle);
			}

			Auditoria::registrar('RegistroEntradas', $idRegistroEntradas, 'CREAR', $usuarioId, null, [
				'Fecha' => $fecha,
				'Hora' => $hora,
				'Materiales' => count($listaMaterial),
			]);

			$db->commit();

			return $idRegistroEntradas;

		} catch (PDOException $e) {
			$db->rollBack();
			throw $e;
		}
	}

}

?>
