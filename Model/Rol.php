<?php 
/**
* 
*/
class Rol
{
	private $Id;
	private $Descripcion;

	
	function __construct($Id, $Descripcion)
	{
		$this->setId($Id);
		$this->setDescripcion($Descripcion);
		
	}

	public function getId(){
		return $this->Id;
	}

	public function setId($Id){
		$this->Id = $Id;
	}

	public function getDescripcion(){
		return $this->Descripcion;
	}

	public function setDescripcion($Descripcion){
		$this->Descripcion = $Descripcion;
	}


	public static function searchById($id){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM rol WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$rol = $select->fetch();


		$listaRol = new Rol($rol['ID'],$rol['Descripcion']);

		return $listaRol;

	}

	public static function all(){
		$db = Db::getConnect();
		$listaRol = [];
		$select = $db->query('SELECT * FROM rol ORDER BY ID');

		foreach ($select->fetchAll() as $rol) {
			$listaRol[] = new Rol($rol['ID'], $rol['Descripcion']);
		}

		return $listaRol;
	}

	public static function save($rol){
		$db = Db::getConnect();
		$insert = $db->prepare('INSERT INTO rol VALUES (null,:Descripcion)');
		$insert->bindValue('Descripcion', $rol->getDescripcion());
		$insert->execute();

		return $insert;
	}

	public static function update($rol){
		$db = Db::getConnect();
		$update = $db->prepare('UPDATE rol SET Descripcion=:Descripcion WHERE ID=:ID');
		$update->bindValue('Descripcion', $rol->getDescripcion());
		$update->bindValue('ID', $rol->getId());
		$update->execute();
	}

	public static function delete($id){
		$db = Db::getConnect();

		// rol_permiso es una tabla de asignacion (no datos historicos): se limpia antes de
		// intentar el borrado para que no bloquee por su propia FK. Si el rol todavia tiene
		// usuarios asociados, el DELETE de abajo fallara por la FK usuario->rol y sera
		// capturado por el llamador, que es el comportamiento deseado.
		$deletePermisos = $db->prepare('DELETE FROM rol_permiso WHERE RolID=:RolID');
		$deletePermisos->bindValue('RolID', $id);
		$deletePermisos->execute();

		$delete = $db->prepare('DELETE FROM rol WHERE ID=:ID');
		$delete->bindValue('ID', $id);
		$delete->execute();

		return $delete;
	}
}

?>