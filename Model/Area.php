<?php 
/**
* 
*/
class Area
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
		$listaArea = [];
		$select = $db->query('SELECT * FROM area order by ID');

		foreach($select->fetchAll() as $area){
			$listaArea[] = new Area($area['ID'],$area['Descripcion']);
		}
		
		return $listaArea;
	}


	public static function searchById($id){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM area WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$area = $select->fetch();


		$listaArea = new Area($area['ID'],$area['Descripcion']);
		
		return $listaArea;

	}

	public static function searchByIdUpdate($id){

		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM Area WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$area = $select->fetch();

		$listaArea = new Area($area['ID'],$area['Descripcion']);
		
		return $listaArea;

	}

	public static function save($area){
		
		$db=Db::getConnect();
		$insert=$db->prepare('INSERT INTO area VALUES (null,:Descripcion)');
		$insert->bindValue('Descripcion',$area->getDescripcion());
		$insert->execute();

		return $insert;

	}

	public static function update($area){
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE area SET Descripcion=:Descripcion WHERE ID=:ID');
		$update->bindValue('Descripcion', $area->getDescripcion());
		$update->bindValue('ID',$area->getId());
		$update->execute();
	}

	public static function delete($id){
		$db=Db::getConnect();
		$delete=$db->prepare('DELETE  FROM area WHERE ID=:ID');
		$delete->bindValue('ID',$id);
		$delete->execute();		

		return $delete;
	}
}

?>