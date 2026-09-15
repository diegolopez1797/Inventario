<?php
/**
*
*/
class Permiso
{
	private $Id;
	private $Codigo;
	private $Descripcion;


	function __construct($Id, $Codigo, $Descripcion)
	{
		$this->setId($Id);
		$this->setCodigo($Codigo);
		$this->setDescripcion($Descripcion);
	}

	public function getId(){
		return $this->Id;
	}

	public function setId($Id){
		$this->Id = $Id;
	}

	public function getCodigo(){
		return $this->Codigo;
	}

	public function setCodigo($Codigo){
		$this->Codigo = $Codigo;
	}

	public function getDescripcion(){
		return $this->Descripcion;
	}

	public function setDescripcion($Descripcion){
		$this->Descripcion = $Descripcion;
	}

	public static function all(){
		$db = Db::getConnect();
		$listaPermiso = [];
		$select = $db->query('SELECT * FROM permiso ORDER BY Codigo');

		foreach ($select->fetchAll() as $permiso) {
			$listaPermiso[] = new Permiso($permiso['ID'], $permiso['Codigo'], $permiso['Descripcion']);
		}

		return $listaPermiso;
	}

	// Codigos de permiso asignados a un rol (solo los codigos, para marcar checkboxes en la matriz).
	public static function codigosPorRol($rolId){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT p.Codigo FROM rol_permiso rp INNER JOIN permiso p ON rp.PermisoID = p.ID WHERE rp.RolID = :RolID');
		$select->bindValue('RolID', $rolId);
		$select->execute();

		return $select->fetchAll(PDO::FETCH_COLUMN);
	}

	// Reemplaza el conjunto de permisos asignados a un rol (usado por la pantalla de matriz).
	public static function asignarARol($rolId, array $idsPermiso){
		$db = Db::getConnect();
		$db->beginTransaction();
		try {
			$delete = $db->prepare('DELETE FROM rol_permiso WHERE RolID = :RolID');
			$delete->bindValue('RolID', $rolId);
			$delete->execute();

			$insert = $db->prepare('INSERT INTO rol_permiso (RolID, PermisoID) VALUES (:RolID, :PermisoID)');
			foreach ($idsPermiso as $permisoId) {
				$insert->bindValue('RolID', $rolId);
				$insert->bindValue('PermisoID', $permisoId);
				$insert->execute();
			}

			$db->commit();
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}
	}

	// Punto unico de verificacion de permisos. Nunca compara contra RolID ni nombre de rol directamente.
	public static function usuarioPuede($codigo, $usuario = null){
		if ($usuario === null) {
			if (!isset($_SESSION['usuario'])) {
				return false;
			}
			$usuario = $_SESSION['usuario'];
		}

		$db = Db::getConnect();
		$select = $db->prepare('SELECT COUNT(*) FROM rol_permiso rp INNER JOIN permiso p ON rp.PermisoID = p.ID WHERE rp.RolID = :RolID AND p.Codigo = :Codigo');
		$select->bindValue('RolID', $usuario->getRolId());
		$select->bindValue('Codigo', $codigo);
		$select->execute();

		return $select->fetchColumn() > 0;
	}
}

?>
