<?php 
/**
* 
*/
class Destino
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
		$listaDestino = [];
		$select = $db->query('SELECT * FROM destino order by ID');

		foreach($select->fetchAll() as $destino){
			$listaDestino[] = new Destino($destino['ID'],$destino['Descripcion']);
		}
		
		return $listaDestino;
	}

	public static function searchById($id){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM destino WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$destino = $select->fetch();

		$listaDestino = new Destino($destino['ID'],$destino['Descripcion']);
		
		return $listaDestino;

	}

	public static function searchByIdUpdate($id){

		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM destino WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$destino = $select->fetch();

		$listaDestino = new Destino($destino['ID'],$destino['Descripcion']);
		
		return $listaDestino;

	}

	public static function save($destino){
		
		$db=Db::getConnect();
		$insert=$db->prepare('INSERT INTO destino VALUES (null,:Descripcion)');
		$insert->bindValue('Descripcion',$destino->getDescripcion());
		$insert->execute();

		return $insert;

	}

	public static function update($destino){
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE destino SET Descripcion=:Descripcion WHERE ID=:ID');
		$update->bindValue('Descripcion', $destino->getDescripcion());
		$update->bindValue('ID',$destino->getId());
		$update->execute();
	}

	public static function delete($id){
		$db=Db::getConnect();
		$delete=$db->prepare('DELETE  FROM destino WHERE ID=:ID');
		$delete->bindValue('ID',$id);
		$delete->execute();		

		return $delete;
	}
}

?>