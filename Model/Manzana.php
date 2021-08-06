<?php 
/**
* 
*/
class Manzana
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
		$listaManzana = [];
		$select = $db->query('SELECT * FROM manzana order by ID');

		foreach($select->fetchAll() as $manzana){
			$listaManzana[] = new Manzana($manzana['ID'],$manzana['Descripcion']);
		}
		
		return $listaManzana;
	}


	public static function searchById($id){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM manzana WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$manzana = $select->fetch();


		$listaManzana = new Manzana($manzana['ID'],$manzana['Descripcion']);
		
		return $listaManzana;

	}

	public static function searchByIdUpdate($id){

		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM manzana WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$manzana = $select->fetch();

		$listaManzana = new Manzana($manzana['ID'],$manzana['Descripcion']);
		
		return $listaManzana;

	}

	public static function save($manzana){
		
		$db=Db::getConnect();
		$insert=$db->prepare('INSERT INTO manzana VALUES (null,:Descripcion)');
		$insert->bindValue('Descripcion',$manzana->getDescripcion());
		$insert->execute();

		return $insert;

	}

	public static function update($manzana){
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE manzana SET Descripcion=:Descripcion WHERE ID=:ID');
		$update->bindValue('Descripcion', $manzana->getDescripcion());
		$update->bindValue('ID',$manzana->getId());
		$update->execute();
	}

	public static function delete($id){
		$db=Db::getConnect();
		$delete=$db->prepare('DELETE  FROM manzana WHERE ID=:ID');
		$delete->bindValue('ID',$id);
		$delete->execute();	

		return $delete;	
	}
}

?>