<?php
/**
*
*/
class Proveedor
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


	public static function all(){
		$db = Db::getConnect();
		$listaProveedor = [];
		$select = $db->query('SELECT * FROM proveedor order by ID');

		foreach($select->fetchAll() as $proveedor){
			$listaProveedor[] = new Proveedor($proveedor['ID'],$proveedor['Descripcion']);
		}

		return $listaProveedor;
	}

	public static function searchById($id){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM proveedor WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$proveedor = $select->fetch();

		$listaProveedor = new Proveedor($proveedor['ID'],$proveedor['Descripcion']);

		return $listaProveedor;

	}

	public static function searchByIdUpdate($id){

		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM proveedor WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$proveedor = $select->fetch();

		$listaProveedor = new Proveedor($proveedor['ID'],$proveedor['Descripcion']);

		return $listaProveedor;

	}

	public static function save($proveedor){

		$db=Db::getConnect();
		$insert=$db->prepare('INSERT INTO proveedor VALUES (null,:Descripcion)');
		$insert->bindValue('Descripcion',$proveedor->getDescripcion());
		$insert->execute();

		return $insert;

	}

	public static function update($proveedor){
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE proveedor SET Descripcion=:Descripcion WHERE ID=:ID');
		$update->bindValue('Descripcion', $proveedor->getDescripcion());
		$update->bindValue('ID',$proveedor->getId());
		$update->execute();
	}

	public static function delete($id){
		$db=Db::getConnect();
		$delete=$db->prepare('DELETE FROM proveedor WHERE ID=:ID');
		$delete->bindValue('ID',$id);
		$delete->execute();

		return $delete;
	}
}

?>
