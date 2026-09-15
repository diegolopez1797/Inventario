<?php
/**
*
*/
class Auditoria
{
	private $Id;
	private $Entidad;
	private $EntidadId;
	private $Accion;
	private $UsuarioId;
	private $Fecha;
	private $DatosAntes;
	private $DatosDespues;


	function __construct($Id, $Entidad, $EntidadId, $Accion, $UsuarioId, $Fecha, $DatosAntes, $DatosDespues)
	{
		$this->setId($Id);
		$this->setEntidad($Entidad);
		$this->setEntidadId($EntidadId);
		$this->setAccion($Accion);
		$this->setUsuarioId($UsuarioId);
		$this->setFecha($Fecha);
		$this->setDatosAntes($DatosAntes);
		$this->setDatosDespues($DatosDespues);
	}

	public function getId(){
		return $this->Id;
	}

	public function setId($Id){
		$this->Id = $Id;
	}

	public function getEntidad(){
		return $this->Entidad;
	}

	public function setEntidad($Entidad){
		$this->Entidad = $Entidad;
	}

	public function getEntidadId(){
		return $this->EntidadId;
	}

	public function setEntidadId($EntidadId){
		$this->EntidadId = $EntidadId;
	}

	public function getAccion(){
		return $this->Accion;
	}

	public function setAccion($Accion){
		$this->Accion = $Accion;
	}

	public function getUsuarioId(){
		return $this->UsuarioId;
	}

	public function setUsuarioId($UsuarioId){
		$this->UsuarioId = $UsuarioId;
	}

	public function getFecha(){
		return $this->Fecha;
	}

	public function setFecha($Fecha){
		$this->Fecha = $Fecha;
	}

	public function getDatosAntes(){
		return $this->DatosAntes;
	}

	public function setDatosAntes($DatosAntes){
		$this->DatosAntes = $DatosAntes;
	}

	public function getDatosDespues(){
		return $this->DatosDespues;
	}

	public function setDatosDespues($DatosDespues){
		$this->DatosDespues = $DatosDespues;
	}

	// Registra un evento de auditoria. $datosAntes/$datosDespues son arreglos asociativos (o null)
	// que se guardan como JSON. null significa que no aplica (CREAR no tiene "antes", ELIMINAR no tiene "despues").
	public static function registrar($entidad, $entidadId, $accion, $usuarioId, $datosAntes, $datosDespues){
		$db = Db::getConnect();

		$insert = $db->prepare('INSERT INTO auditoria (ID, Entidad, EntidadID, Accion, UsuarioID, Fecha, DatosAntes, DatosDespues) VALUES (null, :entidad, :entidadId, :accion, :usuarioId, :fecha, :datosAntes, :datosDespues)');
		$insert->bindValue('entidad', $entidad);
		$insert->bindValue('entidadId', $entidadId);
		$insert->bindValue('accion', $accion);
		$insert->bindValue('usuarioId', $usuarioId);
		$insert->bindValue('fecha', date('Y-m-d H:i:s'));
		$insert->bindValue('datosAntes', $datosAntes !== null ? json_encode($datosAntes, JSON_UNESCAPED_UNICODE) : null);
		$insert->bindValue('datosDespues', $datosDespues !== null ? json_encode($datosDespues, JSON_UNESCAPED_UNICODE) : null);
		$insert->execute();
	}

	public static function all(){
		$db = Db::getConnect();
		$lista = [];
		$select = $db->query('SELECT * FROM auditoria ORDER BY ID DESC');

		foreach ($select->fetchAll() as $row) {
			$lista[] = new Auditoria($row['ID'], $row['Entidad'], $row['EntidadID'], $row['Accion'], $row['UsuarioID'], $row['Fecha'], $row['DatosAntes'], $row['DatosDespues']);
		}

		return $lista;
	}

	// Arma el WHERE dinamico compartido por contarTotal() y paginado(), para que nunca puedan
	// quedar desincronizados (el total de paginas siempre corresponde al mismo filtro que
	// se esta mostrando).
	private static function condicionesFiltro($usuarioId, $entidad, $accion, $fechaInicial, $fechaFinal){
		$where = [];
		$params = [];

		if (!empty($usuarioId)) {
			$where[] = 'UsuarioID = :usuarioId';
			$params['usuarioId'] = $usuarioId;
		}
		if (!empty($entidad)) {
			$where[] = 'Entidad = :entidad';
			$params['entidad'] = $entidad;
		}
		if (!empty($accion)) {
			$where[] = 'Accion = :accion';
			$params['accion'] = $accion;
		}
		if (!empty($fechaInicial)) {
			$where[] = 'Fecha >= :fechaInicial';
			$params['fechaInicial'] = $fechaInicial . ' 00:00:00';
		}
		if (!empty($fechaFinal)) {
			// Fecha es DATETIME completo; sin la hora, ':fechaFinal' equivaldria a medianoche y
			// excluiria todo lo ocurrido despues de las 00:00:00 de ese mismo dia.
			$where[] = 'Fecha <= :fechaFinal';
			$params['fechaFinal'] = $fechaFinal . ' 23:59:59';
		}

		return [$where, $params];
	}

	// Valores reales de Entidad ya registrados (para el filtro) - nunca una lista inventada,
	// solo lo que el sistema efectivamente ha auditado hasta hoy.
	public static function entidadesRegistradas(){
		$db = Db::getConnect();
		return $db->query('SELECT DISTINCT Entidad FROM auditoria ORDER BY Entidad')->fetchAll(PDO::FETCH_COLUMN);
	}

	// Idem para Accion.
	public static function accionesRegistradas(){
		$db = Db::getConnect();
		return $db->query('SELECT DISTINCT Accion FROM auditoria ORDER BY Accion')->fetchAll(PDO::FETCH_COLUMN);
	}

	// Total de eventos que cumplen el filtro (para calcular cuantas paginas hay).
	public static function contarTotal($usuarioId = null, $entidad = null, $accion = null, $fechaInicial = null, $fechaFinal = null){
		$db = Db::getConnect();

		list($where, $params) = self::condicionesFiltro($usuarioId, $entidad, $accion, $fechaInicial, $fechaFinal);

		$sql = 'SELECT COUNT(*) FROM auditoria';
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

	// Una pagina del historial de auditoria que cumple el filtro, mas reciente primero. Esta
	// tabla no tiene techo (crece con cada accion administrativa del sistema), asi que a
	// diferencia de otros catalogos aqui la paginacion no es una mejora futura sino una
	// condicion minima de uso.
	public static function paginado($pagina, $tamanoPagina, $usuarioId = null, $entidad = null, $accion = null, $fechaInicial = null, $fechaFinal = null){
		$db = Db::getConnect();
		$lista = [];

		$pagina = max(1, (int) $pagina);
		$offset = ($pagina - 1) * $tamanoPagina;

		list($where, $params) = self::condicionesFiltro($usuarioId, $entidad, $accion, $fechaInicial, $fechaFinal);

		$sql = 'SELECT * FROM auditoria';
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

		foreach ($select->fetchAll() as $row) {
			$lista[] = new Auditoria($row['ID'], $row['Entidad'], $row['EntidadID'], $row['Accion'], $row['UsuarioID'], $row['Fecha'], $row['DatosAntes'], $row['DatosDespues']);
		}

		return $lista;
	}
}

?>
