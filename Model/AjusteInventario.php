<?php
/**
* Movimiento de inventario que no corresponde a una Entrada ni a una Salida:
* apertura de trazabilidad, conteo fisico, perdida o daño. Sigue el mismo patron
* transaccional que RegistroEntradas/RegistroSalidas, pero bloquea la fila del
* material (SELECT ... FOR UPDATE) antes de leer su saldo, porque una perdida de
* actualizacion aqui falsearia justo la conciliacion que este mecanismo existe
* para dar.
*/
class AjusteInventario
{
	private $Id;
	private $Fecha;
	private $Hora;
	private $MaterialId;
	private $UsuarioId;
	private $Tipo;
	private $CantidadAnterior;
	private $CantidadNueva;
	private $CantidadAjuste;
	private $CostoUnitario;
	private $Motivo;

	const TIPOS_VALIDOS = ['APERTURA', 'CONTEO', 'PERDIDA', 'DANO'];


	function __construct($Id, $Fecha, $Hora, $MaterialId, $UsuarioId, $Tipo, $CantidadAnterior, $CantidadNueva, $CantidadAjuste, $CostoUnitario, $Motivo)
	{
		$this->setId($Id);
		$this->setFecha($Fecha);
		$this->setHora($Hora);
		$this->setMaterialId($MaterialId);
		$this->setUsuarioId($UsuarioId);
		$this->setTipo($Tipo);
		$this->setCantidadAnterior($CantidadAnterior);
		$this->setCantidadNueva($CantidadNueva);
		$this->setCantidadAjuste($CantidadAjuste);
		$this->setCostoUnitario($CostoUnitario);
		$this->setMotivo($Motivo);
	}

	public function getId(){ return $this->Id; }
	public function setId($Id){ $this->Id = $Id; }

	public function getFecha(){ return $this->Fecha; }
	public function setFecha($Fecha){ $this->Fecha = $Fecha; }

	public function getHora(){ return $this->Hora; }
	public function setHora($Hora){ $this->Hora = $Hora; }

	public function getMaterialId(){ return $this->MaterialId; }
	public function setMaterialId($MaterialId){ $this->MaterialId = $MaterialId; }

	public function getUsuarioId(){ return $this->UsuarioId; }
	public function setUsuarioId($UsuarioId){ $this->UsuarioId = $UsuarioId; }

	public function getTipo(){ return $this->Tipo; }
	public function setTipo($Tipo){ $this->Tipo = $Tipo; }

	public function getCantidadAnterior(){ return $this->CantidadAnterior; }
	public function setCantidadAnterior($CantidadAnterior){ $this->CantidadAnterior = $CantidadAnterior; }

	public function getCantidadNueva(){ return $this->CantidadNueva; }
	public function setCantidadNueva($CantidadNueva){ $this->CantidadNueva = $CantidadNueva; }

	public function getCantidadAjuste(){ return $this->CantidadAjuste; }
	public function setCantidadAjuste($CantidadAjuste){ $this->CantidadAjuste = $CantidadAjuste; }

	public function getCostoUnitario(){ return $this->CostoUnitario; }
	public function setCostoUnitario($CostoUnitario){ $this->CostoUnitario = $CostoUnitario; }

	public function getMotivo(){ return $this->Motivo; }
	public function setMotivo($Motivo){ $this->Motivo = $Motivo; }


	private static function deFila($fila){
		return new AjusteInventario($fila['ID'], $fila['Fecha'], $fila['Hora'], $fila['MaterialID'], $fila['UsuarioID'], $fila['Tipo'], $fila['CantidadAnterior'], $fila['CantidadNueva'], $fila['CantidadAjuste'], $fila['CostoUnitario'], $fila['Motivo']);
	}

	public static function porMaterial($materialId){
		$db = Db::getConnect();
		$lista = [];
		$select = $db->prepare('SELECT * FROM ajuste_inventario WHERE MaterialID=:MaterialID ORDER BY Fecha, Hora, ID');
		$select->bindValue('MaterialID', $materialId);
		$select->execute();

		foreach ($select->fetchAll() as $fila) {
			$lista[] = self::deFila($fila);
		}

		return $lista;
	}

	public static function all(){
		$db = Db::getConnect();
		$lista = [];
		$select = $db->query('SELECT * FROM ajuste_inventario ORDER BY ID DESC');

		foreach ($select->fetchAll() as $fila) {
			$lista[] = self::deFila($fila);
		}

		return $lista;
	}

	// WHERE dinamico compartido por contarTotal() y paginado() - igual que en Auditoria.php,
	// para que el total de paginas siempre corresponda exactamente al mismo filtro mostrado.
	private static function condicionesFiltro($tipo, $materialId, $usuarioId, $fechaInicial, $fechaFinal){
		$where = [];
		$params = [];

		if (!empty($tipo)) {
			$where[] = 'Tipo = :tipo';
			$params['tipo'] = $tipo;
		}
		if (!empty($materialId)) {
			$where[] = 'MaterialID = :materialId';
			$params['materialId'] = $materialId;
		}
		if (!empty($usuarioId)) {
			$where[] = 'UsuarioID = :usuarioId';
			$params['usuarioId'] = $usuarioId;
		}
		if (!empty($fechaInicial)) {
			$where[] = 'Fecha >= :fechaInicial';
			$params['fechaInicial'] = $fechaInicial;
		}
		if (!empty($fechaFinal)) {
			$where[] = 'Fecha <= :fechaFinal';
			$params['fechaFinal'] = $fechaFinal;
		}

		return [$where, $params];
	}

	// Total de ajustes que cumplen el filtro (para calcular cuantas paginas hay). No existia
	// antes de esta fase - AjusteInventario solo tenia all() (Fase 3 seccion 12).
	public static function contarTotal($tipo = null, $materialId = null, $usuarioId = null, $fechaInicial = null, $fechaFinal = null){
		$db = Db::getConnect();

		list($where, $params) = self::condicionesFiltro($tipo, $materialId, $usuarioId, $fechaInicial, $fechaFinal);

		$sql = 'SELECT COUNT(*) FROM ajuste_inventario';
		if (!empty($where)) {
			$sql .= ' WHERE ' . implode(' AND ', $where);
		}

		$select = $db->prepare($sql);
		foreach ($params as $nombre => $valor) {
			$select->bindValue($nombre, $valor);
		}
		$select->execute();

		return (int) $select->fetchColumn();
	}

	// Una pagina de ajustes que cumple el filtro, mas reciente primero. Mismo patron exacto de
	// Material::paginado()/Auditoria::paginado().
	public static function paginado($pagina, $tamanoPagina, $tipo = null, $materialId = null, $usuarioId = null, $fechaInicial = null, $fechaFinal = null){
		$db = Db::getConnect();
		$lista = [];

		$pagina = max(1, (int) $pagina);
		$offset = ($pagina - 1) * $tamanoPagina;

		list($where, $params) = self::condicionesFiltro($tipo, $materialId, $usuarioId, $fechaInicial, $fechaFinal);

		$sql = 'SELECT * FROM ajuste_inventario';
		if (!empty($where)) {
			$sql .= ' WHERE ' . implode(' AND ', $where);
		}
		$sql .= ' ORDER BY ID DESC LIMIT :offset, :tamano';

		$select = $db->prepare($sql);
		foreach ($params as $nombre => $valor) {
			$select->bindValue($nombre, $valor);
		}
		$select->bindValue('offset', $offset, PDO::PARAM_INT);
		$select->bindValue('tamano', (int) $tamanoPagina, PDO::PARAM_INT);
		$select->execute();

		foreach ($select->fetchAll() as $fila) {
			$lista[] = self::deFila($fila);
		}

		return $lista;
	}

	public static function existeApertura($materialId){
		$db = Db::getConnect();
		$select = $db->prepare("SELECT COUNT(*) FROM ajuste_inventario WHERE MaterialID=:MaterialID AND Tipo='APERTURA'");
		$select->bindValue('MaterialID', $materialId);
		$select->execute();

		return $select->fetchColumn() > 0;
	}

	private static function guardar($ajuste){
		$db = Db::getConnect();
		$insert = $db->prepare('INSERT INTO ajuste_inventario (ID, Fecha, Hora, MaterialID, UsuarioID, Tipo, CantidadAnterior, CantidadNueva, CantidadAjuste, CostoUnitario, Motivo) VALUES (null, :Fecha, :Hora, :MaterialID, :UsuarioID, :Tipo, :CantidadAnterior, :CantidadNueva, :CantidadAjuste, :CostoUnitario, :Motivo)');
		$insert->bindValue('Fecha', $ajuste->getFecha());
		$insert->bindValue('Hora', $ajuste->getHora());
		$insert->bindValue('MaterialID', $ajuste->getMaterialId());
		$insert->bindValue('UsuarioID', $ajuste->getUsuarioId());
		$insert->bindValue('Tipo', $ajuste->getTipo());
		$insert->bindValue('CantidadAnterior', $ajuste->getCantidadAnterior());
		$insert->bindValue('CantidadNueva', $ajuste->getCantidadNueva());
		$insert->bindValue('CantidadAjuste', $ajuste->getCantidadAjuste());
		$insert->bindValue('CostoUnitario', $ajuste->getCostoUnitario(), $ajuste->getCostoUnitario() === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
		$insert->bindValue('Motivo', $ajuste->getMotivo());
		$insert->execute();

		return $insert;
	}

	// Excepcion de negocio (no de infraestructura) para que el Controller pueda mostrar
	// un mensaje claro en vez de dejar burbujear una PDOException cruda.
	public static function registrar($materialId, $usuarioId, $tipo, $valorIngresado, $costoUnitario, $motivo){
		if (!in_array($tipo, self::TIPOS_VALIDOS)) {
			throw new Exception('Tipo de ajuste no valido.');
		}

		$db = Db::getConnect();

		try {
			$db->beginTransaction();

			// Bloquea la fila del material antes de leer su saldo: evita que una Entrada,
			// una Salida o un segundo Ajuste concurrente pisen esta lectura antes del commit.
			$select = $db->prepare('SELECT * FROM material WHERE ID=:ID FOR UPDATE');
			$select->bindValue('ID', $materialId);
			$select->execute();
			$filaMaterial = $select->fetch();

			if ($filaMaterial === false) {
				throw new Exception('El material no existe.');
			}

			$saldoAnterior = (int) $filaMaterial['Saldo'];
			$costoPromedioAnterior = $filaMaterial['CostoPromedio'];

			if ($tipo === 'APERTURA') {
				if (self::existeApertura($materialId)) {
					throw new Exception('Este material ya tiene una apertura registrada.');
				}
				// La apertura nunca cambia el saldo: solo documenta el que ya existe.
				$cantidadNueva = $saldoAnterior;
				$cantidadAjuste = 0;
				$costoParaGuardar = ($costoUnitario !== null && $costoUnitario !== '') ? $costoUnitario : null;
			} elseif ($tipo === 'CONTEO') {
				// $valorIngresado es el conteo fisico encontrado (cantidad absoluta).
				$cantidadNueva = (int) $valorIngresado;
				$cantidadAjuste = $cantidadNueva - $saldoAnterior;
				$costoParaGuardar = null;
			} else { // PERDIDA o DANO
				// $valorIngresado es la cantidad perdida/dañada (siempre positiva).
				$cantidadPerdida = (int) $valorIngresado;
				$cantidadNueva = $saldoAnterior - $cantidadPerdida;
				$cantidadAjuste = -$cantidadPerdida;
				$costoParaGuardar = null;
			}

			if ($cantidadNueva < 0) {
				throw new Exception('El ajuste dejaria el saldo en negativo.');
			}

			// Snapshot informativo del costo vigente cuando el ajuste QUITA cantidad
			// (mismo principio que una Salida: nunca se toca CostoPromedio al quitar).
			if ($cantidadAjuste < 0 && $costoPromedioAnterior !== null) {
				$costoParaGuardar = $costoPromedioAnterior;
			}

			// Se llama siempre, incluso cuando la cantidad no cambia (APERTURA): garantiza
			// que material_almacen quede sincronizado tambien para un material que nunca
			// antes tuvo un movimiento que disparara Almacen::sincronizarSaldoPrincipal().
			Material::ingresoMaterial($materialId, $cantidadNueva);

			// Solo APERTURA puede sembrar un CostoPromedio que hoy es NULL. Nunca se
			// pondera contra un promedio previo (por definicion, es el primer costo
			// conocido de este material) y nunca se aplica a CONTEO/PERDIDA/DANO.
			if ($tipo === 'APERTURA' && $costoUnitario !== null && $costoUnitario !== '') {
				Material::actualizarCosto($materialId, $costoUnitario);
			}

			$ajuste = new AjusteInventario(null, date('Y-m-d'), date('H:i:s'), $materialId, $usuarioId, $tipo, $saldoAnterior, $cantidadNueva, $cantidadAjuste, $costoParaGuardar, $motivo);
			self::guardar($ajuste);
			$idAjuste = $db->lastInsertId();

			Auditoria::registrar('AjusteInventario', $idAjuste, 'CREAR', $usuarioId, null, [
				'MaterialID' => $materialId,
				'Tipo' => $tipo,
				'CantidadAnterior' => $saldoAnterior,
				'CantidadNueva' => $cantidadNueva,
				'CantidadAjuste' => $cantidadAjuste,
				'Motivo' => $motivo,
				'CostoUnitario' => $costoParaGuardar,
			]);

			$db->commit();

			return $idAjuste;

		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}
	}
}

?>
