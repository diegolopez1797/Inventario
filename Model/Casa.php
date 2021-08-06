<?php 
/**
* 
*/
class Casa
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
		$listaCasa = [];
		$select = $db->query('SELECT * FROM casa order by ID');

		foreach($select->fetchAll() as $casa){
			$listaCasa[] = new Casa($casa['ID'],$casa['Descripcion']);
		}
		
		return $listaCasa;
	}


	public static function searchById($id){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM casa WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$casa = $select->fetch();


		$listaCasa = new Casa($casa['ID'],$casa['Descripcion']);
		
		return $listaCasa;

	}

	public static function searchByIdUpdate($id){

		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM casa WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$casa = $select->fetch();

		$listaCasa = new Casa($casa['ID'],$casa['Descripcion']);
		
		return $listaCasa;

	}

	public static function save($casa){
		
		$db=Db::getConnect();
		$insert=$db->prepare('INSERT INTO casa VALUES (null,:Descripcion)');
		$insert->bindValue('Descripcion',$casa->getDescripcion());
		$insert->execute();

		return $insert;

	}

	public static function update($casa){
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE casa SET Descripcion=:Descripcion WHERE ID=:ID');
		$update->bindValue('Descripcion', $casa->getDescripcion());
		$update->bindValue('ID',$casa->getId());
		$update->execute();
	}

	public static function delete($id){
		$db=Db::getConnect();
		$delete=$db->prepare('DELETE  FROM casa WHERE ID=:ID');
		$delete->bindValue('ID',$id);
		$delete->execute();		

		return $delete;
	}
}

?>