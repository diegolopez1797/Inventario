<?php
/**
* Destinatarios administrables de la alerta de stock minimo (Fase de implementacion aprobada:
* Analisis y Propuesta - Gestion de Destinatarios de Notificaciones por Correo). Tabla
* independiente, sin relacion con `usuario` - un destinatario no necesita cuenta de acceso a
* Berdez, solo recibir un correo.
*/
class NotificacionDestinatario
{
	private $Id;
	private $Nombre;
	private $Correo;
	private $Activo;

	function __construct($Id, $Nombre, $Correo, $Activo)
	{
		$this->setId($Id);
		$this->setNombre($Nombre);
		$this->setCorreo($Correo);
		$this->setActivo($Activo);
	}

	public function getId(){ return $this->Id; }
	public function setId($Id){ $this->Id = $Id; }
	public function getNombre(){ return $this->Nombre; }
	public function setNombre($Nombre){ $this->Nombre = $Nombre; }
	public function getCorreo(){ return $this->Correo; }
	public function setCorreo($Correo){ $this->Correo = $Correo; }
	public function getActivo(){ return $this->Activo; }
	public function setActivo($Activo){ $this->Activo = $Activo; }

	private static function deFila($fila){
		return new NotificacionDestinatario($fila['ID'], $fila['Nombre'], $fila['Correo'], $fila['Activo']);
	}

	// Listado completo para la pantalla de administracion (activos e inactivos).
	public static function all(){
		$db = Db::getConnect();
		$lista = [];
		$select = $db->query('SELECT * FROM notificacion_destinatario ORDER BY Nombre');
		foreach ($select->fetchAll() as $fila) { $lista[] = self::deFila($fila); }
		return $lista;
	}

	public static function searchById($id){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM notificacion_destinatario WHERE ID=:id');
		$select->bindValue('id', $id);
		$select->execute();
		return self::deFila($select->fetch());
	}

	// Unicos que reciben correo - usado por RegistroSalidasController::notificarAlertasStockMinimo().
	public static function activos(){
		$db = Db::getConnect();
		$lista = [];
		$select = $db->query('SELECT * FROM notificacion_destinatario WHERE Activo = 1 ORDER BY Nombre');
		foreach ($select->fetchAll() as $fila) { $lista[] = self::deFila($fila); }
		return $lista;
	}

	// Pre-chequeo de duplicados ANTES de insertar/actualizar (mismo criterio que ya usa
	// Ubicacion antes de un reparentado: validar en el Model antes de depender solo del error
	// crudo de la restriccion UNIQUE). $excluirId se usa al editar, para no marcar como
	// duplicado el propio registro que se esta guardando.
	public static function correoExiste($correo, $excluirId = null){
		$db = Db::getConnect();
		$sql = 'SELECT COUNT(*) FROM notificacion_destinatario WHERE Correo = :correo';
		$params = ['correo' => $correo];
		if ($excluirId !== null) {
			$sql .= ' AND ID != :excluirId';
			$params['excluirId'] = $excluirId;
		}
		$select = $db->prepare($sql);
		foreach ($params as $k => $v) { $select->bindValue($k, $v); }
		$select->execute();
		return $select->fetchColumn() > 0;
	}

	// Mismo patron que Destino::save()/Ubicacion::save(): recibe el objeto ya validado por el
	// Controller, inserta, devuelve el PDOStatement (el Controller solo verifica isset()).
	public static function save($destinatario){
		$db = Db::getConnect();
		$insert = $db->prepare('INSERT INTO notificacion_destinatario (ID, Nombre, Correo, Activo) VALUES (null, :nombre, :correo, 1)');
		$insert->bindValue('nombre', $destinatario->getNombre());
		$insert->bindValue('correo', $destinatario->getCorreo());
		$insert->execute();
		return $insert;
	}

	public static function update($destinatario){
		$db = Db::getConnect();
		$update = $db->prepare('UPDATE notificacion_destinatario SET Nombre=:nombre, Correo=:correo WHERE ID=:id');
		$update->bindValue('nombre', $destinatario->getNombre());
		$update->bindValue('correo', $destinatario->getCorreo());
		$update->bindValue('id', $destinatario->getId());
		$update->execute();
	}

	public static function activar($id){
		$db = Db::getConnect();
		$update = $db->prepare('UPDATE notificacion_destinatario SET Activo = 1 WHERE ID=:id');
		$update->bindValue('id', $id);
		$update->execute();
	}

	public static function desactivar($id){
		$db = Db::getConnect();
		$update = $db->prepare('UPDATE notificacion_destinatario SET Activo = 0 WHERE ID=:id');
		$update->bindValue('id', $id);
		$update->execute();
	}
}
?>
