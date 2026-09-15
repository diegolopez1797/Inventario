<?php
/**
* Solicitud de material: la crea quien la necesita (Residente de Obra u otro rol con
* solicitud.crear), la aprueba el responsable del proyecto (o alguien con el permiso
* excepcional solicitud.aprobar.excepcional) y la entrega el almacen reutilizando
* RegistroSalidas::registrar() para no duplicar la logica de saldo/CPP/auditoria.
*/
class Solicitud
{
	private $Id;
	private $Fecha;
	private $Hora;
	private $UsuarioSolicitaId;
	private $ProyectoId;
	private $UbicacionId;
	private $Estado;
	private $UsuarioApruebaId;
	private $FechaAprobacion;
	private $RegistroSalidasId;


	function __construct($Id, $Fecha, $Hora, $UsuarioSolicitaId, $ProyectoId, $UbicacionId, $Estado, $UsuarioApruebaId, $FechaAprobacion, $RegistroSalidasId)
	{
		$this->setId($Id);
		$this->setFecha($Fecha);
		$this->setHora($Hora);
		$this->setUsuarioSolicitaId($UsuarioSolicitaId);
		$this->setProyectoId($ProyectoId);
		$this->setUbicacionId($UbicacionId);
		$this->setEstado($Estado);
		$this->setUsuarioApruebaId($UsuarioApruebaId);
		$this->setFechaAprobacion($FechaAprobacion);
		$this->setRegistroSalidasId($RegistroSalidasId);
	}

	public function getId(){ return $this->Id; }
	public function setId($Id){ $this->Id = $Id; }

	public function getFecha(){ return $this->Fecha; }
	public function setFecha($Fecha){ $this->Fecha = $Fecha; }

	public function getHora(){ return $this->Hora; }
	public function setHora($Hora){ $this->Hora = $Hora; }

	public function getUsuarioSolicitaId(){ return $this->UsuarioSolicitaId; }
	public function setUsuarioSolicitaId($UsuarioSolicitaId){ $this->UsuarioSolicitaId = $UsuarioSolicitaId; }

	public function getProyectoId(){ return $this->ProyectoId; }
	public function setProyectoId($ProyectoId){ $this->ProyectoId = $ProyectoId; }

	public function getUbicacionId(){ return $this->UbicacionId; }
	public function setUbicacionId($UbicacionId){ $this->UbicacionId = $UbicacionId; }

	public function getEstado(){ return $this->Estado; }
	public function setEstado($Estado){ $this->Estado = $Estado; }

	public function getUsuarioApruebaId(){ return $this->UsuarioApruebaId; }
	public function setUsuarioApruebaId($UsuarioApruebaId){ $this->UsuarioApruebaId = $UsuarioApruebaId; }

	public function getFechaAprobacion(){ return $this->FechaAprobacion; }
	public function setFechaAprobacion($FechaAprobacion){ $this->FechaAprobacion = $FechaAprobacion; }

	public function getRegistroSalidasId(){ return $this->RegistroSalidasId; }
	public function setRegistroSalidasId($RegistroSalidasId){ $this->RegistroSalidasId = $RegistroSalidasId; }


	private static function delFila($fila){
		return new Solicitud($fila['ID'], $fila['Fecha'], $fila['Hora'], $fila['UsuarioSolicitaID'], $fila['ProyectoID'], $fila['UbicacionID'], $fila['Estado'], $fila['UsuarioApruebaID'], $fila['FechaAprobacion'], $fila['RegistroSalidasID']);
	}

	public static function searchById($id){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM solicitud WHERE ID=:ID');
		$select->bindValue('ID', $id);
		$select->execute();

		return self::delFila($select->fetch());
	}

	public static function all(){
		$db = Db::getConnect();
		$lista = [];
		$select = $db->query('SELECT * FROM solicitud ORDER BY ID DESC');

		foreach ($select->fetchAll() as $fila) {
			$lista[] = self::delFila($fila);
		}

		return $lista;
	}

	// Solicitudes creadas por un usuario especifico (para su propia bandeja "Mis solicitudes").
	public static function porUsuarioSolicita($usuarioId){
		$db = Db::getConnect();
		$lista = [];
		$select = $db->prepare('SELECT * FROM solicitud WHERE UsuarioSolicitaID=:UsuarioID ORDER BY ID DESC');
		$select->bindValue('UsuarioID', $usuarioId);
		$select->execute();

		foreach ($select->fetchAll() as $fila) {
			$lista[] = self::delFila($fila);
		}

		return $lista;
	}

	// Solicitudes PENDIENTE de los proyectos donde el usuario es responsable (su bandeja de aprobacion).
	public static function pendientesParaResponsable($usuarioId){
		$db = Db::getConnect();
		$lista = [];
		$select = $db->prepare("SELECT s.* FROM solicitud s INNER JOIN proyecto p ON s.ProyectoID = p.ID WHERE s.Estado = 'PENDIENTE' AND p.ResponsableID = :UsuarioID ORDER BY s.ID");
		$select->bindValue('UsuarioID', $usuarioId);
		$select->execute();

		foreach ($select->fetchAll() as $fila) {
			$lista[] = self::delFila($fila);
		}

		return $lista;
	}

	public static function pendientes(){
		$db = Db::getConnect();
		$lista = [];
		$select = $db->query("SELECT * FROM solicitud WHERE Estado = 'PENDIENTE' ORDER BY ID");

		foreach ($select->fetchAll() as $fila) {
			$lista[] = self::delFila($fila);
		}

		return $lista;
	}

	public static function aprobadas(){
		$db = Db::getConnect();
		$lista = [];
		$select = $db->query("SELECT * FROM solicitud WHERE Estado = 'APROBADA' ORDER BY ID");

		foreach ($select->fetchAll() as $fila) {
			$lista[] = self::delFila($fila);
		}

		return $lista;
	}

	// WHERE dinamico compartido por contarTotal()/paginado()/contarPorEstado() - mismo patron
	// de Auditoria.php/AjusteInventario.php. $incluirEstado en false se usa desde
	// contarPorEstado(), que agrupa POR Estado y por lo tanto nunca debe filtrar por el mismo.
	private static function condicionesFiltro($estado, $proyectoId, $usuarioSolicitaId, $fechaInicial, $fechaFinal, $incluirEstado = true){
		$where = [];
		$params = [];

		if ($incluirEstado && !empty($estado)) {
			$where[] = 'Estado = :estado';
			$params['estado'] = $estado;
		}
		if (!empty($proyectoId)) {
			$where[] = 'ProyectoID = :proyectoId';
			$params['proyectoId'] = $proyectoId;
		}
		if (!empty($usuarioSolicitaId)) {
			$where[] = 'UsuarioSolicitaID = :usuarioSolicitaId';
			$params['usuarioSolicitaId'] = $usuarioSolicitaId;
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

	// Total de solicitudes que cumplen el filtro (Fase 3 seccion 13: no existia antes, los
	// metodos previos - porUsuarioSolicita/pendientes/aprobadas - son consultas fijas de un
	// solo filtro cada una, utiles para la bandeja pero no para un informe con filtros combinables).
	public static function contarTotal($estado = null, $proyectoId = null, $usuarioSolicitaId = null, $fechaInicial = null, $fechaFinal = null){
		$db = Db::getConnect();

		list($where, $params) = self::condicionesFiltro($estado, $proyectoId, $usuarioSolicitaId, $fechaInicial, $fechaFinal);

		$sql = 'SELECT COUNT(*) FROM solicitud';
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

	// Una pagina de solicitudes que cumple el filtro, mas reciente primero.
	public static function paginado($pagina, $tamanoPagina, $estado = null, $proyectoId = null, $usuarioSolicitaId = null, $fechaInicial = null, $fechaFinal = null){
		$db = Db::getConnect();
		$lista = [];

		$pagina = max(1, (int) $pagina);
		$offset = ($pagina - 1) * $tamanoPagina;

		list($where, $params) = self::condicionesFiltro($estado, $proyectoId, $usuarioSolicitaId, $fechaInicial, $fechaFinal);

		$sql = 'SELECT * FROM solicitud';
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
			$lista[] = self::delFila($fila);
		}

		return $lista;
	}

	// Conteo por Estado (Fase 2.1: "Totales: conteo por estado"), respetando los mismos filtros
	// de Proyecto/Solicitante/fecha que el listado, pero nunca el propio Estado.
	public static function contarPorEstado($proyectoId = null, $usuarioSolicitaId = null, $fechaInicial = null, $fechaFinal = null){
		$db = Db::getConnect();

		list($where, $params) = self::condicionesFiltro(null, $proyectoId, $usuarioSolicitaId, $fechaInicial, $fechaFinal, false);

		$sql = 'SELECT Estado, COUNT(*) AS Total FROM solicitud';
		if (!empty($where)) {
			$sql .= ' WHERE ' . implode(' AND ', $where);
		}
		$sql .= ' GROUP BY Estado';

		$select = $db->prepare($sql);
		foreach ($params as $nombre => $valor) {
			$select->bindValue($nombre, $valor);
		}
		$select->execute();

		$resultado = [];
		foreach ($select->fetchAll(PDO::FETCH_ASSOC) as $fila) {
			$resultado[$fila['Estado']] = (int) $fila['Total'];
		}

		return $resultado;
	}

	// Detalle de materiales de una solicitud: cada fila trae el objeto Material completo
	// (para poder mostrar descripcion/unidad/saldo) junto con la cantidad solicitada.
	public static function detalle($solicitudId){
		$db = Db::getConnect();
		$lista = [];
		$select = $db->prepare('SELECT * FROM solicitud_detalle WHERE SolicitudID=:SolicitudID ORDER BY ID');
		$select->bindValue('SolicitudID', $solicitudId);
		$select->execute();

		foreach ($select->fetchAll() as $fila) {
			$lista[] = [
				'material' => Material::searchById($fila['MaterialID']),
				'cantidad' => $fila['CantidadSolicitada'],
			];
		}

		return $lista;
	}

	// Crea la solicitud (cabecera + detalle) en una unica transaccion.
	// $listaMaterialId y $listaCantidad van indexados en paralelo (misma posicion = misma linea).
	public static function crear($usuarioSolicitaId, $proyectoId, $ubicacionId, $listaMaterialId, $listaCantidad){
		$db = Db::getConnect();

		// La ubicacion, si se indico, debe pertenecer al proyecto de la solicitud - se revalida
		// aqui (unico punto de escritura) sin confiar en lo que haya filtrado el formulario.
		if ($ubicacionId !== null && $ubicacionId !== '') {
			$ubicacion = Ubicacion::searchById($ubicacionId);
			if ((int) $ubicacion->getProyectoId() !== (int) $proyectoId) {
				throw new Exception('La ubicación seleccionada no pertenece al proyecto de esta solicitud.');
			}
		}

		try {
			$db->beginTransaction();

			$insert = $db->prepare('INSERT INTO solicitud (ID, Fecha, Hora, UsuarioSolicitaID, ProyectoID, UbicacionID, Estado, UsuarioApruebaID, FechaAprobacion, RegistroSalidasID) VALUES (null, :fecha, :hora, :usuarioSolicitaId, :proyectoId, :ubicacionId, :estado, null, null, null)');
			$insert->bindValue('fecha', date('Y-m-d'));
			$insert->bindValue('hora', date('H:i:s'));
			$insert->bindValue('usuarioSolicitaId', $usuarioSolicitaId);
			$insert->bindValue('proyectoId', $proyectoId);
			$insert->bindValue('ubicacionId', $ubicacionId, $ubicacionId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
			$insert->bindValue('estado', 'PENDIENTE');
			$insert->execute();

			$solicitudId = $db->lastInsertId();

			$insertDetalle = $db->prepare('INSERT INTO solicitud_detalle (ID, SolicitudID, MaterialID, CantidadSolicitada) VALUES (null, :solicitudId, :materialId, :cantidad)');
			foreach ($listaMaterialId as $i => $materialId) {
				$insertDetalle->bindValue('solicitudId', $solicitudId);
				$insertDetalle->bindValue('materialId', $materialId);
				$insertDetalle->bindValue('cantidad', $listaCantidad[$i]);
				$insertDetalle->execute();
			}

			Auditoria::registrar('Solicitud', $solicitudId, 'CREAR', $usuarioSolicitaId, null, [
				'ProyectoID' => $proyectoId,
				'UbicacionID' => $ubicacionId,
				'Materiales' => count($listaMaterialId),
			]);

			$db->commit();

			return $solicitudId;
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}
	}

	// Unico punto de decision sobre quien puede aprobar una solicitud: el responsable del
	// proyecto, o cualquier usuario con el permiso excepcional. No compara RolID ni nombres
	// de rol directamente, para dejar espacio a futuros niveles de aprobacion sin tocar esto.
	public static function usuarioPuedeAprobar($solicitud, $usuario){
		$proyecto = Proyecto::searchById($solicitud->getProyectoId());

		if ($proyecto->getResponsableId() !== null && $proyecto->getResponsableId() == $usuario->getId()) {
			return true;
		}

		return Permiso::usuarioPuede('solicitud.aprobar.excepcional', $usuario);
	}

	public static function aprobar($solicitudId, $usuarioApruebaId){
		$db = Db::getConnect();

		$update = $db->prepare("UPDATE solicitud SET Estado = 'APROBADA', UsuarioApruebaID = :usuarioApruebaId, FechaAprobacion = :fechaAprobacion WHERE ID = :ID AND Estado = 'PENDIENTE'");
		$update->bindValue('usuarioApruebaId', $usuarioApruebaId);
		$update->bindValue('fechaAprobacion', date('Y-m-d H:i:s'));
		$update->bindValue('ID', $solicitudId);
		$update->execute();

		Auditoria::registrar('Solicitud', $solicitudId, 'APROBAR', $usuarioApruebaId, ['Estado' => 'PENDIENTE'], ['Estado' => 'APROBADA']);
	}

	public static function rechazar($solicitudId, $usuarioApruebaId){
		$db = Db::getConnect();

		$update = $db->prepare("UPDATE solicitud SET Estado = 'RECHAZADA', UsuarioApruebaID = :usuarioApruebaId, FechaAprobacion = :fechaAprobacion WHERE ID = :ID AND Estado = 'PENDIENTE'");
		$update->bindValue('usuarioApruebaId', $usuarioApruebaId);
		$update->bindValue('fechaAprobacion', date('Y-m-d H:i:s'));
		$update->bindValue('ID', $solicitudId);
		$update->execute();

		Auditoria::registrar('Solicitud', $solicitudId, 'RECHAZAR', $usuarioApruebaId, ['Estado' => 'PENDIENTE'], ['Estado' => 'RECHAZADA']);
	}

	// Marca la solicitud como ENTREGADA y la enlaza con el registro de salida que la satisfizo.
	// La escritura de saldo/CPP/detalle de la salida la hace RegistroSalidas::registrar(), esta
	// funcion solo cierra el ciclo de vida de la solicitud una vez que esa salida ya existe.
	public static function marcarEntregada($solicitudId, $registroSalidasId, $usuarioId){
		$db = Db::getConnect();

		$update = $db->prepare("UPDATE solicitud SET Estado = 'ENTREGADA', RegistroSalidasID = :registroSalidasId WHERE ID = :ID AND Estado = 'APROBADA'");
		$update->bindValue('registroSalidasId', $registroSalidasId);
		$update->bindValue('ID', $solicitudId);
		$update->execute();

		Auditoria::registrar('Solicitud', $solicitudId, 'ENTREGAR', $usuarioId, ['Estado' => 'APROBADA'], ['Estado' => 'ENTREGADA', 'RegistroSalidasID' => $registroSalidasId]);
	}
}

?>
